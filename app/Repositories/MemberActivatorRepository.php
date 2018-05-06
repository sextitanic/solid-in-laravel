<?php

namespace App\Repositories;

use App\Models\MemberActivator;

class MemberActivatorRepository
{
    private $activator;

    public function __construct()
    {
        $this->activator = new MemberActivator();
    }

    /**
     * 新增一筆會員啟用驗證碼資料
     *
     * @param array $data[
     *      @var int    $member_id 會員 id
     *      @var string $code      啟用驗證碼
     *      @var int    $type      註冊類型
     * ]
     * @return void
     */
    public function create(array $data)
    {
        $rules = [
            'member_id' => 'required|integer',
            'code' => 'required|string',
            'type' => 'required|integer'
        ];

        $this->validate($data, $rules);

        try {
            $this->activator->member_id = $data['member_id'];
            $this->activator->code = $data['code'];
            $this->activator->type = $data['type'];

            if ($this->activator->save() !== true) {
                throw new \App\Exceptions\DatabaseQueryException('新增 member_activator 資料表失敗');
            }
        } catch (\Exception $e) {
            throw new \App\Exceptions\DatabaseQueryException($e->getMessage());
        }
        
        return true;
    }

    private function validate(array $input, array $rules)
    {
        $validator = validator($input, $rules);
        if ($validator->fails()) {
            throw new \App\Exceptions\InvalidParameterException($validator->errors()->first());
        }

        return true;
    }
}
