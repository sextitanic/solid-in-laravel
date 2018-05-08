<?php

namespace App\Services\Account\Registration;

class Facebook extends BaseRegistration
{
    protected $type = 3;

    protected function validate(array $input): bool
    {
        $rules['token'] = 'required|string';
        $rules['userId'] = 'required|string';

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new \App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }

    /**
     * 新增會員 facebook 註冊資料
     *
     * @param array $data[
     *      @var string $token  存取 facebook 的 token
     *      @var string $userId facebook 的 user id
     * ]
     * @return integer
     */
    public function register(array $data): int
    {
        $this->validate($data);

        // 範例這邊先偷懶不實作寫入資料表的功能

        return 100; // 固定回傳一個值符合父類別回傳的參數型態
    }

    /**
     * 寫入資訊到 member_thirdparty 資料表
     *
     * @return void
     */
    public function insertThirdPartyInfo(array $data): bool
    {
        // 這邊我也懶得實作啦，直接回傳 true 符合規範

        return true;
    }
}
