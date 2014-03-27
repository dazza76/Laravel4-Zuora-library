Laravel4-Zuora-library
===========================
This is be broken

## Installation

Install this package through Composer. To your `composer.json` file, add:

```js
"require-dev": {
	"Dazza76/Zuora": "dev-master"
}
```

Next, run `composer update` to download it.

add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
	// ...

	'Dazza76\Zuora\ZuoraServiceProvider'
)
```

Finally create the config files and publish the public folder
php artisan config:publish dazza76/zuora
php artisan asset:publish dazza76/zuora


## Configuration

Run `php artisan config:publish dazza76/zuora` to publish the package config file. Add your username, password, security token(optional) and the absolute path to your enterprise/partner WSDL file which can be obtained from your Zuora Org.

```php
Config::set('zuora::connections.key', array(
           'host'   => '',
           'username' => '',
           'password'   => '',
           'passive'   => false,
));
```


Accessing connections
---------------------
You can access default Zuora connection via the `Zuora::connection` method:
```php
Zuora::connection()->queryall(...);
```

When using multiple connections you can access each specific Zuora connection by passing connection name:
```php
Zuora::connection('foo')->queryall(...);
```

If you need to disconnect from a given Zuora use the disconnect method:
```php
Zuora::disconnect('foo');
```

Basic usage examples
------------
```php
// With custom connection
$listing = Zuora::connection('my-Zuora-connection')->queryall(...);

// with default connection
$listing = Zuora::connection()->queryall(...);
```

