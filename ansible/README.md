Ansible
======================
This playbook installs the uReport Client application.

This assumes some familiarity with the Ansible configuration management system and that you have an ansible control machine configured.  Before running the playbook, you must first have a tarball of the application that was previously built.  There is a Makefile in the project root.

Dependencies
-------------
This playbook requires a few roles inside of a roles directory.
You can probably use ansible-galaxy to download them:

    ansible-galaxy install --roles-path ./roles -r roles.yml

or clone them from Github:
```
git clone https://github.com/City-of-Bloomington/ansible-role-linux.git  ./roles/City-of-Bloomington.linux
git clone https://github.com/City-of-Bloomington/ansible-role-apache.git ./roles/City-of-Bloomington.apache
git clone https://github.com/City-of-Bloomington/ansible-role-php.git    ./roles/City-of-Bloomington.php
etc
```

Variables
--------------
You'll need to set these variables somewhere in your inventory.

```yml
ureport_client_archive_path: "../build/ureport-client.tar.gz"
ureport_client_install_path: "/srv/sites/ureport-client"
ureport_client_backup_path:  "/srv/backups/ureport-client"
ureport_client_site_home:    "/srv/data/ureport-client"

ureport_client_base_uri: "/ureport"
ureport_client_base_url: "https://{{ ansible_host }}{{ ureport_client_base_uri }}"

ureport_client_google_api_key:    "secret key"
ureport_client_max_image_size:    "10M"

ureport_client_graylog:
  domain: "graylog.example.gov"
  port:   "12200"

ureport_client_open311:
  url:          'https://crm.example.gov/open311/v2'
  jurisdiction: 'crm.example.gov'
  api_key:      'secret key'
```

Run the Playbook
-----------------

    ansible-playbook deploy.yml -i /path/to/inventory --limit=host.example.com

License
-------

Copyright (c) 2016-2024 City of Bloomington, Indiana

This material is avialable under the GNU Affero General Public License (GLP):
https://www.gnu.org/licenses/agpl-3.0.txt
