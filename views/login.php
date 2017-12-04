<?php
if (isset($_SESSION['username'])) {
    header("location:balance.php");
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Вход | Account Manager</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/common.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <form method="POST" action="../verifyuser.php" class="form-signin">
        <h2 class="form-heading">Вход</h2>
        <div class="form-group">
            <input name="username" type="text" class="form-control" placeholder="Имя пользователя"
                   autofocus="true"/>
            <input name="password" type="password" class="form-control" placeholder="Пароль"/>
            <button class="btn btn-lg btn-primary btn-block" type="submit" name="action" value="sign in">Войти</button>
            <!--<button class="btn btn-lg btn-primary btn-block" type="submit" name="action" value="sign up">Зарегистрироваться</button>-->
            <h4 class="text-center"><a href="registration.php">Создать аккаунт</a></h4>
        </div>
    </form>
</div>
</body>
</html>