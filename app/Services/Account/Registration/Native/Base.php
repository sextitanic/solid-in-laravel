<?php

namespace App\Services\Account\Registration\Native;

use DB;
use App\Services\Account\Registration\BaseRegistration;
use App\Repositories\MemberRepository;
use App\Repositories\MemberActivatorRepository;
use App\Contracts\Account\Registration\Activator as ActivatorContract;
use App\Contracts\Account\Registration\Notify as NotifyContract;
use App\Services\Api\Internal\NotifyApi;

abstract class Base extends BaseRegistration implements ActivatorContract, NotifyContract
{
    protected $activator;
    protected $notify;
    protected $member;

    public function __construct(
        MemberActivatorRepository $activator,
        NotifyApi $notify,
        MemberRepository $member
    ) {
        $this->member = $member;
        $this->activator = $activator;
        $this->notify = $notify;
    }

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
        parent::validate($input);

        $rules = [
            'password' => 'required|string|min:8|max:16', // 密碼必填，且字數在 8 ~ 16 之間
            'sex' => 'nullable|in:1,2'
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

        $result = $this->activator->create($insertData);

        return true;
    }
}
