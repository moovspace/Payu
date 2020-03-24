<?php
declare(strict_types=1);
namespace Payu\Db;

use \PDO;
use \Exception;
use Payu\Config; // change here for yours path

final class Db extends Config
{
	public $Pdo = null;

	// Singleton
	public static function GetInstance(): self
	{
		static $instance;

		if (null === $instance) {
			$instance = new self();
		}

		return $instance;
	}

	private function __construct()
	{
		// Connet to database
		$this->Pdo = self::Conn();
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	final static function Conn()
	{
		try
		{
			$con = new PDO('mysql:host='.self::MYSQL_HOST.';port='.self::MYSQL_PORT.';dbname='.self::MYSQL_DBNAME.';charset=utf8mb4', self::MYSQL_USER, self::MYSQL_PASS);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$con->setAttribute(PDO::ATTR_PERSISTENT, true);
			$con->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
			$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return $con;
		}
		catch(Exception $e)
		{
			echo 'Connection failed: ' . $e->getMessage ();
			// print_r($e->errorInfo());
			return null;
		}
	}
}

// // Mysql from static method (Db class)
// $db = PaymentDb::getInstance();
// $rows = $db->Pdo->query('select * from `users`')->fetchAll();
?>
