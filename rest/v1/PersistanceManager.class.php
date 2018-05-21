<?php
class PersistanceManager {
    private $pdo;
	/* PDO constructor */
	public function __construct($params) {
		$dsn = "mysql:host=".$params["host"].";dbname=".$params["db"].";charset=".$params["charset"];
		$opt = array(
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false
		);
		$this->pdo = new PDO($dsn, $params["user"], $params["pass"], $opt);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/* Get basic product info from database */
	public function get_basic_product_info() {
		$stmt = $this->pdo->query("SELECT product_name, product_price, product_picture, status FROM products;");
		return $stmt->fetchAll();
	}
}
?>