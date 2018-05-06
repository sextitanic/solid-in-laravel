<?php

namespace App\Services\Account\Registration;

use DB;

abstract class BaseRegistration
{
    protected $type;

    /**
     * 檢查不同註冊狀態的共用傳入參數是否正確
     *
     * @param array $input[
     *      @var string $password 密碼
     *      @var int    $sex      性別
     * ]
     * @return boolean
     */
    protected function validate(array $input): bool
    {
        $input['type'] = $this->type;

        $rules = [
            'password' => 'required|string|min:8|max:16', // 密碼必填，且字數在 8 ~ 16 之間
            'sex' => 'nullable|in:1,2',
            'type' => 'required|integer'
        ];

        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
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
        $insertData = [
            'member_id' => $memberId,
            'code' => $code,
            'type' => $this->type,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $result = DB::table('member_activator')->insert($insertData);
    
            if ($result !== true) {
                DB::rollBack();
                throw new \Exception('會員啟用驗證碼新增失敗');
            }
        } catch (\Exception $e) {
            Log::error('新增會員驗證資料失敗：' . $e->getMessage());
            throw new \App\Exceptions\DatabaseQueryException('新增會員驗證資料失敗', 500, $e);
        }

        return true;
    }

    abstract public function register(array $data): int; // 寫入會員資料表
    abstract public function getActivateCode(): string; // 取得啟用驗證碼
    abstract public function notify(array $data): bool; // 發送通知
}
