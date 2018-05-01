# 範例程式-會員註冊(不好的寫法)

> 此範例先用不好的撰寫方式來完成會員註冊功能   
> 註冊功能分成兩種，email 註冊或手機號碼註冊  
> 會用一個 controller 把這兩種註冊方式完成  
> 並且說明一下這種寫程式方法的錯誤在哪邊

## 在此範例，你會學到

- route 跟 controller 對應的設定和路徑的變數
- laravel 的兩種參數驗證方式(Request 物件跟 helper function)
- 基本的 DB Query Builder
- 基本的 try catch 和 exception
- 將變數傳入 blade 模板
- 把 view 存成變數
- 安裝新的 package 並使用

## 這種寫法的缺點

- 全部東西都在 controller 裡面
- 無法撰寫單元測試程式
- 程式無法重複使用

## laravel 功能教學

- 建立 controller
  - 使用指令 php artisan make:controller MemberController
- 路徑和 controller 對應
  - 在 routes/web.php 或 routes/api.php 設定 url 對應的 contrller@method
  - 在 api.php 裡面設定的，url 路徑要多帶 api，e.g. localhost/api/path
- 安裝 package GuzzleHttp
  - 用途：http request 的套件
  - 安裝方式：composer require guzzlehttp/guzzle
