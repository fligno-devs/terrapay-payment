<?php

use FlignoDevs\TerrapayPayment\TerrapayPayment;

if (! function_exists('terrapay_payment')) {

    /**
     * @return TerrapayPayment
     */
    function terrapay_payment(): TerrapayPayment
    {
        return resolve('terrapay-payment');
    }
}
