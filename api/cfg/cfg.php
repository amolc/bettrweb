<?php
ini_set('display_errors',0);
ini_set('magic_quotes_gpc',1);
ini_set("session.cookie_httponly", 1);
session_start();
$_SESSION['generated'] = time();
$GLOBALS['debug_sql'] = array();
$sitedef = $_SERVER[ 'SERVER_NAME'];
$twitterlink="#";
$fblink="#";
$instagramlink="#";
$pintlink="#";

if ("localhost" == $sitedef || $sitedef=="192.168.1.3")
{
	$__dbhost = "localhost";
	$__dbname = "dbbettr";
	$__dbuser = "root";
	$__dbpass = "deep";
	define( 'HTTP_ROOT', '/' );
	define( 'DEBUG', false );
	$serverpath = "http://".$_SERVER['HTTP_HOST']."/fotolockr/";
	define( 'SERVERPATH', $serverpath );
	$innerpath = "http://".$_SERVER['HTTP_HOST']."/fotolockr/inner/";
	define( 'INNERPATH', $innerpath );
	$adminpath = $serverpath."cadmin/";
	define( 'ADMINPATH', $adminpath );
	$upload_path=$_SERVER['DOCUMENT_ROOT']."fotolockr/uploads/";
	setcookie("serverpath",$serverpath);
	$sitename="Bettr";
	$fbAppId="868634446484929";
	$fbAppSecret="850ee0702534e491fbe474b58aab0373";	
}
elseif ('apps.fountaintechies.com' == $sitedef)
{
	$__dbhost = "localhost";
	$__dbname = "dbbettr";
	$__dbuser = "bettr";
	$__dbpass = "7gXWOqeaf";
	define( 'HTTP_ROOT', '/' );
	define( 'DEBUG', false );
	$serverpath = "http://".$_SERVER['HTTP_HOST']."/bettr/";
	define( 'SERVERPATH', $serverpath );
	$innerpath = "http://".$_SERVER['HTTP_HOST']."/bettr/inner/";
	define( 'INNERPATH', $innerpath );
	$adminpath = $serverpath."cadmin/";
	define( 'ADMINPATH', $adminpath );
	$upload_path=$_SERVER['DOCUMENT_ROOT']."bettr/uploads/";
	setcookie("serverpath",$serverpath);
	$sitename="Bettr";
	}

db_connect();

// base functions to follow this line ###################################################################
function db_connect() {

	$srv = $GLOBALS['__dbhost'];
	$unm = $GLOBALS['__dbuser'];
	$pwd = $GLOBALS['__dbpass'];
	$db  = $GLOBALS['__dbname'];
	$GLOBALS['db_con'] = mysqli_connect($srv,$unm,$pwd,$db);
	return is_object($GLOBALS['db_con']);
}

function db_query($sql='',$type=0) {
	if (!is_object($GLOBALS['db_con']) || $sql == '') {
		die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Server Maintenance</title>
			</head>
			<body style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:14px;">
			The server is currently under maintenance.<br /><br />
			Please check back later.
			</body>
			</html>
		');

		exit;
	 } else {

 	$result = mysqli_query($GLOBALS['db_con'],$sql);
		if (eregi('^insert.*',$sql) || eregi('^update.*',$sql) || eregi('^delete.*',$sql)) {
				$num_rows = mysqli_affected_rows($GLOBALS['db_con']);
		 } else {

				$num_rows = mysqli_num_rows($result);
		}

		if ($type == 0 || $type == 4) {  // return results & num of rows

	if ($num_rows) {
				if ($type == 0) {
					while ($row = mysqli_fetch_array($result)) {
						if (count($row) > 1) {
							$rows[] = $row;

						 } else {

							$rows[] = $row[0];
						}
					}
				 } else {

					while ($row = mysqli_fetch_row($result)) {
						if (count($row) > 1) {
								$rows[] = $row;

						 } else {

								$rows[] = $row[0];

						}
					}

				}
				$return_val = array(0=>true,'rows'=>$rows,'count'=>$num_rows);
			 } else {
				$return_val = array(0=>false,'count'=>0);
		}
		 } elseif ($type == 1) {  // return num of rows
				$return_val = $num_rows;
		 } elseif ($type == 3) {  // return last_insert_id
				$return_val = mysqli_insert_id($GLOBALS['db_con']);

		 } 
		 elseif($type==5)
		 {
			 $return_val=mysqli_affected_rows($GLOBALS['db_con']);
		 }
		 else {
				$return_val = $num_rows;
		}
		// clean up my result set, eh?
		@mysqli_free_result($result);
	}
	if (mysqli_error($GLOBALS['db_con'])) {
		// there was an error, add it to the global array
		$GLOBALS['debug_sql'][] = array('PROBLEM SQL',$sql,mysqli_error($GLOBALS['db_con']));
	}
	return $return_val;
}
function db_close() {
        return mysqli_close($GLOBALS['db_con']);
}
function mysql_res($string='')
{
	// shorthand mysql_real_escape_string
	return mysqli_real_escape_string($GLOBALS['db_con'],$string);
}
function encrypt_str($str){
	$str=md5(md5(md5(md5($str))));
return $str;
}
function filter_text($str)
{
	$str=ltrim(rtrim($str));
	$str=strip_tags($str);
	$str=addslashes($str);
	

	return $str;
}
function filter_rich_text($str)
{
	$str=addslashes(ltrim(rtrim($str)));
	return $str;
}
function valid_pass($pwd) {
   
		$error = "ok";
		if(strlen($pwd) < 6)//to short
		{
			$error = "Error. Password must be 6-12 characters long";
		}
		if(strlen($pwd) > 12)//to long
		{
			$error = "Error. Password must be 6-12 characters long";
		}	
		if(!preg_match("#[0-9]+#", $pwd))//at least one number
		{
			$error = "Error. Password must contain at least one number.";
		}
		if(!preg_match("#[a-z]+#", $pwd))//at least one letter
		{
			$error = "Error. Password must contain at least one small case letter";
		}
		if(!preg_match("#[A-Z]+#", $pwd))//at least one capital letter
		{
			$error = "Error. Password must contain at least one upper case letter";
		}
		if(preg_match('![^a-z0-9]!i', $pwd))//at least one symbol
		{
			$error = "Error. Speial characters are not allowed.";
		}
		return $error;

}

?>