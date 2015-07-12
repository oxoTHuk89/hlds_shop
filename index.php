<?php 
include "connect.php";
require_once('smarty/Smarty.class.php');

//Запросы
$sql_servers = "SELECT id, hostname  FROM amx_serverinfo WHERE id<>2";
$sql_type = "SELECT * FROM pay_type";
$sql_player = "SELECT id, steamid, access, created, expired, days FROM amx_amxadmins WHERE ashow <> 0 AND  access = 'bcdefijmnotu' OR access = 'bt' OR access = 'b'";
//Формирование переменных для шаблона
	$date = date("Y-m-d H:i:s");//Дата 
	//Список серверов ID+Hostname
	foreach ($dbh->query($sql_servers) as $myrow) {
		$servers_id[] = $myrow['id'];
		$servers_name[] = $myrow['hostname'];
		
		//$servers = array($servers_id,$servers_name);
	}
	//Список типов
	foreach ($dbh->query($sql_type) as $myrow) {
		$pay_cost[] = $myrow['cost'];
		$pay_type[] = $myrow['name'];
	}
	//Список админов+VIP + вся хуйня
	foreach ($dbh->query($sql_player) as $myrow) {
		$player_id[] = $myrow['id'];
		$player_name[] = $myrow['steamid'];
			//$sql_player = "SELECT * FROM pay WHERE name ='".$myrow['steamid']."'";
	}
//Данные уходят в Smarty
$smarty = new Smarty();
$smarty->assign('date', $date);
$smarty->assign('servers_id', $servers_id);
$smarty->assign('servers_name', $servers_name);
$smarty->assign('pay_cost', $pay_cost);
$smarty->assign('pay_type', $pay_type);
$smarty->assign('player_id', $player_id);
$smarty->assign('player_name', $player_name);
$smarty->assign('steamid', $steamid);
$smarty->assign('bid', $bid);
$smarty->assign('player_nick', $player_nick);
$smarty->assign('rest_name', $rest_name);

$smarty->assign('cost', $cost);
$smarty->assign('mrh_login', $mrh_login);
$smarty->assign('mrh_pass1', $mrh_pass1);
$smarty->assign('inv_id', $inv_id);
//$smarty->assign('inv_desc', $inv_desc);
$smarty->assign('pay_type', $pay_type);
$smarty->assign('culture', $culture);
$smarty->assign('encoding', $encoding);
$smarty->assign('crc', $crc);
//** раскомментируйте следующую строку для отображения отладочной консоли
//$smarty->debugging = true;
$smarty->display('body.tpl');

?>
