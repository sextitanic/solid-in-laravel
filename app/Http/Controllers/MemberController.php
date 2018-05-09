<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberCreatePost;
use App\Services\Account\Registration\RegistrationFactory;
use Log;
use DB;

class MemberController extends Controller
{
    /**
     * 會員註冊，用 url 路徑來分是用 email 或手機註冊
     *
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function register(MemberCreatePost $request, string $type)
    {
        try {
            // 依照傳進來的路徑來呼叫要使用的物件
            $account = RegistrationFactory::create($type);
            
            DB::beginTransaction();
            // 會員註冊
            $memberId = $account->register($request->input());
            
            // 如果順利註冊，就取得啟用驗證碼
            $activateCode = $account->getActivateCode();
            
            // 寫入會員啟用資料表
            $account->activate($memberId, $activateCode);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($e instanceof \App\Exceptions\InvalidParameterException) {
                return $this->response(422, $e->getMessage());
            }

            return $this->response(500, $e->getMessage());
        }

        try {
            // 把啟用驗證碼 merge 進 Request 的物件裡
            $request->merge([
                'code' => $activateCode
            ]);

            $sendResult = $account->notify($request->input());
        } catch (\Throwable $e) {
            Log::info('傳送驗證通知失敗' . $e->getMessage());
        }
        
        return $this->response(200, '正常執行');
    }

    /**
     * 會員註冊，用第三方註冊方式
     * Facebook、Google Plus etc.
     *
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function thirdPartyRegister(Request $request, string $type)
    {
        // 依照傳進來的路徑來呼叫要使用的物件
        $account = RegistrationFactory::create($type);

        try {
            DB::beginTransaction();
            // 會員註冊
            $memberId = $account->register($request->input('userId'));
            
            // 寫入第三方註冊會員資訊
            $account->insertThirdPartyInfo($memberId, $request->input());

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($e instanceof \App\Exceptions\InvalidParameterException) {
                return $this->response(422, $e->getMessage());
            }

            return $this->response(500, $e->getMessage());
        }
        
        return $this->response(200, '正常執行');
    }

    private function response(int $code, string $message, array $data = [])
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
