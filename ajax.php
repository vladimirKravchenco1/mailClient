<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 30.06.2016
 * Time: 11:43
 */
error_reporting (E_ERROR);
ini_set("display_errors", 1);
header("Content-Type:text/html;charset=UTF-8");
require_once('db.php');
require_once('functions.php');

/*------------ Метод вызова функции обработки AJAX запроса >>> ------------*/
function getAjaxFunction($PDO){
    if(!isset($_GET['func'])){
        exit("Error");
    }
    $method = $_GET['func'];//получаем название функции
    if(function_exists($method)){
        $result = $method($PDO);
        if(!empty($result)){print json_encode($result, JSON_UNESCAPED_UNICODE);}
    }
}
/*------------ <<<  Метод вызова функции обработки AJAX запроса ------------*/


function getGmailConnect($PDO){
    $return = array('state'=>'false','errors'=>'','data'=>'');

    if(isset($_POST['user'])){
        $user = trim($_POST['user']);
    }
    else{
        $return['error']="Не указан email";
        return $return;
    }

    if(isset($_POST['pass'])){
        $pass = $_POST['pass'];
    }
    else{
        $return['error']="Не указан пароль";
        return $return;
    }

    $connect = gmailConnect($user,$pass);
    if($connect){
        $res = $PDO->query("SELECT u_code FROM users WHERE `email`='$user' AND `password`='$pass'")->fetch();
        if(!$res['u_code']){
            $u_code = uniqid();
            $res = $PDO->query("INSERT INTO users (`email`,`password`, `u_code`) VALUES ('$user', '$pass', '$u_code')");
        }else{
            $u_code = $res['u_code'];
        }
        setcookie("u_code",$u_code, time()+360*24*7, "/");
        if($res){$return['state'] = "true"; $return['data']='Успешная авторизация';}
    }else{
        $return['errors'] = "Не удалось подключиться к gmail";
    }
    imap_close($connect);
    return $return;
}


function getMessages($PDO){
    if(isset($_POST['dir']) and in_array($_POST['dir'],['inbox','spam','basket'])){
        $dir = $_POST['dir'];
    }else{
        $dir = 'inbox';
    }
    $return = array('state'=>'false','errors'=>'','data'=>'');
    if(!isset($_COOKIE['u_code'])){ $return['errors']= "Вы не авторизированы"; return $return;}

    $u_code = $_COOKIE['u_code'];
    $userData = $PDO->query("SELECT `email`,`password` FROM users WHERE `u_code`='$u_code'")->fetch();
    if(!$userData){ $return['errors']= "Вы не авторизированы"; return $return;}

    $messages = getMessagesData($userData['email'], $userData['password'],$dir);

    foreach($messages as $message){
        $date = date("Y-m-d",  strtotime($message['date']));
        (empty($message['title']))?$message['title'] = "Без темы":$message['title'];
        $return['data'].= "<tr><td onclick='getMessageBody({$message['msg_num']},$(this));'>{$message['title']}</td>,<td>{$date}</td><td><button onclick=\"deleteMess({$message['msg_num']},$(this));\" type=\"button\" class=\"btn btn-danger\">Удалить</button></td></tr>";
    }
    $return['state'] = "true";
    return $return;
}

function getMessageBody($PDO){
    $return = array('state'=>'false','errors'=>'','data'=>'');
    if(!isset($_COOKIE['u_code'])){ $return['errors']= "Вы не авторизированы"; return $return;}

    $u_code = $_COOKIE['u_code'];
    $userData = $PDO->query("SELECT `email`,`password` FROM users WHERE `u_code`='$u_code'")->fetch();
    if(!$userData){ $return['errors']= "Вы не авторизированы"; return $return;}
    $id = $_POST['id'];
    $dir = $_POST['dir'];
    $fullMessage = getMessageBodyById($userData['email'],$userData['password'],$id, $dir);
    //var_dump($fullMessage);
    !empty($fullMessage['body']) and $return['state']='true';
    $return['data'] = $fullMessage['body'];
    return $return;
}

function deleteMess($PDO){
    $return = array('state'=>'false','errors'=>'','data'=>'');
    if(!isset($_COOKIE['u_code'])){ $return['errors']= "Вы не авторизированы"; return $return;}

    $u_code = $_COOKIE['u_code'];
    $userData = $PDO->query("SELECT `email`,`password` FROM users WHERE `u_code`='$u_code'")->fetch();
    if(!$userData){ $return['errors']= "Вы не авторизированы"; return $return;}
    $id = $_POST['id'];
    $connect = gmailConnect($userData['email'],$userData['password']);
    $res =imap_mail_copy($connect, $id,'[Gmail]/Trash');
    if($res){$return['state'] = "true"; $return['data'] = "Успешно удалено!";}
    return $return;
}

getAjaxFunction($PDO);