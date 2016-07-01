<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 30.06.2016
 * Time: 12:59
 */

/*------------------ Б.Д. ------------------*/
$db = (array) json_decode(file_get_contents('db.json'),true);
define('HOST', $db['host']);
define('USER', $db['user']);
define('PASS', $db['pass']);
define('DB', $db['db']);

$PDO = new PDO('mysql:host='.HOST.';dbname='.DB,USER,PASS);
//($PDO)?die("Не удалось подключиться к БД"):"";
$PDO->exec("set names utf8");

