<?php
include_once("config.php");

class MySQLDB {
	var $db;
	
	public function __construct(){
		$this->db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8',
						DB_USER,
						DB_PASS,
						array(PDO::ATTR_EMULATE_PREPARES => false,
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
		
	}
	
	function addContact($name,$lastname,$phones,$addresses,$emails,$webs,$notes,$update,$uid){
		if($update=='yes'){
			$stmt = $this->db->prepare("UPDATE sab_people set name=:a,apedillos=:b,tfnos=:c,addresses=:d,emails=:e,webs=:f,notas=:g WHERE id=:id");
			$stmt->execute(array(':a'=>$name, ':b'=>$lastname, ':c'=>$phones, ':d'=>$addresses, ':e'=>$emails, ':f'=>$webs, ':g'=>$notes, ':id'=>$uid));
			if($stmt){
				return "OK";
			} else {
				return "KO";
			}
		} else {
			$stmtb = $this->db->prepare("INSERT INTO sab_people(name,apedillos,tfnos,addresses,emails,webs,notas) VALUES (:a, :b, :c, :d, :e, :f, :g)");
			$stmtb->execute(array(':a'=>$name, ':b'=>$lastname, ':c'=>$phones, ':d'=>$addresses, ':e'=>$emails, ':f'=>$webs, ':g'=>$notes));
			if($stmtb){
				return $this->db->lastInsertId();
			} else {
				return "KO";
			}
		}
	}
	function delContact($uid){
		$stmtb = $this->db->prepare("DELETE from sab_people where id=:id");
		$stmtb->execute(array(':id'=>$uid));
		if($stmtb){
			return "OK";
		} else {
			return "KO";
		}
	}
	function getContacts($page){
		$page = $page - 1;
		$stmt2 = $this->db->prepare("select TABLE_ROWS from information_schema.TABLES where TABLE_SCHEMA = 'AB' AND table_name='sab_people'");
		$stmt2->execute();
		$amount = $stmt2->fetch();
		$amount = $amount['TABLE_ROWS'];

		$page1 = $page * CONTACTS_PER_SHEET;
		$page2 = $page1 + CONTACTS_PER_SHEET;
		//$stmt = $this->db->prepare("select *, (select TABLE_ROWS from information_schema.TABLES where TABLE_SCHEMA = 'AB' AND table_name='sab_people') as CNT from sab_people order by name LIMIT $page1,$page2");
		$stmt = $this->db->prepare("select * from sab_people order by name LIMIT $page1,$page2");
		$stmt->execute();
		return array($amount, $stmt->fetchAll(PDO::FETCH_ASSOC));
	}
	
	function search($term,$page){
		$page = $page - 1;
		if($term=='*' || $term==' '|| $term=='%') return $this->getContacts(1);
		$term = "%$term%";
		$stmt2 = $this->db->prepare("SELECT COUNT(*) FROM `sab_people` WHERE name LIKE :a OR apedillos LIKE :b OR tfnos LIKE :c OR emails LIKE :d OR addresses LIKE :e OR webs LIKE :f OR notas LIKE :g");
		$stmt2->execute(array(':a'=>$term,':b'=>$term,':c'=>$term,':d'=>$term,':e'=>$term,':f'=>$term,':g'=>$term));
		$amount = $stmt2->fetch()[0];

		$page1 = $page * CONTACTS_PER_SHEET;
		$page2 = $page1 + CONTACTS_PER_SHEET;
		$stmt = $this->db->prepare("SELECT * FROM `sab_people` WHERE name LIKE :a OR apedillos LIKE :b OR tfnos LIKE :c OR emails LIKE :d OR addresses LIKE :e OR webs LIKE :f OR notas LIKE :g order by name LIMIT $page1,$page2");
		$stmt->execute(array(':a'=>$term,':b'=>$term,':c'=>$term,':d'=>$term,':e'=>$term,':f'=>$term,':g'=>$term));
		return array($amount, $stmt->fetchAll(PDO::FETCH_ASSOC));
	}

};

$db = new MySQLDB;
?>
