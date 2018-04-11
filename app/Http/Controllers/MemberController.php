<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        $nowDate = date('Y-m-d H:i:s');

        try {
            $request->validate([
                'type' => 'required|in:1,2',
                'email' => 'nullable|required_if:type,1|email',
                'phone' => 'nullable|required_if:type,2|regex:/^09\d{8}$/',
                'password' => 'required|string|min:8|max:16',
                'sex' => 'nullable|in:1,2,3'
            ]);
            
            $insertData = [
                'sex' => $request->input('sex'),
                'type' => $request->input('type'),
                'reg_date' => $nowDate,
                'updated_at' => $nowDate,
                'password' => encrypt($request->input('password'))
            ];
    
            switch ((int) $request->input('type')) {
                case 1:
                    $insertData['reg_email'] = $request->input('email');
                    break;
                case 2:
                    $insertData['reg_phone'] = $request->input('phone');
                    break;
            }
            
            DB::beginTransaction();
    
            $memberId = DB::table('member')->insertGetId($insertData);
            
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
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response($e->getCode(), $e->getMessage());
        }

        switch ((int) $request->input('type')) {
            case 1:
                $sendResult = $this->sendRegisterEmail('', '', '');
                break;
            case 2:
                $sendResult = $this->sendRegisterSms('', '', '');
                break;
        }

        return $this->response(200, '正常執行');
    }

    public function register2(Request $request, string $type)
    {
        $nowDate = date('Y-m-d H:i:s');

        $rules = [
            'password' => 'required|string|min:8|max:16',
            'sex' => 'nullable|in:1,2,3'
        ];

        switch ($type) {
            case 'email':
                $rules['account'] = 'nullable|required_if:type,1|email';
                break;
            case 'phone':
                $rules['account'] = 'nullable|required_if:type,2|regex:/^09\d{8}$/';
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
                'reg_from' => $request->input('type'),
                'reg_date' => $nowDate,
                'updated_at' => $nowDate,
                'password' => encrypt($request->input('password'))
            ];
    
            if ($request->input('type') === 'email') {
                $insertData['reg_email'] = $request->input('email');
            } elseif ($request->input('type') === 'phone') {
                $insertData['reg_phone'] = $request->input('phone');
            }
    
            DB::beginTransaction();
    
            $memberId = DB::table('member')->insertGetId($insertData);
            
            if (is_int($memberId)) {
                if ($request->input('type') === '1') {
                    $activateCode = str_random(64);
                } elseif ($request->input('type') === '2') {
                    $activateCode = rand(1000, 9000);
                }
            } else {
                Log::error('新增會員失敗：' . $e->getMessage());
                throw new \Exception('新增會員失敗');
            }
    
            $insertData = [
                'member_id' => $memberId,
                'code' => $activateCode,
                'type' => $request->input('type'),
                'created_at' => date('Y-m-d H:i:s')
            ];
    
            $result = DB::table('member_activator')->insert($insertData);
    
            if ($result !== true) {
                DB::rollBack();
                throw new \Exception('會員啟用驗證碼新增失敗');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response(500, $e->getMessage());
        }

        try {
            switch ((int) $request->input('type')) {
                case 'email':
                    $this->sendRegisterEmail('', '', '');
                    break;
                case 'phone':
                    $this->sendRegisterSms('', '', '');
                    break;
            }
        } catch (\Exception $e) {
            Log::info('寄送註冊訊息失敗');
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

    private function sendRegisterEmail(string $email, string $title, string $body)
    {
        return true;
    }

    private function sendRegisterSms(string $phone, string $title, string $body)
    {
        return true;
    }
}
