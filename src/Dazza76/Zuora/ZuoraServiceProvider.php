<?php
/**
 * ZuoraServiceProvider.php
 *
 * @package default
 */


namespace Dazza76\Zuora;

use Illuminate\Support\ServiceProvider;
//use Dazza76\zuora\zuora-api\API.php;
require_once 'zuora-api/API.php';
//require_once 'Zuora_API.php';
//use Dazza76\Zuora\lib;
use lib;
class ZuoraServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package('dazza76/zuora');
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->app['zuora'] = $this->app->share(function($app) {
				return new ZuoraManager($app);
			});
		$this->app->booting(function() {
				$loader = \Illuminate\Foundation\AliasLoader::getInstance();
				$loader->alias('Zuora', 'Dazza76\Zuora\Facades\Zuora');
				$loader->alias('Zuora_API', 'Dazza76\Zuora\zuora-api\API');
			});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('zuora');
	}


}
