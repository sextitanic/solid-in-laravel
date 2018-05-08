<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hash;

class Member extends Model
{
    const CREATED_AT = 'reg_date';

    protected $table = 'member';

    public function setRegEmailAttribute(string $email = null)
    {
        if (empty($email) === false) {
            $this->attributes['reg_email'] = strtolower($email);
        }
    }

    public function setPasswordAttribute(string $password = null)
    {
        $this->attributes['password'] = Hash::make($password);
    }
}
