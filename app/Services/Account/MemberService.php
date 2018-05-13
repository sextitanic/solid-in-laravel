<?php

namespace App\Services\Account;

use DB;
use App\Services\Account\Registration\Native\Base as BaseNative;
use Log;

class MemberService
{
    /**
     * 會員註冊
     *
     * @param BaseNative $account 註冊的物件
     * @param array $input [
     *      @var string account  要註冊的帳號
     *      @var string password 密碼
     *      @var int    sex      性別
     * ]
     * @return boolean
     */
    public function register(BaseNative $account, array $input): bool
    {
        try {
            DB::beginTransaction();
            // 會員註冊
            $memberId = $account->register($input);
            
            // 如果順利註冊，就取得啟用驗證碼
            $activateCode = $account->getActivateCode();
            
            // 寫入會員啟用資料表
            $account->activate($memberId, $activateCode);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw new $e($e->getMessage());
        }

        try {
            // 新增驗證啟用碼
            $input['code'] = $activateCode;

            // 寄送通知
            $sendResult = $account->notify($input);
        } catch (\Throwable $e) {
            Log::info('傳送驗證通知失敗' . $e->getMessage());
            throw new \App\Exceptions\NotifyException('傳送驗證通知失敗' . $e->getMessage());
        }

        return true;
    }

    public function forgotPassword()
    {
        // 沒有要做喔
    }

    public function modifyPassword()
    {
        // 沒有要做喔
    }
}
