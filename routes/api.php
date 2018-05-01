<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 用路徑判斷是 email 或是手機註冊
Route::post('/member/register/{type}', 'MemberController@register');

// 偽裝 email 寄信 API 回傳值
Route::post('/email', function () {
    return response()->json([
        'status' => 200,
        'message' => 'success',
        'data' => []
    ]);
});
// 偽裝 sms 傳簡訊 API 回傳值
Route::post('/sms', function () {
    return response()->json([
        'status' => 200,
        'message' => 'success',
        'data' => []
    ]);
});
