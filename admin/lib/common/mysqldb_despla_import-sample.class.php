<?php
//class DBConn {
class MysqlDb {

	private $conn;
	private $host_db;
	private $user_db;
	private $password_db;
	private $db_name;
	private $mysqli = 0;
	private $local = 0;
	private static $instance;

	private function __construct() {

		$this->host_db = "localhost";

        $this->user_db = "sissh";
        $this->password_db = "mjuiokm";
        $this->db_name = "ocha_sissh_despla_import";

		if ($this->mysqli == 0){
			$this->conn = mysql_connect($this->host_db,$this->user_db,$this->password_db);
			mysql_select_db($this->db_name);
		}
		else{
			$this->conn = mysqli_connect($this->host_db,$this->user_db,$this->password_db,$this->db_name);
		}

	}

	// Singleton Design Pattern
	public static function getInstance(){

		if (!isset(self::$instance))	self::$instance = new self;

		return self::$instance;
	}

	public function OpenRecordset($sql,$db_name=''){

		if ($db_name != '')	mysql_select_db($db_name);
		
		if ($this->mysqli == 0){
			$Result = mysql_query($sql,$this->conn);
		}
		else{
			mysqli_real_query($this->conn,$sql);
			$Result = mysqli_use_result($this->conn);
		}

		//$this->Query_time(true);

		if (!$Result) {
			
			if ($this->local == 1){
				echo "Query no vlido: ". $sql. mysql_error();
			}
			else{
				
				echo "Error en consulta";
				$from = "rojas@un-ocha.org";
				
				require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");
				
				$mail = new PHPMailer();
				
				$mail->IsSMTP(); // set mailer to use SMTP
				$mail->Username = "rojas";  // SMTP username
				$mail->Password = "ruben"; // SMTP password
				
				$mail->From = $from;
				$mail->FromName = "Rubas";
				$mail->AddAddress($from, "Fideo");
				
				$mail->WordWrap = 50;                                 // set word wrap to 50 characters
				$mail->IsHTML(true);                                  // set email format to HTML
				
				$mail->Subject = "Error consulta SI OCHA";
				$mail->Body    = $sql."<br>ID USURIO = ".$_SESSION["id_usuario_s"].'<br>url: '.$_SERVER['REQUEST_URI'];
				//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
				
				$mail->Send();
			}

			//$this->CloseConnection();
			die;
		}
		else {
			return $Result;
		}
	}

	public function RowCount($Result) {
		if ($Result) {
			return mysql_num_rows($Result);
		}
		else {
			return 0;
		}
	}

	public function FetchObject($Result) {
		//return mysql_fetch_object($Result);
		return ($this->mysqli == 0) ? mysql_fetch_object($Result) : mysqli_fetch_object($Result);
	}

	public function FetchRow($Result) {
		//return mysql_fetch_row($Result);
		return ($this->mysqli == 0) ? mysql_fetch_row($Result) : mysqli_fetch_row($Result);
	}

	public function FetchAssoc($Result) {
		return mysql_fetch_assoc($Result);
	}

	public function Execute($query,$db_name='') {
		if ($db_name != '')	mysql_select_db($db_name);

		if (!mysql_query($query)){
			$message  = '<br /><b>Invalid query: ' . mysql_error() ."</b>";
    		$message .= '<br /><b>Whole query: ' . $query."</b>";
			
			die($message);
		}

	}

	public function GetGeneratedID() {
		return mysql_insert_id($this->conn);
	}

	public function FreeMemory($Result) {
		mysql_free_result($Result);
	}

}

?>
