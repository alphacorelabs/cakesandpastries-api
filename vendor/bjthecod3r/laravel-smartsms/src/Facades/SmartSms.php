<?php

namespace BJTheCod3r\SmartSms\Facades;

use Illuminate\Support\Facades\Facade;

class SmartSms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'smartsms';
    }
}
