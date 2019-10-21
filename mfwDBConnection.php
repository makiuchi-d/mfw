<?php

/**
 * DB Connection (PDO)
 */
class mfwDBConnection {

	const DEFAULT_DBNAME = 'default_master';
	protected static $con_pool = array();

	public static function getPDO($name=null)
	{
		if($name===null){
			$name = self::DEFAULT_DBNAME;
		}
		$env = mfwServerEnv::getEnv();
		if(!isset(self::$con_pool[$env][$name])){
			$conf = mfwServerEnv::databaseSetting($name);
			$pdo = new PDO(
				$conf['dsn'],$conf['user'],$conf['pass'],
				array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					)
				);
			self::$con_pool[$env][$name] = $pdo;
		}
		return self::$con_pool[$env][$name];
	}

	public static function disconnect()
	{
		self::$con_pool = array();
	}

}

