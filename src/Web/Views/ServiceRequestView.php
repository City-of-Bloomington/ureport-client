<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Web\Views;

use Web\View;

class ServiceRequestView extends View
{
    public function __construct(array $service, array $definition, string $group_code)
    {
        parent::__construct();

        $accept = '.jpg,.png';
        list($maxSize, $maxBytes) = self::maxUpload();

        $this->vars = [
            'service'             => $service,
            'attributes'          => $definition['attributes'] ?? null,
            'group_code'          => $group_code,
            'google_maps_api_key' => GOOGLE_MAPS_API_KEY,
            'recaptcha_site_key'  => RECAPTCHA_SITE_KEY,
            'default_latitude'    => ini_get('date.default_latitude'),
            'default_longitude'   => ini_get('date.default_longitude'),
            'first_name'          => $_SESSION['first_name'] ?? '',
            'last_name'           => $_SESSION['last_name' ] ?? '',
            'email'               => $_SESSION['email'     ] ?? '',
            'phone'               => $_SESSION['phone'     ] ?? '',
            'description'         => $_POST['description'   ] ?? '',
            'address_string'      => $_POST['address_string'] ?? '',
            'lat'                 => !empty($_POST['lat' ]) ? (float)$_POST['lat' ] : '',
            'long'                => !empty($_POST['long']) ? (float)$_POST['long'] : '',
            'accept'              => $accept,
            'maxBytes'            => $maxBytes,
            'maxSize'             => $maxSize,
            'media_help'          => "Accepted file types: $accept<br />An error will be thrown if the selected file is greater than $maxSize"
        ];
        if (isset($_SESSION['errorMessages'])) {
            $this->vars['errorMessages'] = $_SESSION['errorMessages'];
            unset($_SESSION['errorMessages']);
        }
    }

    public function render(): string
    {
        return $this->twig->render("{$this->outputFormat}/requestForm.twig", $this->vars);
    }

    /**
     * Return the max size upload allowed in PHP ini
     *
     * This returns both a human readable size string as well as the raw
     * number of bytes.
     */
    public static function maxUpload(): array
    {
        $upload_max_size  = ini_get('upload_max_filesize');
        $post_max_size    = ini_get('post_max_size');
        $upload_max_bytes = self::bytes($upload_max_size);
        $post_max_bytes   = self::bytes(  $post_max_size);

        if ($upload_max_bytes < $post_max_bytes) {
            $maxSize  = $upload_max_size.'B';
            $maxBytes = $upload_max_bytes;
        }
        else {
            $maxSize  = $post_max_size.'B';
            $maxBytes = $post_max_bytes;
        }
        return [$maxSize, $maxBytes];
    }

    public static function bytes(string $size): int
    {
        switch (substr($size, -1)) {
            case 'M': return (int)$size * 1048576;
            case 'K': return (int)$size * 1024;
            case 'G': return (int)$size * 1073741824;
            default:  return (int)$size;
        }
    }
}
