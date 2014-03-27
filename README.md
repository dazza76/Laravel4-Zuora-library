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

Finally, add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
	// ...

	'Dazza76\Zuora\ZuoraServiceProvider'
)
```

## Configuration

Run `php artisan config:publish dazza76/zuora` to publish the package config file. Add your username, password, security token(optional) and the absolute path to your enterprise/partner WSDL file which can be obtained from your Zuora Org.

## Example
```php
Zuora::query('select Name  from Account limit 5');

```
