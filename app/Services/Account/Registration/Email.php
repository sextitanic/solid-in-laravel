<?php

namespace App\Services\Account\Registration;

use DB;
use Hash;
use Log;
use App\Services\Api\Internal\NotifyApi;

class Email extends BaseRegistration
{
    protected $type = 1;

    protected function validate(array $input): bool
    {
        parent::validate($input);

        $rules['account'] = 'required|email|unique:member,reg_email'; // 符合 email 格式且之前不存在 member 資料表內

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new \App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }

    /**
     * 新增會員 email 註冊資料
     *
     * @param array $data[
     *      @var int    $sex      性別
     *      @var string $password 密碼
     *      @var string $account  email
     * ]
     * @return integer
     */
    public function register(array $data): int
    {
        $this->validate($data);

        $nowDate = date('Y-m-d H:i:s');

        $insertData = [
            'sex' => $data['sex'] ?? null,
            'type' => $this->type,
            'reg_date' => $nowDate,
            'updated_at' => $nowDate,
            'password' => Hash::make($data['password']),
            'reg_email' => $data['account']
        ];

        try {
            $memberId = DB::table('member')->insertGetId($insertData);
        } catch (\Exception $e) {
            Log::error('新增會員資料失敗：' . $e->getMessage());
            throw new \App\Exceptions\DatabaseQueryException('新增會員資料失敗：' . $data['account'], 500, $e);
        }

        return $memberId;
    }

    /**
     * 取得啟用驗證碼
     *
     * @return string
     */
    public function getActivateCode(): string
    {
        return str_random(64);
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
        $notify = new NotifyApi();

        $post = [
            'email' => $data['account'],
            'from' => 'service@test.com',
            'title' => '歡迎您成為 XXXX 網站的會員',
            'body' => view('notification.email.account.registration', $data)->render()
        ];

        $result = $notify->post('/email', $post);
        
        if ($result['status'] != 200) {
            throw new \App\Exceptions\ApiException('寄送 email: ' . $data['account'] . ' 失敗');
        }

        return true;
    }
}
