# 範例程式-會員註冊：單一職責-Service Layer

> 把商業邏輯的部分切出來放到 Services 資料夾內

## 在此範例，你會學到

- 物件繼承
- 自訂 Exception 物件
- 在 Request 物件中新增資料到 input 裡

## 這種寫法的缺點

- 全部東西都在 controller 裡面
- 無法撰寫單元測試程式
- 程式無法重複使用
