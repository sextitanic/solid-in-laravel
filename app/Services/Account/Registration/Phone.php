<?php

namespace App\Services\Account\Registration;

use Log;
use App\Services\Account\Registration\Native\Base;

class Phone extends Base
{
    protected $type = 2;

    protected function validate(array $input): bool
    {
        parent::validate($input);
        
        $rules['account'] = 'required|regex:/^09\d{8}$/|unique:member,reg_phone';

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new \App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }

    public function register(array $data): int
    {
        $this->validate($data);

        $nowDate = date('Y-m-d H:i:s');

        $insertData = [
            'sex' => $data['sex'] ?? null,
            'type' => $this->type,
            'reg_date' => $nowDate,
            'updated_at' => $nowDate,
            'password' => $data['password'],
            'reg_phone' => $data['account']
        ];

        $memberId = $this->member->create($insertData);

        return $memberId;
    }

    public function getActivateCode(): string
    {
        return rand(1000, 9000);
    }

    /**
     * 寄送 email 驗證通知信
     *
     * @param array $data[
     *      @var string $email 會員 email
     *      @var int    $sex   性別
     *      @var string $code  驗證啟用碼
     * ]
     * @return boolean
     */
    public function notify(array $data): bool
    {
        $post = [
            'phone' => $data['account'],
            'content' => view('notification.sms.account.registration', $data)->render()
        ];

        $result = $notify->post('/sms', $post);

        if ($result['status'] != 200) {
            throw new \App\Exceptions\ApiException('寄送 ' . $data['account'] . ' 簡訊失敗');
        }

        return true;
    }
}
