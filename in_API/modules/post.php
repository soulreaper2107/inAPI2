<?php
require_once "c:/xampp/htdocs/inapi/modules/auth.php";
include_once "Common.php";

class Post extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Post a new user
    public function postUser($body) {
        $result = $this->postData("user_tbl", $body, $this->pdo);
        if ($result['code'] == 200) {
            $this->logger($body['username'], "POST", "Created a new user record");
            return $this->generateResponse($result['data'], "success", "Successfully created a new user.", $result['code']);
        }
        $this->logger("UnknownUser", "POST", $result['errmsg']);
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Post a new seller
    public function postSeller($body) {
        $result = $this->postData("seller_tbl", $body, $this->pdo);
        if ($result['code'] == 200) {
            $this->logger($body['username'], "POST", "Created a new seller record");
            return $this->generateResponse($result['data'], "success", "Successfully created a new seller.", $result['code']);
        }
        $this->logger("UnknownSeller", "POST", $result['errmsg']);
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Post a new product
    public function postProduct($body) {
        // Insert product into products_tbl
        $result = $this->postData("products_tbl", $body, $this->pdo);
        if ($result['code'] == 200) {
            // Update the seller's product availability
            $this->updateSellerProducts($body['productowner'], $result['data']['lastInsertId']);
            $this->logger($body['productowner'], "POST", "Created a new product record");
            return $this->generateResponse($result['data'], "success", "Successfully created a new product.", $result['code']);
        }
        $this->logger("UnknownSeller", "POST", $result['errmsg']);
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Update seller's product availability
    private function updateSellerProducts($sellerUsername, $productId) {
        // Get current product availability
        $sellerData = $this->getDataByTable("seller_tbl", "username = '$sellerUsername'", $this->pdo);
        if ($sellerData['code'] == 200) {
            $productAvl = json_decode($sellerData['data'][0]['productavl'], true) ?? [];
            $productAvl[] = $productId;

            // Update the JSON column with the new product ID
            $this->updateJsonColumn("seller_tbl", "productavl", "username = '$sellerUsername'", $productAvl, $this->pdo);
        }
    }

    // Post a product to a user's cart
    public function postToCart($userId, $productId) {
        // Get current cart for the user
        $userData = $this->getDataByTable("user_tbl", "userid = $userId", $this->pdo);
        if ($userData['code'] == 200) {
            $cart = json_decode($userData['data'][0]['carts'], true) ?? [];
            $cart[] = $productId;

            // Update the JSON column with the new product ID
            $this->updateJsonColumn("user_tbl", "carts", "userid = $userId", $cart, $this->pdo);
            $this->logger($userId, "POST", "Added product $productId to cart");
            return $this->generateResponse(null, "success", "Successfully added product to cart.", 200);
        }
        return $this->generateResponse(null, "failed", "User not found or unable to update cart.", 404);
    }
}

?>
