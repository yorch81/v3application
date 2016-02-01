# V3ctor WareHouse Web Application #

## Description ##
V3ctorWH is a Web Application REST API for V3 WareHouse Core.

## Requirements ##
* [PHP 5.4.1 or higher](http://www.php.net/)
* [MongoDb](http://www.mongodb.org/)
* [MySQL](https://www.mysql.com/)
* [Slim Framework](http://www.slimframework.com/)
* [V3Wh Core](https://github.com/yorch81/v3wh)

## Installation ##
Clone Repository
Execute php composer.phar install

Create config.php
~~~

$hostname = 'DB_HOST';
$username = 'DB_USER';
$password = 'DB_PASSWORD';
$dbname   = 'DBNAME';
$port     = 27017;
$key      = "KEY";

~~~

## Examples ##
~~~

<?php
require 'config.php';
require 'vendor/autoload.php';

new V3Application(V3Application::MONGODB, $hostname, $username, $password, $dbname, $port, $key);
?>

~~~

## References ##
https://en.wikipedia.org/wiki/Representational_state_transfer

P.D. Let's go play !!!




