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
		$stmt = $this->pdo->query("SELECT id, product_name, product_price, product_picture, status FROM products;");
		return $stmt->fetchAll();
	}

	/* Get available categories from database */
	public function get_categories() {
		$stmt = $this->pdo->query("SELECT * FROM categories;");
		return $stmt->fetchAll();
	}

	/* Get detailed product info from database */
	public function get_detailed_product_info($id) {
		$stmt = $this->pdo->prepare("SELECT p.*, c.category_name AS product_category  FROM products AS p INNER JOIN 
															categories AS c ON p.product_category = c.id WHERE p.id = :id;");
		$stmt->execute(array("id" => $id));
		return $stmt->fetch();
	}

	/* Get detailed product info from database */
	public function check_coupon($coupon) {
		$stmt = $this->pdo->prepare("SELECT * FROM coupons WHERE coupon_value = :coupon_value");
		$stmt->execute(array("coupon_value" => $coupon));
		return $stmt->fetch();
	}

	/* Get products based on category */
	public function get_products_via_category($category, $range) {
		$query = "SELECT p.id, product_name, product_price, product_picture, status FROM products AS p INNER JOIN
						categories AS c ON p.product_category = c.id";
		/* if both price range and category were chosen */
		if ($range != "all" && $category != "all") {
			$range = explode("&", $range);
			$query .= " WHERE c.category_name LIKE :category AND p.product_price BETWEEN :start AND :end;";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute(array(
				"category" => $category,
				"start" => $range[0],
				"end" => $range[1]	
			));
		/* if a range was chosen */
		} else if ($range != "all" && $category == "all") {
			$range = explode("&", $range);
			$query .= " WHERE p.product_price BETWEEN :start AND :end;";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute(array(
				"start" => $range[0],
				"end" => $range[1]	
			));
		/* if a category was chosen */
		} else if ($range == "all" && $category != "all") {
			$range = array(0, 200);
			$query .= " WHERE c.category_name LIKE :category;";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute(array(
				"category" => $category
			));
		/* if nothing was chosen */
		} else {
			$stmt = $this->pdo->prepare($query);
			$stmt->execute();
		}
		
		return $stmt->fetchAll();
	}

	public function add_new_user($user) {
        /* try for duplicate entities */
        try {
            /* reset autoincrement on every try to avoid skipped indices */
            $this->pdo->query("ALTER TABLE users AUTO_INCREMENT = 1");
            /* prepare and execute the statement */
            $stmt = $this->pdo->prepare("INSERT INTO users (user_name, email, password, country, address, zipcode, activation_hash, activated)
                                                                VALUES (:user_name, :email, :password, :country, :address, :zipcode, :activation_hash, :activated);");
            $activation_hash = md5(((string)rand(0, 1000)).$user["email"]);
            $stmt->execute(array(
            "user_name" => $user["username"],
            "email" => $user["email"],
			"password" => password_hash($user["password"], PASSWORD_DEFAULT),
			"country" => ($user["country"] == "Select country:") ? NULL : $user["country"],
			"address" => $user["address"],
			"zipcode" => $user["zipcode"],
            "activation_hash" => $activation_hash,
            "activated" => 0 
            ));
            /* send activation mail */
            Mailer::mail($user["email"], $activation_hash, $user["user_name"], $user["password"]);
            return array("status" => "success");
        } catch (Exception $e) {
            /* this error code signifies duplicate entry */
            if ($e->errorInfo[1] == 1062)
                return array("status" => "duplicate");
            else 
                return array("status" => "error");
        }
	}
	
}
?>