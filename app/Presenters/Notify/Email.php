<?php

namespace App\Presenters\Notify;

class Email
{
    public function name(string $email): string
    {
        $result = substr($email, 0, strpos($email, '@'));
        return strtolower($result);
    }

    public function sex(int $sex): string
    {
        $result = '';

        if ($sex === 1) {
            $result = '先生';
        } elseif ($sex === 2) {
            $result = '小姐';
        }

        return $result;
    }
}
