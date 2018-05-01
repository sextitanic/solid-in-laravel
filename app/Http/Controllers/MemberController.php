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
        $nowDate = date('Y-m-d H:i:s');

        $rules = [
            'password' => 'required|string|min:8|max:16',
            'sex' => 'nullable|in:1,2,3'
        ];

        $reg_type = '';

        switch ($type) {
            case 'email':
                $rules['account'] = 'required|email';
                $reg_type = 1;
                break;
            case 'phone':
                $rules['account'] = 'required|regex:/^09\d{8}$/';
                $reg_type = 2;
                break;
            default:
                throw new \Exception('沒有相對應註冊方式', 422);
        }

        $validator = validator($request->input(), $rules);
        if ($validator->fails()) {
            return $this->response(422, $validator->errors()->first());
        }

        try {
            $insertData = [
                'sex' => $request->input('sex'),
                'type' => $reg_type,
                'reg_date' => $nowDate,
                'updated_at' => $nowDate,
                'password' => Hash::make($request->input('password'))
            ];
    
            if ($type === 'email') {
                $insertData['reg_email'] = $request->input('account');
            } elseif ($type === 'phone') {
                $insertData['reg_phone'] = $request->input('account');
            }
    
            DB::beginTransaction();
    
            $memberId = DB::table('member')->insertGetId($insertData);
            
            if (is_int($memberId)) {
                if ($type === 'email') {
                    $activateCode = str_random(64);
                } elseif ($type === 'phone') {
                    $activateCode = rand(1000, 9000);
                }
            } else {
                Log::error('新增會員失敗：' . $e->getMessage());
                throw new \Exception('新增會員失敗');
            }
    
            $insertData = [
                'member_id' => $memberId,
                'code' => $activateCode,
                'type' => $reg_type,
                'created_at' => date('Y-m-d H:i:s')
            ];
    
            $result = DB::table('member_activator')->insert($insertData);
    
            if ($result !== true) {
                DB::rollBack();
                throw new \Exception('會員啟用驗證碼新增失敗');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->response(500, $e->getMessage());
        }

        try {
            // 新增 guzzlehttp 的 client
            $client = new Client();
            $notifyUrl = env('NOTIFY_API');
            switch ((int) $request->input('type')) {
                case 'email': // email 註冊的話寄信
                    $post = [
                        'email' => $request->input('email'),
                        'from' => $request->input('service@test.com'),
                        'title' => '歡迎您成為 XXXX 網站的會員',
                        'body' => view('notification.email.account.registration', $passData)->render()
                    ];
                    $sendResult = $this->client->request('POST', $notifyUrl . '/email', ['form_params' => $post]);
                    break;
                case 'phone': // 手機註冊的話寄驗證碼
                    $post = [
                        'phone' => $request->input('phone'),
                        'content' => view('notification.sms.account.registration', $passData)->render()
                    ];
                    $sendResult = $this->client->request('POST', $notifyUrl . '/email', ['form_params' => $post]);
                    break;
            }
        } catch (\Throwable $e) {
            Log::info('傳送驗證通知失敗');
        }
        
        return $this->response(200, '正常執行');
    }

    /**
     * 會員註冊，用傳入的參數 type 來分是 email 或手機註冊
     *
     * @param Request $request
     * @return void
     */
    public function register2(Request $request)
    {
        $nowDate = date('Y-m-d H:i:s');

        try {
            // 檢查傳入參數
            $request->validate([
                'type' => 'required|in:1,2',
                'email' => 'nullable|required_if:type,1|email',
                'phone' => 'nullable|required_if:type,2|regex:/^09\d{8}$/',
                'password' => 'required|string|min:8|max:16',
                'sex' => 'nullable|in:1,2,3'
            ]);
            
            // 設定寫入 member 資料表的資料
            $insertData = [
                'sex' => $request->input('sex'),
                'type' => $request->input('type'),
                'reg_date' => $nowDate,
                'updated_at' => $nowDate,
                'password' => Hash::make($request->input('password'))
            ];
    
            // 判斷註冊的方法，寫入不同的欄位
            switch ((int) $request->input('type')) {
                case 1:
                    $insertData['reg_email'] = $request->input('email');
                    break;
                case 2:
                    $insertData['reg_phone'] = $request->input('phone');
                    break;
            }
            
            DB::beginTransaction();
    
            // 新增一個會員並且取得會員 id
            $memberId = DB::table('member')->insertGetId($insertData);
            
            // 對不同的註冊方式取得不同的驗證碼
            if (is_int($memberId)) {
                if ($request->input('type') === '1') {
                    $activateCode = str_random(64);
                } elseif ($request->input('type') === '2') {
                    $activateCode = rand(1000, 9000);
                }
            } else {
                Log::error('新增會員失敗：' . $e->getMessage());
                throw new \Exception('新增會員失敗', 500);
            }
    
            // 設定寫入 member_activator 資料表的資料
            $insertData = [
                'member_id' => $memberId,
                'code' => $activateCode,
                'type' => $request->input('type'),
                'created_at' => date('Y-m-d H:i:s')
            ];
    
            $result = DB::table('member_activator')->insert($insertData);
    
            if ($result !== true) {
                DB::rollBack();
                throw new \Exception('會員啟用驗證碼新增失敗', 500);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->response($e->getCode(), $e->getMessage());
        }

        try {
            // 新增 guzzlehttp 的 client
            $client = new Client();
            $notifyUrl = env('NOTIFY_API');
            switch ((int) $request->input('type')) {
                case 1: // email 註冊的話寄信
                    $post = [
                        'email' => $request->input('email'),
                        'from' => $request->input('service@test.com'),
                        'title' => '歡迎您成為 XXXX 網站的會員',
                        'body' => view('notification.email.account.registration', $passData)->render()
                    ];
                    $sendResult = $this->client->request('POST', $notifyUrl . '/email', ['form_params' => $post]);
                    break;
                case 2: // 手機註冊的話寄驗證碼
                    $post = [
                        'phone' => $request->input('phone'),
                        'content' => view('notification.sms.account.registration', $passData)->render()
                    ];
                    $sendResult = $this->client->request('POST', $notifyUrl . '/email', ['form_params' => $post]);
                    break;
            }
        } catch (\Throwable $e) {
            Log::info('傳送驗證通知失敗');
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
