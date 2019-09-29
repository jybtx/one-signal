<?php

namespace Jybtx\OneSignal\Faceds;

use Illuminate\Support\Facades\Facade;

class OneSignalFacade extends Facade
{

	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() 
    {
        return 'OneSignal';
    }
}