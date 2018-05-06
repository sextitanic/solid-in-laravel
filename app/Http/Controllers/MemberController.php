<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * 會員註冊，用 url 路徑來分是用 email 或手機註冊
     *
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function register(Request $request, string $type)
    {
        // 依照傳進來的路徑來呼叫要使用的物件
        $class = 'App\Services\Account\Registration\\' . ucfirst($type);
        if (class_exists($class) === false) {
            return $this->response(422, 'Class ' . $class . ' Not exist.');
        }
        $account = new $class();

        try {
            DB::beginTransaction();
            // 會員註冊
            $memberId = $account->register($request->input());
            
            // 如果順利註冊，就取得啟用驗證碼
            if (is_int($memberId)) {
                $activateCode = $account->getActivateCode();
            } else {
                Log::error('新增會員失敗：' . $e->getMessage());
                throw new \Exception('新增會員失敗');
            }
            // 寫入會員啟用資料表
            $account->activate($memberId, $activateCode);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
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

    private function response(int $code, string $message, array $data = [])
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
