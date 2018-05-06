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

    public function getActivateCode(): string
    {
        // facebook 註冊不需要寄送啟用驗證通知，直接回傳空字串

        return '';
    }

    /**
     * 寫入會員驗證啟用資料表
     *
     * @param integer $memberId 會員 ID
     * @param string  $code     啟用碼
     * @return boolean
     */
    public function activate(int $memberId, string $code): bool
    {
        // facebook 註冊不需要寄送啟用驗證通知，直接回傳 true

        return true;
    }

    public function notify(array $data)
    {
        // facebook 註冊不需要寄送啟用驗證通知，直接回傳 true

        return true;
    }
}
