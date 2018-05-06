# 範例程式-會員註冊：單一職責-Presenter Layer

> 把 view 的顯示邏輯獨立出來
> 放置在 Presenters 資料夾內

## 在此範例，你會學到

- blade 注入 class

## Laravel 功能教學

- 在 blade 注入 class
  - 在 app\resources\views\notification\email\account\registration.blade.php 注入 class
  - ```php
    @inject('format', 'App\Presenters\Notify\Email')
    ```