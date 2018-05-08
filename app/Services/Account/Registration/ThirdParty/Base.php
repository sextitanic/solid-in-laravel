<?php

namespace App\Services\Account\Registration\ThirdParty;

use App\Services\Account\Registration\BaseRegistration;
use App\Contracts\Account\Registration\ThirdPartyInfo as ThirdPartyInfoContract;

class Base extends BaseRegistration implements ThirdPartyInfo
{
    /**
     * 檢查不同註冊狀態的共用傳入參數是否正確
     *
     * @param array $input[
     *      @var string $token  第三方登入的 AccessToken
     *      @var int    $userId 第三方登入的會員 ID
     * ]
     * @return boolean
     */
    protected function validate(array $input): bool
    {
        parent::validate($input);

        $rules['token'] = 'required|string';
        $rules['userId'] = 'required|string';

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new \App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }
}
