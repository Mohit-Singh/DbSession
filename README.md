DbSession
=========

Store Session in database in ZF2.

How To Use

This a very simple module to store all your session data in your database. 
for this add the following code in you composer.json

```php
 "require": {
  "MohitOssCube/DbSession" : "dev-master"
  }

```
after this run the following command

```php
>php composer.phar install

```


now create a table using the following schema.

```php
CREATE TABLE `session` (
  `id` char(32) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

Now check the update the following database requirement in the config

```php
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=db_name;host=localhost',
        'username'       => 'user_name',
        'password'       => 'xxxxxxx', 
        'host'           => 'localhost',
        'dbname'         => 'db_name',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
```
if you already define all these value in you global config please remove them.

now the module is ready to use. you can set the setting using following config values

```php
'session' => array(
        'remember_me_seconds' => 2419200,
        'use_cookies'       => true,
        'cookie_httponly'   => true,
        'cookie_lifetime'   => 2419200,
        'gc_maxlifetime'    => 2419200,
    
    ),

```

Remember if you set "use_cookies" to "false" then every time application will create a new session in database and your session data will removed.


