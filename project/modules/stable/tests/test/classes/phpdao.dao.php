<?php
/**
 * 
 */

/**
 * 
 */
class DAOPHPDao {
	private static $_var = null;

	public function get (){
		return self::$_var;
	}
	
	public function update ($record){
	}

	public function delete (){
	}
	
	public function insert ($record){
		self::$_vars = $record;
	}

	public function findBy (CopixDAOSearchParams $pSp, $pLeftJoin = array ()){
		return $this->get ();
	}
	
	public function deleteBy (CopixDAOSearchParams $sp){
		self::$_vars= null;		
	}
	
	public function countBy (CopixDAOSearchParams $sp){
		return isset (self::$_vars) ?  1 : 0;
	}
	
	public function findAll (){
		return array (self::$_vars);
	}
}

class DAORecordPhpDao {
	public $id;
	public $caption;
}