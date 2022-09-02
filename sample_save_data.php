<?php

$arrFXT = array();
$arrFirstRow = array();
$arrSingleFXT = array();

$version_name = $_POST['version_name'];
$version_name = stripslashes($version_name);
$version_name = trim(urldecode($version_name));

$sn = $_POST['sn'];
$sn = stripslashes($sn);
$sn = urldecode($sn);
//echo $sn."<BR><BR>";
$arrFXT =  json_decode($sn,true);
$arrFirstRow = $arrFXT[0]; 
$id = $arrFXT[0]['id'];
$area = $arrFXT[0]['area'];

//print_r($arrFXT);

// ##### get max(ver_num) from log and add 1 #####

$con=mysqli_connect("localhost","my_user","my_password","my_db");
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

mysqli_query($con,"SET CHARACTER SET 'utf8'");
mysqli_query($con,"SET SESSION collation_connection ='utf8_unicode_ci'");

mysqli_set_charset($con,"utf8"); ////////// important ////////

$area = mysqli_real_escape_string($con, trim($area));

mysqli_query("LOCK TABLES log WRITE, item WRITE; "); // Lock the table so that there won't be duplicate number for ver_num

$sql = "SELECT MAX( ver_num )+1 AS `new_ver_num` FROM `history` WHERE id = '".$id."' AND  area = '".$area."';";

//echo $sql;
//echo "<br><br>";

$result = mysqli_query($con,$sql);
//echo mysqli_num_rows($result);
//echo "<br><br>";

if (mysqli_num_rows($result)==0) { // if there's no record
	$new_ver_num = 1;
}
elseif (mysqli_num_rows($result)>0) { // if there's a record
	$row = mysqli_fetch_row($result);	
	$new_ver_num = $row[0];
    if (!is_numeric($new_ver_num)) {
		$new_ver_num = 1;
    } 
}
else {
	$new_ver_num = 1;
}
//echo "<br><br>";

//echo "new_ver_num=".$new_ver_num."<br><br>";

// ##### build sql texts to put data into item and history #####

$sql =  "INSERT INTO `item_log` ("; 

//echo $sql;
//echo "<br><br>";

$sql_3 =  "INSERT INTO `item` ("; 

//mysql_query("set session character_set_connection=utf8;");
//mysql_query("set session character_set_results=utf8;");
//mysql_query("set session character_set_client=utf8;");
//mysql_query("SET sql_mode='';");

if (mysqli_query($con, $sql)) 
{
    echo "Fixture log successfully inserted! " . mysqli_affected_rows($con) . " rows\n\n";
	if (mysqli_query($con, $sql_3)) 
	{
		echo "Fixture data successfully updated! " . mysqli_affected_rows($con) . " rows\n\n";
	} else 
	{
		echo "Error occurred while updating fixture data: " . mysqli_error($con) . "\n\n";
	}
} else 
{
    echo "Error occurred while inserting fixture log: " . mysqli_error($con) . "\n\n";
}

mysqli_query("UNLOCK TABLES;");
mysqli_close($con);

?>
