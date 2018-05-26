<?php
require_once "../../vendor/autoload.php";
require_once "PersistanceManager.class.php";
require_once "Config.class.php";
require_once "InstagramScrapper.php";

/* Register custom classes */
Flight::register("db", "PersistanceManager", array(Config::DB));
Flight::register("instagram", "InstagramScrapper", array("https://www.instagram.com/flink_home/"));

/* Set routes */
Flight::route("GET /db/products", function() {
    Flight::json(Flight::db()->get_basic_product_info());
});

Flight::route("GET /db/categories", function() {
    Flight::json(Flight::db()->get_categories());
});

Flight::route("GET /db/products/@id", function($id) {
    Flight::json(Flight::db()->get_detailed_product_info($id));
});

Flight::route("GET /db/coupon/@coupon", function($coupon) {
    $coupon = Flight::db()->check_coupon($coupon);
    if ($coupon) {
        Flight::json($coupon);
    } else {
        Flight::halt(404, Flight::json(array("status" => "Not found.")));
    }
    
});

Flight::route("GET /db/find/@category/@range", function($category, $range) {
    Flight::json(Flight::db()->get_products_via_category($category, $range));
});

Flight::route("GET /scrape/ig", function() {
    Flight::json(Flight::instagram()->get_json_data());
});

Flight::start();
?>