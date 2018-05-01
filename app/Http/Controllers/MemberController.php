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
        $class = 'App\Services\Account\Registration\\' . ucfirst($type);
        if (class_exists($class) === false) {
            return $this->response(422, 'Class ' . $class . ' Not exist.');
        }
        
        $account = new $class();

        try {
            DB::beginTransaction();
    
            $memberId = $account->register($request->input());
            
            if (is_int($memberId)) {
                $activateCode = $account->getActivateCode();
            } else {
                Log::error('新增會員失敗：' . $e->getMessage());
                throw new \Exception('新增會員失敗');
            }
    
            $account->activate($memberId, $activateCode);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->response(500, $e->getMessage());
        }

        try {
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
