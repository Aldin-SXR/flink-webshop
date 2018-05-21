<?php
require_once "../../vendor/autoload.php";
require_once "PersistanceManager.class.php";
require_once "Config.class.php";

/* Register custom classes */
Flight::register("db", "PersistanceManager", array(Config::DB));

/* Set routes */
Flight::route("GET /db/products", function() {
    Flight::json(Flight::db()->get_basic_product_info());
});

Flight::start();
?>