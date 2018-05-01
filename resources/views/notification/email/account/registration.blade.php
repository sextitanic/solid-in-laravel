<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @php
        echo substr($email, 0, strpos($email, '@'));
    @endphp
    @if (empty($sex) === false)
        @if ((int)$sex === 1)
        先生
        @elseif ((int)$sex === 2)
        小姐
        @endif
    @endif
    您好，請點選以下連結啟用帳戶
    <br>
    點我啟用：<a href="http://localhost/account/activate?code={{ $code }}">http://localhost/account/activate?code={{ $code }}</a>
</body>
</html>