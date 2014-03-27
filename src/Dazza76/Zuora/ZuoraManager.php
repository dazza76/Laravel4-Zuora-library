<?php
/**
 * ZuoraManager.php
 *
 * @package default
 */


namespace Dazza76\Zuora;

class ZuoraManager {

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Create a new ZUORA instance.
	 *
	 * @return void
	 * @param \Illuminate\Foundation\Application $app
	 */
	public function __construct($app) {
		$this->app = $app;
	}


	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection() {
		return $this->app['config']['zuora::default'];
	}


	/**
	 * Get the configuration for a connection.
	 *
	 *
	 * @throws \InvalidArgumentException
	 * @param string  $name
	 * @return array
	 */
	protected function getConfig($name) {
		$name = $name ?: $this->getDefaultConnection();

		// To get the zuora connection configuration, we will just pull each of the
		// connection configurations and get the configurations for the given name.
		// If the configuration doesn't exist, we'll throw an exception and bail.
		$connections = $this->app['config']['zuora::connections'];

		if (is_null($config = array_get($connections, $name))) {
			throw new \InvalidArgumentException("Zuora [$name] not configured.");
		}

		return $config;
	}


	/**
	 * Make the ZUORA connection instance.
	 *
	 * @param string  $name
	 * @return \Dazza76\Zuora\Zuora
	 */
	protected function makeConnection($name) {
		$config = $this->getConfig($name);
		return new Zuora($config);
	}


	/**
	 * Get a ZUORA connection instance.
	 *
	 * @param string  $name
	 * @return unknown
	 */
	public function connection($name = null) {
		$name = $name ?: $this->getDefaultConnection();

		// If we haven't created this connection, we'll create it based on the config
		// provided in the application.
		if ( ! isset($this->connections[$name])) {
			$this->connections[$name] = $this->makeConnection($name);
		}

		return $this->connections[$name];
	}


	/**
	 * Disconnect from the given zuora.
	 *
	 * @return void
	 * @param string  $name
	 */
	public function disconnect($name = null) {
		$name = $name ?: $this->getDefaultConnection();

		if ($this->connections[$name]) {
			$this->connections[$name]->disconnect();
			unset($this->connections[$name]);
		}
	}


	/**
	 * Return all of the created connections.
	 *
	 * @return array
	 */
	public function getConnections() {
		return $this->connections;
	}


}
