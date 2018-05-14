# SOLID 講解 in Laravel

> 公司內部教學
> 會使用會員註冊的功能來講解 SOLID 觀念  
> - 檢查傳入參數
> - 寫入會員資料表
> - 取得啟用帳戶驗證碼
> - 寫入會員啟用資料表
> - 寄送驗證通知
> 
> 會分成多個 Git 分支將此功能一一拆解  
> 帶大家瞭解基本的觀念

可搭配教學影片一起服用：[https://youtu.be/ZntHFYumuLA](https://youtu.be/ZntHFYumuLA "教學影片")

## 開發環境

- 作業系統：Ubuntu 16.04 In Docker
- 語言：PHP 7.2.3
- Framework：Laravel 5.6.12

## 大綱

- 一般常見不推薦的程式
  - 將程式都寫同一支程式裡 - 01_bad_code
- S：單一職責 (SRP：Single Responsibility Principle)
  - 一個類別負責一件事情
    - Service：負責處理商業邏輯 - 02_1_service_layer
    - Repository：負責處理資料表操作邏輯 - 02_2_repository_layer
    - Presenter：負責處理 view 顯示邏輯 - 02_3_presenter_layer
    - Laravel Form Request Validation - 02_4_form_request_validation
- O：開放/封閉原則 (OCP：Open/closed principle)
  - 開放程式擴充功能，封閉修改程式
    - 使用靜態工廠模式：03_open_closed_principle
- L：里氏替換原則 (LSP：Liskov Substitution Principle)
  - 擴充父類別功能而不是修改它：04_liskov_substitution_principle
- I：介面隔離原則 (ISP：Interface Segregation Principles)
  - 類別不需要實作它不需要用到的程式
    - 錯誤的類別，空實作過多不需要的方法：05_1_wrong_interface_segregation_principle
    - 將各種方法切成不同方法讓程式依需求實作：05_2_interface_segregation_principle
- D：依賴反轉原則 (DIP：Dependency Inversion Principle)
  - 高階模組不應該依賴低階模組，兩者都應該依賴抽象
  - 抽象不依賴細節，細節要依賴抽象
    - 建構子注入：06_1_constructor_injection
    - 依賴注入：06_2_dependency_inversion
    - Laravel Service Provider：06_3_laravel_service_provider
- Helper：建立自己的 helper 函式，並且自動載入
  - 07_helper