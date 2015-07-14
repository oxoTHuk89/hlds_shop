<?php
//База данных, подключение
$user = "";
$password = "";
$host = "";
$database = "";
try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$database, $user, $password);
	$dbh->query("SET NAMES utf8");
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

//Robokassa
$mrh_login = ""; //логин
$mrh_pass1 = ""; //пароль1
$mrh_pass2 = ""; //пароль2
$inv_desc = "Game Nat1ons"; //Тайтл магазина
$culture = "ru";
$encoding = "utf-8";

//xPaw отправка amx_reloadadmins
$rcon = ""; //Rcon  пароль
$cmd = "amx_reloadadmins"; //Команда, посылаемая на сервер

//Sucsess
$url = "http://g-nation.ru/index.php?/topic/367-faq-po-magazinu/#entry3715";
