<?php
/**
 * @copyright 2023 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web;

class Open311Gateway
{
    private $url;
    private $jurisdiction_id;
    private $api_key;


    public function __construct(array $config)
    {
        $this->url             = $config['url'];
        $this->jurisdiction_id = $config['jurisdiction_id'];
        $this->api_key         = $config['api_key'];
    }

	public function getServiceGroups(): array
	{
		$groups      = [];
		$services    = $this->getServiceList();
		$hasFeatured = false;

		if ($services) {
			foreach ($services as $s) {
				$code = self::slug($s['group']);
				if (!array_key_exists($code, $groups)) {
					$groups[$code] = $s['group'];
				}
			}
		}
		return $groups;
	}

	private function slug(string $string): string
	{
		$slug    = strtolower($string);
		$regex   = ['/\&/' , '/[^a-z\-\s]/', '/\s+/'];
		$replace = ['and',       '',           '-' ];
		return preg_replace($regex, $replace, strtolower($string));
	}

	public function getServiceList(): array
	{
        static $services = [];

		if (!$services) {
			$url  = "{$this->url}/services.json?jurisdiction_id={$this->jurisdiction_id}&api_key={$this->api_key}";
			$json = $this->queryServer($url);
			if ($json) {
				$services = $json;
			}
		}
		return $services;
	}

	/**
	 * Returns only the services matching the given group
	 *
	 * @return array An array of Service information
	 */
	public function getGroupServices(string $group): array
	{
		$out = [];

		foreach ($this->getServiceList() as $s) {
            if ($group === $s['group'])        { $out[] = $s; }
            if ($group ===   'featured'
                && !empty($s['featured'])) { $out[] = $s; }
		}
		return $out;
	}

	/**
	 * Returns a single service entry from GET Service List
	 */
	public function getService(string $service_code): ?array
	{
		foreach ($this->getServiceList() as $s) {
			if ($s['service_code'] == $service_code) {
				return $s;
			}
		}
		return null;
	}

	/**
	 * Returns the result from GET Service Definition for a single service
	 */
	public function getServiceDefinition(string $service_code): ?array
	{
        static $defs = [];

		if (!array_key_exists($service_code, $defs)) {
			$url = "{$this->url}/services/$service_code.json?jurisdiction_id={$this->jurisdiction_id}&api_key={$this->api_key}";
			$d   = $this->queryServer($url);
			if ($d) { $defs[$service_code] = $d; }
		}

		return isset($defs[$service_code])
                   ? $defs[$service_code]
                   : null;
	}

	public function getServiceRequest(int $service_request_id): array
	{
		return $this->queryServer("{$this->url}/requests/$service_request_id.json");
	}

	/**
	 * @param  array    $post   $_POST array
	 * @param  array    $file   $_FILES entry for file upload
	 * @return array    JSON response from Open311 server
	 */
	public function postServiceRequest(array $post, array $file=null): array
	{
		$request = [
			'jurisdiction_id' => $this->jurisdiction_id,
			'api_key'         => $this->api_key,
			'service_code'    => $post['service_code']
		];
		$open311Fields = ['address_string', 'lat', 'long', 'description',
		                  'first_name', 'last_name', 'phone', 'email'];
		foreach ($open311Fields as $f) {
			if (!empty($post[$f])) { $request[$f] = $post[$f]; }
		}

		$def = $this->getServiceDefinition($post['service_code']);
		if ($def && !empty($def['attributes'])) {
			foreach ($def['attributes'] as $a) {
				$code = $a['code'];
				if (!empty($post[$code])) { $request['attribute'][$code] = $post[$code]; }
			}
		}
		if (!empty($file['name']) && !empty($file['tmp_name'])) {
            $request['media'] = new \CURLFile($file['tmp_name'], $file['type'], $file['name']);
		}
		$open311 = curl_init("{$this->url}/requests.json");
		curl_setopt_array($open311, [
			CURLOPT_POST           => true,
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS     => $this->flatten_request_array($request),
			CURLOPT_SSL_VERIFYPEER => false
		]);
		$response = curl_exec($open311);
		if (!$response) {
			throw new \Exception(curl_error($open311));
		}
		$json = json_decode($response, true);
		if (!$json) {
			echo "------Error from Open311 server----------\n";
			echo $response;
			exit();
		}
		else {
			return $json;
		}
		return [];
	}

	/**
	 * Creates a curl fields array from a POST array
	 *
	 * Curl does not allow multidimensional arrays.
	 * Instead, you must flatten the multidimensional arrays into
	 * simple fieldname strings
	 */
	private function flatten_request_array(array $request, string $prefix=null): array
	{
		$out = [];
		foreach ($request as $key=>$value) {
			if (!is_array($value)) {
				if ($prefix) { $out[$prefix."[$key]"] = $value; }
				else         { $out[$key]             = $value; }
			}
			else {
				$out = array_merge(
					$out,
					$this->flatten_request_array($value, $prefix ? $prefix."[$key]" : $key)
				);
			}
		}
		return $out;
	}

	private function queryServer(string $url): array
	{
		$file = Url::get($url);
		if ($file) {
            $json = json_decode($file, true);
			if ($json) {
				return $json;
			}
			else {
				throw new \Exception('endpoints/invalidJSON');
			}
		}
		else {
			throw new \Exception('endpoints/open311ServerUnReachable');
		}
		return [];
	}
}
