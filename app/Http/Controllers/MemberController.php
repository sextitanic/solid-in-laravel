<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberCreatePost;
use App\Services\Account\Registration\RegistrationFactory;
use App\Services\Account\MemberService;
use Log;

class MemberController extends Controller
{
    /**
     * 會員註冊，用 url 路徑來分是用 email 或手機註冊
     *
     * @param Request $request
     * @param string $type
     * @return void
     */
    public function register(MemberCreatePost $request, MemberService $memberService, string $type)
    {
        try {
            // 依照傳進來的路徑來呼叫要使用的物件
            $account = RegistrationFactory::create($type);

            $memberService->register($account, $request->input());
        } catch (\Throwable $e) {
            // 參數錯誤
            if ($e instanceof \App\Exceptions\InvalidParameterException) {
                Log::error($e->getMessage());
                return $this->response(422, $e->getMessage());
            // 找不到物件
            } elseif ($e instanceof \App\Exceptions\ClassNotExistsException) {
                Log::error($e->getMessage());
                return $this->response(501, $e->getMessage());
            // 寄送通知錯誤，只記 log 不回傳錯誤
            } elseif ($e instanceof \App\Exceptions\NotifyException) {
                Log::error($e->getMessage());
            } else {
                Log::error($e->getMessage());
                return $this->response(500, '系統錯誤');
            }
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
