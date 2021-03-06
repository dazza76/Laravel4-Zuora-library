Laravel4-Zuora-library
===========================

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

## Configuration

Run `php artisan config:publish dazza76/zuora` to publish the package config file.
Run `php artisan asset:publish dazza76/zuora` to publish the public wsql file.
Add your username, password and path to your file which can be obtained from your Zuora and select an endpoint to connect to 

```php
Config::set('zuora::connections.runtime', array(
			'wsdl' => 'zuora.a.54.0.wsdl',
                        'username' => '',
                        'password' => '',
                        //'endpoint' => 'https://www.zuora.com/apps/services/a/54.0'
                         'endpoint' => 'https://apisandbox.zuora.com/apps/services/a/54.0'
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
Zuora::connection('runtime')->queryall(...);
```


Basic usage examples
------------
```php
// With custom connection
$listing = Zuora::connection('my-Zuora-connection')->queryall(...);

// with default connection
$listing = Zuora::connection()->queryall(...);
```

