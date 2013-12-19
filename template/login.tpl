<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="{path}css/main.css">
        <link rel="stylesheet" href="{path}css/framework.css">
	<link href="/images/favicon.png" rel="icon" type="image/png">
        <title>Добро пожаловать - Система управления версиями</title>
    </head>
    <body>
      
        <div class="f-nav-bar f-nav-bar-fixed"><div style="text-align:center;">Пожалуйста, введите ваш логин и пароль</div></div>
        <div class="centerLogin">
		<div class='logindiv'>
                  <form class='loginform' action="/" method='POST'>
                  <input type="hidden" name="cmd" value="login">
                  Логин:<br>
                  <input class='' type="text" name='login' value=''><br>
                  Пароль:<br>
                  <input class='' type="password" name='password' value=''><br>
                  <div class='righter'><input class='submitinput' type="submit" value='Вход'></div>
                  </form>
                </div>
	<div style="margin: 20px;">{messages}</div>
        </div> 
    </body>
</html>
