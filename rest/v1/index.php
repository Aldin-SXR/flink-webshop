<?php
require_once "../../vendor/autoload.php";
require_once "PersistanceManager.class.php";
require_once "Config.class.php";
require_once "InstagramScrapper.php";
require_once "Contact.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \Firebase\JWT\JWT;

/* Register custom classes */
Flight::register("db", "PersistanceManager", array(Config::DB));
Flight::register("instagram", "InstagramScrapper", array("https://www.instagram.com/flink_home/"));
Flight::register("contact", "Contact", array("aldin.kovacevic.97@gmail.com"));

/**
 * Filtering
 */
Flight::before("start", function(&$params, &$output) {
    /* authorize for all routes containing the word 'private' */
    if (strpos(Flight::request()->url, "private") !== false) {
        $jwt = getallheaders()["Flink-Web-Auth-JWT"];
        try {
            $decoded_token = (array)JWT::decode($jwt, Config::JWT_SECRET, array("HS256"));
            $decoded_token["user"] = (array)$decoded_token["user"];
            Flight::set("id", $decoded_token["user"]["id"]);
        } catch (Exception $e) {
            Flight::clear("id");
            Flight::halt(401, Flight::json(array("status" => "unauthorized")));
            die;
        }
    }
});

/* Set routes */
Flight::route("GET /db/products", function() {
    Flight::json(Flight::db()->get_basic_product_info());
});

Flight::route("GET /db/categories", function() {
    Flight::json(Flight::db()->get_categories());
});

Flight::route("GET /db/countries", function() {
    Flight::json(Flight::db()->get_countries_and_rates());
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
        if ($coupon["expired"] == "1") {
            Flight::halt(401, Flight::json(array("status" => "expired")));
        } else {
            Flight::json($coupon);
        }
    } else {
        Flight::halt(404, Flight::json(array("status" => "Not found.")));
    }
});

Flight::route("POST /order/new", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::db()->place_order($data);
    if ($response["status"] == "success") {
        Flight::json($response);
    } else {
        Flight::halt(400, Flight::json($response));
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
        $token = array("user" => $response, "iat" => time(), "exp" => time() + 2592000);
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

Flight::route("POST /private/db/carts", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::db()->save_cart($data, Flight::get("id"));
    if ($response["status"] == "success") {
        Flight::json($response);
    } else {
        Flight::halt(400, Flight::json($response));
    }
});

Flight::route("GET /private/db/carts", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::db()->load_cart(Flight::get("id"));
    if (isset($response["status"])) {
        Flight::halt(400, Flight::json($response));
    } else {
        Flight::json($response);
    }
});

/* (Un)subscribe to newsletter */
Flight::route("GET /private/subscribe", function() {
    $data = Flight::request()->data->getData();
    $response = Flight::db()->subscribe_to_newsletter(Flight::get("id"));
    if ($response["status"] == "success") {
        Flight::json($response);
    } else {
        Flight::halt(400, Flight::json($response));
    }
});

/* Account activation landing page */
Flight::route("GET /users/verify/*", function() {
    /* check if email and hash have been properly set */
    if (isset(Flight::request()->query["email"]) && isset(Flight::request()->query["hash"])) {
        $result = Flight::db()->activate_user(Flight::request()->query->getData());
        if ($result["status"] == "success") {
            Flight::render("landing.php", array(
                "title" => "Successful registration",
                "status" => "<div class='card-header card-header-success text-center'>",
                "body" => "Congratulations, your account has been successfully verified and activated. <br>
                We thank you for your interesting in Flink products. <hr>
                <small>Flink team</small>"
            ));
        } else if ($result["status"] == "already_activated") {
            Flight::render("landing.php", array(
                "title" => "Re-attempted activation",
                "status" => "<div class='card-header card-header-warning text-center'>",
                "body" => "Your account has already been previously verified and activated. <hr>
                <small>Flink team</small>"
            ));
        } else if ($result["status"] == "tampered_with") {
            Flight::render("landing.php", array(
                "title" => "Activation code manipulation",
                "status" => "<div class='card-header card-header-error text-center'>",
                "body" => "Activation code is incorrect. Possible outside manipulation. <hr>
                <small>Flink team</small>"
            ));
        } else if ($result["status"] == "email_incorrect") {
            Flight::render("landing.php", array(
                "title" => "Incorrect email address",
                "status" => "<div class='card-header card-header-danger text-center'>",
                "body" => "The entered email address is incorrect. Possible outside manipulation. <hr>
                <small>Flink team</small>"
            ));
        }
    }
});

Flight::start();
?>