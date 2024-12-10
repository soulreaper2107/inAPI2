<?php
require_once "c:/xampp/htdocs/inapi/modules/auth.php";
include_once "Common.php";

class Get extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Retrieve logs for a specific date
    public function getLogs($date) {
        $filename = "./logs/" . $date . ".log";
        $logs = [];
        try {
            $file = new SplFileObject($filename);
            while (!$file->eof()) {
                $logs[] = trim($file->fgets());
            }
            $remarks = "success";
            $message = "Successfully retrieved logs.";
        } catch (Exception $e) {
            $remarks = "failed";
            $message = $e->getMessage();
        }

        return $this->generateResponse(["logs" => $logs], $remarks, $message, 200);
    }

    // Retrieve user(s) data
    public function getUsers($userId = null) {
        $condition = "1=1"; // Default condition to retrieve all users
        if ($userId !== null) {
            $condition .= " AND userid=" . intval($userId);
        }

        $result = $this->getDataByTable('user_tbl', $condition, $this->pdo);
        if ($result['code'] == 200) {
            return $this->generateResponse($result['data'], "success", "Successfully retrieved user records.", $result['code']);
        }
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Retrieve seller(s) data
    public function getSellers($sellerId = null) {
        $condition = "1=1"; // Default condition to retrieve all sellers
        if ($sellerId !== null) {
            $condition .= " AND sellerid=" . intval($sellerId);
        }

        $result = $this->getDataByTable('seller_tbl', $condition, $this->pdo);
        if ($result['code'] == 200) {
            return $this->generateResponse($result['data'], "success", "Successfully retrieved seller records.", $result['code']);
        }
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Retrieve product(s) data
    public function getProducts($productId = null) {
        $condition = "1=1"; // Default condition to retrieve all products
        if ($productId !== null) {
            $condition .= " AND productid=" . intval($productId);
        }

        $result = $this->getDataByTable('products_tbl', $condition, $this->pdo);
        if ($result['code'] == 200) {
            return $this->generateResponse($result['data'], "success", "Successfully retrieved product records.", $result['code']);
        }
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
    }

    // Retrieve a user's cart
    public function getCart($userId) {
        $condition = "userid=" . intval($userId);

        $result = $this->getDataByTable('user_tbl', $condition, $this->pdo);
        if ($result['code'] == 200) {
            $cart = $result['data'][0]['carts'];
            $cartItems = json_decode($cart, true);

            // If cart is not empty, retrieve product details
            if (!empty($cartItems)) {
                $productIds = implode(",", $cartItems);
                $products = $this->getDataByTable("products_tbl", "productid IN ($productIds)", $this->pdo);

                if ($products['code'] == 200) {
                    return $this->generateResponse($products['data'], "success", "Successfully retrieved cart items.", 200);
                }
                return $this->generateResponse(null, "failed", "Failed to retrieve cart item details.", 404);
            }

            return $this->generateResponse([], "success", "Cart is empty.", 200);
        }

        return $this->generateResponse(null, "failed", "User not found.", 404);
    }
}

?>

