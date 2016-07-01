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
        <div id="authForm" class="col-lg-6 col-md-6 col-sm-10 col-xs-12" style="<?php if(isset($_COOKIE['u_code'])) print 'display:none';?>">
            <h1 class="text-center">Введите данные для входа</h1>
            <div class="row">
                <form role="form" class="form-border" onsubmit="userAuth($(this)); return false">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input name="user" type="email" class="form-control" id="exampleInputEmail1" placeholder="Введите адрес электронной почты">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Пароль</label>
                        <input name="pass" type="password" class="form-control" id="exampleInputPassword1" placeholder="Пароль">
                    </div>
                    <button type="submit" class="btn btn-default">Войти</button>
                </form>
            </div>
        </div>
        <div id="app" style="<?php if(!isset($_COOKIE['u_code'])) print 'display:none';?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-3  sidebar">
                        <ul class="nav nav-sidebar">
                            <li onclick="getMessages('inbox',$(this));" class="active"><a href="#">Входящие</a></li>
                            <li onclick="getMessages('spam',$(this));"><a href="#">Спам</a></li>
                            <li onclick="getMessages('basket',$(this));"><a href="#">Корзина</a></li>
                        </ul>
                        <input type="hidden" name="dir" value="inbox"/>
                    </div>
                    <div class="col-lg-10 col-lg-offset-2 col-md-10 col-md-offset-2 col-sm-9 col-sm-offset-3  main">
                        <h2 class="sub-header"></h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-lg-10 col-md-10 col-sm-8 col-xs-8">Письмо</th>
                                        <th class="col-lg-1 col-md-1 col-sm-2 col-xs-2">Дата</th>
                                        <th class="col-lg-1 col-md-1 col-sm-2 col-xs-2">Удалить</th>
                                    </tr>
                                    </thead>
                                    <tbody id="messagesBlock">

                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="loading">
    <div class="cssload-dots">
        <div class="cssload-dot"></div>
        <div class="cssload-dot"></div>
        <div class="cssload-dot"></div>
        <div class="cssload-dot"></div>
        <div class="cssload-dot"></div>
    </div>

    <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="goo">
                <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="12" ></feGaussianBlur>
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0	0 1 0 0 0	0 0 1 0 0	0 0 0 18 -7" result="goo" ></feColorMatrix>
                <!--<feBlend in2="goo" in="SourceGraphic" result="mix" ></feBlend>-->
            </filter>
        </defs>
    </svg>
</div>




<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
