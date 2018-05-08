<?php

namespace App\Contracts\Account\Registration;

interface Activator
{
    public function getActivateCode();
    public function activate(int $memberId, string $code): bool;
}
