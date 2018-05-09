# 範例程式-會員註冊：Laravel - Service Provider

> 在程式進入 controller 之前先執行程式

## 在此範例，你會學到

- 建立 laravel 的 service provider 物件

## Laravel 功能教學

- 建立 provider：php artisan make:provider RegisterServiceProvider
- 新建立出來的檔案會在 app/Providers 資料夾內
- 在 config/app.php 裡面加入剛才建立的 provider
- 在 provider 裡面撰寫程式，若是有人需要 NativeBase 則判斷路徑並回傳對應物件
