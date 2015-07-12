<?
include("connect.php");
require __DIR__ . '/include/SourceQuery.class.php';
//установка текущего времени
//current date
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);
$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
// проверка корректности подписи
if ($my_crc !=$crc){
	echo "bad sign\n";
	exit();
}

$sql = $dbh->query("UPDATE pay SET status = '1' WHERE id = '$inv_id'");
$sql = $dbh->query("SELECT * FROM pay WHERE id='$inv_id'");
$row = $sql->fetch();
$server_id = $row['server_id'];

$sql_type = $dbh->query("SELECT id, access FROM pay_type WHERE cost = '$out_summ'");
$row_type = $sql_type->fetch();
$type = $row['type'];
if ($type == "1" || $type == "2" || $type == "3" || $type == "4"){
	$access = $row_type['access'];	
	$password = $row['pasword'];
	$username = "";
	$flags = "a";
	$steamid = $row['username'];
	$icq = $row['vk'];
	$nickname = $row['username'];
	$ashow = "1";
	$days = "30";
	$addtoserver = $row['server_id'];
	$exp="(UNIX_TIMESTAMP()+(".($days * 86400).")),";
	$query = $dbh->query
		("INSERT INTO `amx_amxadmins`
			(`username`,`password`,`access`,`flags`,`steamid`,`nickname`,`icq`,`ashow`,`created`,`expired`,`days`) 
				VALUES ('".$username."','".$password."','".$access."','".$flags."','".$steamid."','".$nickname."','".$icq."',".$ashow.",UNIX_TIMESTAMP(),".$exp."".$days.")");
	$adminid = $dbh->lastInsertId();
	$sban="yes";
	$query = $dbh->query
		("INSERT INTO `amx_admins_servers` 
			(`admin_id`,`server_id`,`custom_flags`,`use_static_bantime`) 
				VALUES('".$adminid."','".$addtoserver."','','".$sban."')");
}
//Продляем услугу
if ($type == 6){
	$sql = $dbh->query("SELECT steamid, expired FROM amx_amxadmins WHERE steamid='".$row['username']."'");
	$row = $sql->fetch();
	$day = 30;
	$create = date("Y-m-d H:i:s",$row['created']); //Created
	$expired = date("Y-m-d H:i:s",$row['expired']); //Current expired
	if (strtotime($expired) > strtotime(TODAY)){
		$end = date("Y-m-d H:i:s", strtotime("+$day day", strtotime($expired))); //expired + $days
	}
	else {
		$end= date("Y-m-d H:i:s", strtotime("+$day day")); //expired + $days
	}
	$create = date_create($create);
	$end1 = date_create($end);
	$interval = date_diff($create, $end1);
	$days = (int)$interval->format("%a");
	$end = strtotime($end);
	$sql = $dbh->query("UPDATE amx_amxadmins SET expired = '$end' WHERE  steamid = '".$row['steamid']."'");
	if (!$sql){
		die("Произошла ошибка, обновите страницу и попробуйте обратиться к администратору.");
	}
}
//Удаляем всю инфу о бане (bid прописывается в password)
if ($type == 5){
	$sql = $dbh->query("DELETE FROM amx_bans WHERE bid = '".$row['pasword']."'");
	$sql = $dbh->query("DELETE FROM amx_bans_edit WHERE bid = '".$row['pasword']."'");
	$sql = $dbh->query("DELETE FROM amx_bans_log WHERE bid = '".$row['pasword']."'");
}
//Посылаем amx_reloadadmins спасибо xPaw
//Пока заглушка на unban чтобы не было ошибки
if ($type != 5){
$sql_server = $dbh->query("SELECT address FROM amx_serverinfo WHERE id='$server_id'");
	$row_server = $sql_server->fetch();
	$address = explode(":", $row_server['address']);
	
	define( 'SQ_SERVER_ADDR', $address[0] );
	define( 'SQ_SERVER_PORT', $address[1] );
	define( 'SQ_TIMEOUT',     1 );
	define( 'SQ_ENGINE',      SourceQuery :: GOLDSOURCE );
	
	$Query = new SourceQuery( );
		$Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );
		$Query->SetRconPassword($rcon);
			$Query->Rcon($cmd);
	$Query->Disconnect( );
}
$f=@fopen("order.txt","a+") or
          die("error");
fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date\n");
fclose($f);

?>


