<?php namespace Basset;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor() : string { return 'basset'; }

    /**
     * Serve a collection or number of collections.
     */
    public static function show() : string {
    	return basset_assets(func_get_args());
    }

}