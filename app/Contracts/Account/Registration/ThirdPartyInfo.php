<?php

namespace App\Contracts\Account\Registration;

interface ThirdPartyInfo
{
    public function insertThirdPartyInfo(array $data): bool;
}
