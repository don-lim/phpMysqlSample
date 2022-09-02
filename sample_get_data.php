<?

if (trim($_SERVER['QUERY_STRING'])) {
//echo $_SERVER['QUERY_STRING'];

	$id = trim($_GET['fc']);
	$area = trim($_GET['area']);
	$ver_num = trim($_GET['v']);
	$mode = trim($_GET['m']);
	$sample = trim($_GET['sample']);
	$list = array();

	$con=mysqli_connect("localhost""my_user","my_password","my_db");
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	mysqli_query($con,"SET CHARACTER SET 'utf8'");
	mysqli_query($con,"SET SESSION collation_connection ='utf8_unicode_ci'");

		
//		$sql = "SHOW FIELDS FROM your_table_1 WHERE FIELD NOT IN ('version_name','ver_num');";
	$sql = "SHOW FIELDS FROM your_table_1 WHERE FIELD NOT IN ('version_name');";
	$result = mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result)) {
		array_push($list, $row['Field']);
	}
	$fields = implode(',',$list); // array with all fields but 'version_name'

	$list = array();
	$sql = "SELECT $fields FROM your_table_1 WHERE id = '".$id."' AND year = '".$area."' AND ver_num = '".$ver_num."' ORDER BY LENGTH(sn), sn ASC";
	$result = mysqli_query($con,$sql);
	while($row = mysqli_fetch_assoc($result)) {
		array_push($list, $row);
	}
	$json_result = json_text(json_encode($list));
	echo $json_result ;	

	
	$sql = "SELECT * FROM your_table_2 WHERE ver_num = (SELECT MAX(ver_num) FROM your_table_2 WHERE id = '".$id."' AND year = '".$area."') AND id = '".$id."' AND year = '".$area."' ORDER BY LENGTH(sn), sn ASC";

//	echo $sql;

	$result = mysqli_query($con,$sql);
	$arrTemp = array();
	if (mysqli_num_rows($result)==0) { // if there's no record, 
	
	}
	else { // regular mode
		while($row = mysqli_fetch_assoc($result)) {
			array_push($list, $row);
		}
		$json_result = json_text(json_encode($list, JSON_PARTIAL_OUTPUT_ON_ERROR));
		echo $json_result ;
	}

	$sql = "SHOW FIELDS FROM your_table_2;";
	$result = mysqli_query($con,$sql);
	$i = 0;
	$value = null;
	while($row = mysqli_fetch_assoc($result)) {
		$key = $row['Field'];
		if($key=="f_name") {		$value = "Sample Item"; }
		else if($key=="id") {	$value = $id; }
		else if($key=="year") {	$value = $area; }
		else {					$value = null; } // replace the data with empty value
//				$arrTemp += array($i => $value); // data associated with integer 
		$arrTemp += array($row['Field'] => $value); // data with key
		++$i;
	}
//	print_r($arrTemp);
	array_push($list, $arrTemp);
	$json_result = json_text(json_encode($list));
	echo $json_result ;

	mysqli_close($con);

} else {
	echo "Parameter missing!";
}

function json_text($str){ // 
	$str =  str_replace("\r","\\r", $str);
	$str =  str_replace("\n","\\n",$str);
	return $str;
}
