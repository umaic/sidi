<?php
class PgDBConn {

	var $conn;
	var $host_db;
	var $user_db;
	var $password_db;
	var $db_name;
	var $port_db;


	function PgDBConn () {

		$this->host_db = "localhost";

		$this->user_db = "";
		$this->password_db = "";
		$this->db_name = "";
		$this->port_db = "5432";
		$this->conn = pg_connect("host='$this->host_db' port='$this->port_db' user='$this->user_db' password='$this->password_db' dbname='$this->db_name'");
	}

	function OpenRecordset($sql,$db_name=''){

		//if ($db_name != '')	mysql_select_db($db_name);
		
		$Result = pg_query($this->conn,$sql);

		if (!$Result) {
			echo "Query no vlido: ". $sql. pg_last_error();
			//echo "Error en consulta";
			/*$from = "rojas@un-ocha.org";

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
			*/

			$this->CloseConnection();
			die;
		}
		else {
			return $Result;
		}
	}

	function RowCount($Result) {
		if ($Result) {
			return pg_num_rows($Result);
		}
		else {
			return 0;
		}
	}

	function FetchObject($Result) {
		return pg_fetch_object($Result);
	}

	function FetchRow($Result) {
		return pg_fetch_row($Result);
	}

	function FetchAssoc($Result) {
		return pg_fetch_assoc($Result);
	}

	function Execute($Query,$db_name='') {
		//if ($db_name != '')	mysql_select_db($db_name);
		pg_query($Query);
	}

	function GetGeneratedID() {
		return pg_last_id($this->conn);
	}

	function FreeMemory($Result) {
		mysql_free_result($Result);
	}

	function CloseConnection() {
		pg_close($this->conn);
	}
}

?>
