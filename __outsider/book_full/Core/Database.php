<?php
namespace Outsider\Book\Core;

use PDO;

class Database
{
	private static ?PDO $pdo = null;

	public static function get(): PDO
	{
		if (self::$pdo === null) {
			self::$pdo = new PDO(
				"mysql:host=localhost;dbname=tilkg_srv;charset=utf8",
				"tilkg_adm",
				"tl76dc8fJrjdK0cAGZ",
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				]
			);
		}
		return self::$pdo;
	}

	// public static function get(): PDO
	// {
	// 	if (self::$pdo === null) {
	// 		self::$pdo = new PDO(
	// 			"mysql:host=176.126.165.65;dbname=user154975_db;charset=utf8",
	// 			"user154975_admin",
	// 			"tl76dc8fJrjdK0cAGZ",
	// 			[
	// 				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	// 				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	// 			]
	// 		);
	// 	}
	// 	return self::$pdo;
	// }

	// public static function get(): PDO
	// {
	// 	if (self::$pdo === null) {
	// 		self::$pdo = new PDO(
	// 			"mysql:host=localhost;dbname=cc90219_isra;charset=utf8;time_zone='+06:00'",
	// 			"cc90219_isra",
	// 			"7dZSvT6n",
	// 			[
	// 				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	// 				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	// 			]
	// 		);
	// 	}
	// 	return self::$pdo;
	// }
}
