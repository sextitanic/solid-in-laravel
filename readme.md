# 範例程式-會員註冊：單一職責-repository Layer

> 把資料表操作邏輯的部分切出來放到 Repositories 資料夾內  
> 用 Laravel 的 Eloquent 配合操作資料表

## 在此範例，你會學到

- Eloquent
  - 指定資料表名稱
  - 自動寫入新增和更新時間
  - 新增資料前自動轉換格式或加密

## Laravel 功能教學

- 在 Models 資料夾內建立 Eloquent Model
  - 使用指令建立 Eloquent php artisan make:model Models/Member
- 修改 Eloquent 預設寫入的欄位
  - created_at 更改，指定為 member 資料表的 reg_date 欄位
  - 手動指定對應的 table 名稱
  - 新增欄位資料時自動轉換資料格式或加密
  - ```php
    const CREATED_AT = 'reg_date';

    protected $table = 'member';

    public function setRegEmailAttribute(string $email = null)
    {
        $this->attributes['reg_email'] = strtolower($email);
    }

    public function setPasswordAttribute(string $password = null)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    ```