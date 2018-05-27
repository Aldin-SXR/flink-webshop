<?php
require_once "../../vendor/autoload.php";
require_once "PersistanceManager.class.php";
require_once "Config.class.php";
require_once "InstagramScrapper.php";
require_once "Contact.php";

use \Firebase\JWT\JWT;

/* Register custom classes */
Flight::register("db", "PersistanceManager", array(Config::DB));
Flight::register("instagram", "InstagramScrapper", array("https://www.instagram.com/flink_home/"));
Flight::register("contact", "Contact", array("aldin.kovacevic.97@gmail.com"));

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

Flight::route("GET /db/find/@category/@range", function($category, $range) {
    Flight::json(Flight::db()->get_products_via_category($category, $range));
});

Flight::route("GET /scrape/ig", function() {
    Flight::json(Flight::instagram()->get_json_data());
});

Flight::route("GET /db/coupon/@coupon", function($coupon) {
    $coupon = Flight::db()->check_coupon($coupon);
    if ($coupon) {
        Flight::json($coupon);
    } else {
        Flight::halt(404, Flight::json(array("status" => "Not found.")));
    }
});

/* Add new user to user database */
Flight::route("POST /users/add", function() {
    $user = Flight::request()->data->getData();
    $response = Flight::db()->add_new_user($user);
    if ($response["status"] == "success") {
        Flight::json($response);
    } else {
        Flight::halt(400, Flight::json($response));
    }
});

/* Add new user to user database */
Flight::route("POST /users/login", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::db()->validate_user($data);
    /* if a valid user was found, authenticate with JWT token */
    if ($response["status"] == "success") {
        unset($response["password"]);
        unset($response["activation_hash"]);
        unset($response["activated"]);
        unset($response["status"]);
        $token = array("user" => $response, "iat" => time(), "exp" => time() + 60);
        $jwt = JWT::encode($token, Config::JWT_SECRET);
        $response["token"] = $jwt;

        Flight::json($response);
    } else if ($response["status"] == "not_activated") {
        Flight::halt(401, Flight::json($response));
    } else {
        Flight::halt(400, Flight::json($response));
    }
});

/* Handle incoming contact message */
Flight::route("POST /contact", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::contact()->relay_contact_message($data);
    if ($response["status"] == "success") {
        Flight::json($response);
    } else {
        Flight::halt(400, Flight::json($response));
    }
});


Flight::start();
?>