<?php

namespace App\Services\Account\Registration;

use DB;
use App\Repositories\MemberRepository;

abstract class BaseRegistration
{
    protected $type;
    protected $member;
    protected $activator;

    public function __construct()
    {
        $this->member = new MemberRepository();
    }

    protected function validate(array $input): bool
    {
        $input['type'] = $this->type;

        $rules = [
            'type' => 'required|integer'
        ];

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }

    abstract public function register(array $data): int; // 寫入會員資料表
}
