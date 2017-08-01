<?php
//class DBConn {
class MysqlDb {

	private $conn;
	private $host_db;
	private $user_db;
	private $password_db;
	private $db_name;
	private $mysqli = 1;
	private $local = 0;
	private static $instance;

	private function __construct() {

		$this->host_db = "localhost";

        $this->user_db = "";
        $this->password_db = "";
        $this->db_name = "";

		if ($this->mysqli == 0){
			$this->conn = mysql_connect($this->host_db,$this->user_db,$this->password_db);
			mysql_select_db($this->db_name);
		}
		else{
			$this->conn = mysqli_connect($this->host_db,$this->user_db,$this->password_db,$this->db_name);
			mysqli_set_charset( $this->conn, 'utf8');

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
            
            if ($db_name != '')	mysql_select_db($db_name);
            
            $Result = mysql_query($sql,$this->conn);
		}
		else{
			//mysqli_real_query($this->conn,$sql);
            
            if ($db_name != '')	mysqli_select_db($db_name);
            
            $Result = mysqli_query($this->conn, $sql);
		}

		//$this->Query_time(true);

		if (empty($Result)) {
			
			if ($this->local == 0){
				//echo "Query no vlido: ". $sql. mysql_error();
			}
			else{
				
				echo "Error en consulta";
				$from = "";
				
				require($_SERVER['DOCUMENT_ROOT']."/sissh/admin/lib/common/class.phpmailer.php");
				
				$mail = new PHPMailer();
				
				$mail->IsSMTP(); // set mailer to use SMTP
				$mail->Username = "";  // SMTP username
				$mail->Password = ""; // SMTP password
				
				$mail->From = $from;
				$mail->FromName = "";
				$mail->AddAddress($from, "");
				
				$mail->WordWrap = 50;                                 // set word wrap to 50 characters
				$mail->IsHTML(true);                                  // set email format to HTML
				
				$mail->Subject = "Error consulta SI OCHA";
				$mail->Body    = $sql."<br>ID USURIO = ".$_SESSION["id_usuario_s"].'<br>url: '.$_SERVER['REQUEST_URI'];
				//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
				
				//$mail->Send();
			}

			//$this->CloseConnection();
			//die;
            return false;
		}
		else {
			return $Result;
		}
	}

	public function RowCount($Result) {
		if ($Result) {
            return ($this->mysqli == 0) ? mysql_num_rows($Result) : mysqli_num_rows($Result);
		}
		else {
			return 0;
		}
	}

	public function FetchObject($Result) {
		//return mysql_fetch_object($Result);
		if (!empty($Result)) {
            return ($this->mysqli == 0) ? mysql_fetch_object($Result) : mysqli_fetch_object($Result);
        } 
	}

	public function FetchRow($Result) {
		//return mysql_fetch_row($Result);
		if (!empty($Result)) {
            return ($this->mysqli == 0) ? mysql_fetch_row($Result) : mysqli_fetch_row($Result);
        }
	}

	public function FetchAssoc($Result) {
		if (!empty($Result)) {
			return ($this->mysqli == 0) ? mysql_fetch_assoc($Result) :  mysqli_fetch_assoc($Result);
		}
	}

    public function Execute($query,$db_name='') {

        if ($this->mysqli == 0){
            if ($db_name != '')	mysql_select_db($db_name);
            
            if (!mysql_query($query)){
                $message  = '<br /><b>Invalid query: ' . mysql_error() ."</b>";
                $message .= '<br /><b>Whole query: ' . $query."</b>";
                
                die($message);
            }
        }
        else{
            
            if ($db_name != '')	mysqli_select_db($db_name);
            
            if (!mysqli_query($this->conn,$query)){
                $message  = '<br /><b>Invalid query: ' . mysqli_error($this->conn) ."</b>";
                $message .= '<br /><b>Whole query: ' . $query."</b>";
                
                die($message);
            }
        }
	}

	public function GetGeneratedID() {
		return ($this->mysqli == 0) ?  mysql_insert_id($this->conn) :  mysqli_insert_id($this->conn);
	}

	public function FreeMemory($Result) {
		return ($this->mysqli == 0) ?  mysql_free_result($Result) :  mysqli_free_result($Result);
	}

}

?>
