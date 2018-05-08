<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function email(Request $request)
    {
        $passData = [
            'account' => 'mars.wu@udnshopping.com',
            'sex' => 2,
            'code' => str_random(64)
        ];
        return view('notification.email.account.registration', $passData);
    }

    public function sms(Request $request)
    {
        $passData = [
            'code' => rand(1000, 9000)
        ];

        return view('notification.sms.account.registration', $passData);
    }
}
