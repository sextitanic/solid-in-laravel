# 範例程式-會員註冊：單一職責-Laravel Form Request Validation

> 在 Controller 注入 Request 物件前先執行傳入參數檢查

## 在此範例，你會學到

- 先過濾參數傳入內容再執行 controller  
- 統一 Exception 處理  
- 捕捉自訂的錯誤處理

## Laravel 功能教學

- 建立 Form Request Validation 物件
  - 執行 artisan 指令建立，php artisan make:request MemberCreatePost
  - 會出現在 app\Http\Requests 資料夾內