<?php
require_once "c:/xampp/htdocs/inapi/modules/auth.php";
include_once "Common.php";

class Delete extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Permanently delete a user
    public function deleteUser($userId) {
        try {
            $sqlString = "DELETE FROM user_tbl WHERE userid = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($userId)]);

            $this->logger("User Delete", "DELETE", "Deleted user with ID $userId");
            return $this->generateResponse(null, "success", "User deleted successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Permanently delete a seller
    public function deleteSeller($sellerId) {
        try {
            $sqlString = "DELETE FROM seller_tbl WHERE sellerid = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($sellerId)]);

            $this->logger("Seller Delete", "DELETE", "Deleted seller with ID $sellerId");
            return $this->generateResponse(null, "success", "Seller deleted successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Permanently delete a product
    public function deleteProduct($productId) {
        try {
            $sqlString = "DELETE FROM products_tbl WHERE productid = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($productId)]);

            $this->logger("Product Delete", "DELETE", "Deleted product with ID $productId");
            return $this->generateResponse(null, "success", "Product deleted successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }
}
?>
