<!doctype html>
<html lang="ru">
<head>
    <meta name="charset" content="utf-8"/>
    <title>MNU Events - Вход</title>
</head>
<body>
<form action="/login" method="post">
    @csrf
    <input type="text" name="login"/>
    <input type="password" name="password"/>
    <button type="submit">Войти</button>
</form>
</body>
</html>
