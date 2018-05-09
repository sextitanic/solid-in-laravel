<?php

namespace App\Services\Account\Registration;

class RegistrationFactory
{
    public static function create(string $type)
    {
        // 依照傳進來的路徑來呼叫要使用的物件
        $class = 'App\Services\Account\Registration\\' . ucfirst($type);
        if (class_exists($class) === false) {
            throw new \App\Exceptions\ClassNotExistsException('找不到 ' . $class . ' 物件');
        }
        return \App::make($class);
    }
}
