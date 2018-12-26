<?php
error_reporting(E_ALL ^ E_DEPRECATED);

class DBController {
	private $host = "mysql-jeptags-db.c8emo1b3l7by.us-west-1.rds.amazonaws.com";
	private $user = "root";
	private $password = "jep1234321";
	private $database = "jeptags_encoding";
	
	/*private $host = "localhost";
	private $user = "apps_bilalUS";
	private $password = ")_9)K&4wB56P";
	private $database = "apps_bilalDB";*/
	
	function __construct() {
		$conn = $this->connectDB();
		if(!empty($conn)) {
			$this->selectDB($conn);
		}
	}
	
	function connectDB() {
		$conn = mysql_connect($this->host,$this->user,$this->password);
		mysql_set_charset('utf8',$conn);
		return $conn;
	}
	
	function selectDB($conn) {
		mysql_select_db($this->database, $conn);
		
	}
	
	function runQuery($query) {
		$result = mysql_query($query);
		while($row=mysql_fetch_assoc($result)) {
			$resultset[] = $row;
			
		}		
		if(!empty($resultset))
			return $resultset;
	}
	
	
	function numRows($query) {
		$result  = mysql_query($query);
		$rowcount = mysql_num_rows($result);
		return $rowcount;	
	}
	
	function updateQuery($query) {
		$result = mysql_query($query);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		} else {
			return $result;
		}
	}
	
	function insertQuery($query) {
		$result = mysql_query($query);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		} else {
			return $result;
		}
	}
	
	function deleteQuery($query) {
		$result = mysql_query($query);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		} else {
			return $result;
		}
	}
	
	function generateRandomString($length = 20) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}
?>