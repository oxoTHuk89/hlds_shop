<?php
include("connect.php");
require_once('smarty/Smarty.class.php');

$date = date("Y-m-d H:i:s");
if (!empty($_POST['sid_return'])) {
	$nickname = htmlspecialchars($_POST['sid'], ENT_QUOTES);
		
	$sql = "SELECT * FROM `amx_bans` WHERE `player_nick` LIKE '%{$nickname}%'";
	if ($nickname == ""){
		//die("Произошла ошибка, обновите страницу и попробуйте обратиться к администратору.");
		$success = 0;	
	}	
	else {
		$success = 1;
	}	
	foreach ($dbh->query($sql) as $myrow){
		$bid[] = $myrow['bid'];
		$player_nick[] = $myrow['player_nick'];
		$admin_nick[] = $myrow['admin_nick'];
		$ban_reason[] = $myrow['ban_reason'];
		$ban_created[] = date("Y-m-d h:i", $myrow['ban_created']);
		
	}
	//Тут надо запилить ошибку, когда не находит ник
	if($myrow == NULL) {
		$success = 0;
	}
	if($myrow != ""){
		$smarty = new Smarty();
		//В шаблон визуалка
		$smarty->assign('bid', $bid);
		$smarty->assign('player_nick', $player_nick);	
		$smarty->assign('admin_nick', $admin_nick);
		$smarty->assign('ban_reason', $ban_reason);
		$smarty->assign('ban_created', $ban_created);
		$smarty->assign('success', $success);
		//В шаблон Robokasa
		$smarty->assign('inv_id', $inv_id);
		$smarty->assign('inv_desc', $inv_desc);
		$smarty->assign('crc', $crc);
		$smarty->assign('Shp_item', $shp_item);
		$smarty->assign('culture', $encoding);
		$smarty->assign('mrh_login', $mrh_login);
		$smarty->assign('mrh_pass1', $mrh_pass1);
		$smarty->assign('cost', $cost);
		$smarty->display('search_steam.tpl');
	}	
}
if (!empty($_POST['unban'])) {
	$type = 5;
	$unban_id = htmlspecialchars($_POST['unban_id'], ENT_QUOTES);
	$steamid = htmlspecialchars($_POST['player_nick'], ENT_QUOTES);
	$cost_unban = htmlspecialchars($_POST['cost_unban'], ENT_QUOTES);
	$sql_q = $dbh->query("
				INSERT INTO `pay` (`cost`, `username`, `pasword`, `server_id`, `type`, `date`, `vk`) 
					VALUES ('{$cost_unban}', '{$steamid}', '{$unban_id}', '{$server_id}', '{$type}', '{$date}', '{$vk}')");
    if (!$sql_q){
        die("Произошла ошибка, обновите страницу и попробуйте обратиться к администратору.");
		$success = 0;
	} else {
		$success = 1;
	}
	$inv_id = $dbh->lastInsertId();
	$shp_item= "Снятие бана";
	$crc  = md5("{$mrh_login}:{$cost_unban}:{$inv_id}:{$mrh_pass1}:Shp_item={$shp_item}");
	$smarty = new Smarty();
	$smarty->assign('success', $success);
	$smarty->assign('inv_id', $inv_id);
	$smarty->assign('inv_desc', $inv_desc);
	$smarty->assign('crc', $crc);
	$smarty->assign('Shp_item', $shp_item);
	$smarty->assign('culture', $encoding);
	$smarty->assign('mrh_login', $mrh_login);
	$smarty->assign('mrh_pass1', $mrh_pass1);
	$smarty->assign('cost', $cost_unban);
	$smarty->display('search_steam.tpl');
}
if (!empty($_POST['shop'])) {
	if (
        empty($_POST['server_id'])
            || 
        empty($_POST['cost'])
            || 
        empty($_POST['username'])
            || 
        empty($_POST['pass'])
            || 
        empty($_POST['vk'])
    ) {
        exit('Не все параметры переданы');
    }
	$sql_hostname = $dbh->query("SELECT `hostname` FROM `amx_serverinfo` WHERE `id` = '".intval($_POST['server_id'])."'");	
	$row_hostname = $sql_hostname->fetch();
	$sql_type = $dbh->query("SELECT `name` FROM `pay_type` WHERE `cost` = '".htmlspecialchars($_POST['cost'], ENT_QUOTES)."'");	
	$row_type = $sql_type->fetch();
	
	$sql = $dbh->query("SELECT COUNT(*) FROM `amx_amxadmins` WHERE `steamid` = '".htmlspecialchars($_POST['username'], ENT_QUOTES)."'");	
	$row = $sql->fetch();
	//var_dump($row);
	if ($row[0]!=0) {	
		$error =  "Такой ник уже существует. Введите другой!";
		$smarty = new Smarty();		
		$smarty->assign('error', $error);
		$smarty->display('res.tpl');
	} else {
		$steamid = htmlspecialchars($_POST['username'], ENT_QUOTES);
		$pass = md5($_POST['pass']);
		$server_id = intval($_POST['server_id']);
		$cost = htmlspecialchars($_POST['cost'], ENT_QUOTES);
		$vk = htmlspecialchars($_POST['vk'], ENT_QUOTES);
		$sql_type = $dbh->query("SELECT id, name FROM pay_type WHERE cost = '{$cost}'");
		$row_type = $sql_type->fetch();
		$type = $row_type['id'];
		$type_name = $row_type['name'];
		
		
		$sql_q = $dbh->query("
				INSERT INTO `pay` (`cost`, `username`, `pasword`, `server_id`, `type`, `date`, `vk`) 
					VALUES ('{$cost}', '{$steamid}', '{$pass}', '{$server_id}', '{$type}', '{$date}', '{$vk}')");
		if (!$sql_q){
			die("Произошла ошибка, обновите страницу и попробуйте обратиться к администратору.");
		}
		//Robokassa для формы в зависимости от поста переменные
		$inv_id = $dbh->lastInsertId();
		$shp_item= "Получение услуг";
		$crc  = md5("{$mrh_login}:{$cost}:{$inv_id}:{$mrh_pass1}:Shp_item={$shp_item}");
		$smarty = new Smarty();
		$smarty->assign('inv_id', $inv_id);
		$smarty->assign('inv_desc', $inv_desc);
		$smarty->assign('crc', $crc);
		$smarty->assign('Shp_item', $shp_item);
		$smarty->assign('culture', $encoding);
		$smarty->assign('mrh_login', $mrh_login);
		$smarty->assign('mrh_pass1', $mrh_pass1);
		$smarty->assign('cost', $cost);
		//Для визуалки
		$smarty->assign('server_id', $row_hostname['hostname']);
		$smarty->assign('type', $type_name);
		$smarty->assign('steamid', $steamid);
		$smarty->display('res.tpl');
		}
}			
if (!empty($_POST['renewal'])) {
	$login_name = htmlspecialchars($_POST['login_name'], ENT_QUOTES);
	$login_pass = md5($_POST['login_pass']);	
	$sql = $dbh->query(
	"SELECT `id`, `steamid`, `password`, `access`, `expired` 
		FROM `amx_amxadmins` 
			WHERE `steamid` = '".$login_name."' AND 
					`password` = '".$login_pass."'");	
	$row = $sql->fetch();
	$steamid = $row['steamid'];
	$sql_admin_server = $dbh->query(
	"SELECT `server_id` 
		FROM `amx_admins_servers` 
			WHERE `admin_id` = '".$row['id']."'");	
	$row_admin_server = $sql_admin_server->fetch();
	$server_id = $row_admin_server['server_id'];
	
	
	$expired = date('Ymd',$row['expired']);
	$today = date('Ymd',strtotime('NOW'));
	
	if($row['expired'] < strtotime('NOW') && $row['expired'] <> 0) {
		$timeleft = "Истекло";
	} elseif($row['expired'] == 0 ) {
		$timeleft = "Никогда";
	} elseif($expired > $today) {
		//$timeleft = $expired - $today;
		$datetime1 = new DateTime($expired);
		$datetime2 = new DateTime($today);
		$interval = $datetime1->diff($datetime2);
		$timeleft = $interval->format('%a дней');
	}
    
	if (!$row) {
		$login_incorrect = "Неправильные данные";
		$timeleft = "";
		//echo "Данные не верны";
	} else {
		$sql_access = $dbh->query("SELECT `id`, `name`, `cost` FROM `pay_type` WHERE `access` = '".$row['access']."'");
		$row = $sql_access->fetch();
		//$type = $row['id']; //пока тип передаем вручну для обновы
		$type = 6;
		$type_name = $row['name'];
		$cost = $row['cost'];
	}
	if(!isset($login_incorrect)) {
		$sql_q = $dbh->query("
				INSERT INTO `pay` (`cost`, `username`, `pasword`, `server_id`, `type`, `date`, `vk`) 
					VALUES ('{$cost}', '{$steamid}', '{$pass}', '{$server_id}', '{$type}', '{$date}', '{$vk}')");
		if (!$sql_q){
			die("Произошла ошибка, обновите страницу и попробуйте обратиться к администратору.");
		}
	}
    $inv_id = $dbh->lastInsertId();
    $shp_item= "Продление услуг";
    $crc  = md5("{$mrh_login}:{$cost}:{$inv_id}:{$mrh_pass1}:Shp_item={$shp_item}");
	$smarty = new Smarty();
	$smarty->assign('inv_id', $inv_id);
	$smarty->assign('inv_desc', $inv_desc);
	$smarty->assign('crc', $crc);
	$smarty->assign('Shp_item', $shp_item);
	$smarty->assign('culture', $encoding);
	$smarty->assign('mrh_login', $mrh_login);
	$smarty->assign('mrh_pass1', $mrh_pass1);
	
	$smarty->assign('login_incorrect', $login_incorrect);
	$smarty->assign('type', $type);
	$smarty->assign('type_name', $type_name);
	$smarty->assign('cost', $cost);
	$smarty->assign('timeleft', $timeleft);
	$smarty->display('renewal.tpl');
}
