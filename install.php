<?php
if(isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['db'])){
    try{
        $PDO = new PDO('mysql:host='.$_POST['host'].';dbname='.$_POST['db'],$_POST['user'],$_POST['pass'],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    catch(PDOException $ex){
        die(print "Доступ к бд указан не верно <a href='install.php'> Указать еще раз</a>");
    }



    $PDO->exec("set names utf8");
    $res = $PDO->query("
        CREATE TABLE `users` (
          `id` int(5) NOT NULL,
          `email` varchar(70) NOT NULL,
          `password` varchar(100) NOT NULL,
          `u_code` varchar(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

    $PDO->query("
        ALTER TABLE `users`
          ADD PRIMARY KEY (`id`),
          ADD UNIQUE KEY `email` (`email`);
        ");
    if($res){
        $DBARR = [
            "host"      => $_POST['host'],
            "user"      => $_POST['user'],
            "pass"      => $_POST['pass'],
            "db"        => $_POST['db'],
        ];
        $write = file_put_contents('db.json',json_encode($DBARR,JSON_UNESCAPED_UNICODE));
        if(!$write){print "Не удалось записать данные в db.json"; die;}
        print "Установка прошла успешно <a href='index.php'> К приложению </a>"; die;

    }else{
        print "Доступ к бд указан не верно <a href='install.php'> Указать еще раз</a>"; die;
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/loading.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div id="authForm" class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
            <h1 class="text-center">Установка</h1>
            <div class="row">
                <form role="form" class="form-border" action="" METHOD="POST">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Хост базы данных</label>
                        <input name="host" type="text" class="form-control" id="exampleInputEmail1" placeholder="Введите адрес электронной почты">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Логин пользователя БД</label>
                        <input name="user" type="text" class="form-control" id="exampleInputPassword1" placeholder="логин">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Пароль БД</label>
                        <input name="pass" type="text" class="form-control" id="exampleInputPassword1" placeholder="Пароль">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Название базы данных</label>
                        <input name="db" type="text" class="form-control" id="exampleInputPassword1" placeholder="Название БД">
                    </div>
                    <button type="submit" class="btn btn-default">Установить</button>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
