<?php

namespace App\Services\Api\Internal;

use App\Services\Api\BaseApi;

class NotifyApi extends BaseApi
{
    public function __construct()
    {
        parent::__construct();
        $this->host = env('NOTIFY_API');
    }
}
