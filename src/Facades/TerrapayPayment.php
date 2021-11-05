<?php

namespace FlignoDevs\TerrapayPayment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TerrapayPayment
 * @package FlignoDevs\TerrapayPayment
 *
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
 * @since 2021-11-04
 */

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
