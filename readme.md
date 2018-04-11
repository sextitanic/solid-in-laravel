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

## 這種寫法的缺點

- 全部東西都在 controller 裡面
- 無法撰寫單元測試程式
- 程式無法重複使用