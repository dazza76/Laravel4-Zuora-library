<?php namespace Dazza76\Zuora\Facades;

use Illuminate\Support\Facades\Facade;

class Zuora extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'zuora'; }

}
