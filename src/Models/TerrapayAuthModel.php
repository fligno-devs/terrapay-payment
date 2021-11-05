<?php

namespace FlignoDevs\TerrapayPayment\Models;

class TerrapayAuthModel extends BaseModel
{
    /**
     * Either OK or ERROR
     * @var string
     */
    public $status;

    /**
     * For auth verification, here are the following statuses:
     * Auth success - API key is valid. HTTP - 200
     * Auth expired - API key has expired HTTP - 400
     * Auth invalid - API key is incorrect. HTTP - 401
     * AUTH_KEY_MISSING - API Key is missing HTTP - 400
     *
     * For auth modification, here are the following statuses:
     * Auth success - API key modified successfully. HTTP - 200
     * Auth expired - Old API key expired. HTTP - 400
     * Auth invalid - Old API key is wrong or the New API key format is not correct. - HTTP - 401
     * AUTH_KEY_MISSING - API Key is missing HTTP - 400
     *
     * @var string
     */
    public $subStatus;

    /**
     * The number of days left for the API key to expire.
     * The partner has to renew the API key before expiry
     *
     * @var int
     */
    public $expiryDays;

    /**
     * Only available during modifyAuth API call
     * @var string
     */
    public $newApiKey;
}
