<?php

namespace FlignoDevs\TerrapayPayment\Facades;

use Illuminate\Support\Facades\Facade;

class TerrapayPayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'terrapay-payment';
    }
}
