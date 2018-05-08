<?php

namespace App\Contracts\Account\Registration;

interface Notify
{
    public function notify(array $data): bool;
}
