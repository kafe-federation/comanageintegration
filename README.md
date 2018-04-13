# Comanage Integration module

The Comanage Integration module collects attributes from Comanage Database by using SQL Query. 
It is implemented as an Authentication Processing Filter for [SimpleSAMLphp](https://simplesamlphp.org).

The configuration of the module is in the `authproc.sp` section in `config/config.php`.

  * [Read more about processing filters in simpleSAMLphp](simplesamlphp-authproc)

## Install

You can install the module by cloning from git.

```bash
cd /path/to/simplesamlphp/modules
git clone https://github.com/kafe-federation/comanageintegration
```

## How to setup the comanageintegration module

`db` option is required for this module. follow example below.

Example:

```php
// /path/to/simplesamlphp/config/config.php file

'authproc.idp' => array(
    59 => array(
        'class' => 'comanageintegration:integration',
        'db' => array(
            'host' => 'mysql:host=127.0.0.1;dbname=registry',
            'user' => 'comanage_mysql_user',
            'password' => 'comanage_mysql_password'
        ),
        'nameId' => 'urn:oid:...', // optional, default eppn
    ),
),
```

