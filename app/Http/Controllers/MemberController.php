<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberCreatePost;
use App\Services\Account\MemberService;
use App\Services\Account\Registration\Native\Base as NativeBase;
use App\Services\Account\Registration\ThirdParty\Base as NativeThirdParty;
use Log;

class MemberController extends Controller
{
    /**
     * 會員註冊，用 url 路徑來分是用 email 或手機註冊
     *
     * @param Request $request 傳入參數
     * @param MemberService 負責處理 MemberController 的物件
     * @param NativeBase    原生註冊的抽象類別
     * @param string $type  註冊類別
     * @return void
     */
    public function register(
        MemberCreatePost $request,
        MemberService $memberService,
        NativeBase $account,
        string $type
    ) {
        try {
            $memberService->register($account, $request->input());
        } catch (\Throwable $e) {
            // 參數錯誤
            if ($e instanceof \App\Exceptions\InvalidParameterException) {
                Log::error($e->getMessage());
                return api_response(422, $e->getMessage());
            // 找不到物件
            } elseif ($e instanceof \App\Exceptions\ClassNotExistsException) {
                Log::error($e->getMessage());
                return api_response(501, $e->getMessage());
            // 寄送通知錯誤，只記 log 不回傳錯誤
            } elseif ($e instanceof \App\Exceptions\NotifyException) {
                Log::error($e->getMessage());
            } else {
                Log::error($e->getMessage());
                return api_response(501, '系統錯誤');
            }
        }
        
        return api_response(200, '正常執行');
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
                return api_response(422, $e->getMessage());
            }

            return api_response(501, '系統錯誤');
        }
        
        return api_response(200, '正常執行');
    }
}
