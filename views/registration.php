<?php
if (isset($_SESSION['username'])) {
    header("location:balance.php");
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Регистрация | Account Manager</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/common.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <form method="POST" action="../newuser.php" class="form-signin">
        <h2 class="form-heading">Регистрация</h2>
        <div class="form-group">
            <input name="username" type="text" class="form-control" placeholder="Имя пользователя"
                   autofocus="true"/>
            <input name="password" type="password" class="form-control" placeholder="Пароль"/>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Зарегистрировать</button>
        </div>
    </form>
</div>
</body>
</html>