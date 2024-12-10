<?php
require_once "c:/xampp/htdocs/inapi/modules/auth.php";
include_once "Common.php";

class Patch extends Common {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Update a user's data
    public function patchUser($body, $userId) {
        $values = [];
        foreach ($body as $value) {
            $values[] = $value;
        }
        $values[] = intval($userId); // Append user ID for the WHERE clause

        try {
            $sqlString = "UPDATE user_tbl SET username=?, password=?, email=?, carts=? WHERE userid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $this->logger("User Update", "PATCH", "Updated user with ID $userId");
            return $this->generateResponse(null, "success", "User updated successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Update a seller's data
    public function patchSeller($body, $sellerId) {
        $values = [];
        foreach ($body as $value) {
            $values[] = $value;
        }
        $values[] = intval($sellerId); // Append seller ID for the WHERE clause

        try {
            $sqlString = "UPDATE seller_tbl SET username=?, email=?, password=?, productavl=? WHERE sellerid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $this->logger("Seller Update", "PATCH", "Updated seller with ID $sellerId");
            return $this->generateResponse(null, "success", "Seller updated successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Update a product's data
    public function patchProduct($body, $productId) {
        $values = [];
        foreach ($body as $value) {
            $values[] = $value;
        }
        $values[] = intval($productId); // Append product ID for the WHERE clause

        try {
            $sqlString = "UPDATE products_tbl SET productname=?, productprize=?, productowner=? WHERE productid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $this->logger("Product Update", "PATCH", "Updated product with ID $productId");
            return $this->generateResponse(null, "success", "Product updated successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Archive (soft delete) a user
    public function archiveUser($userId) {
        try {
            $sqlString = "UPDATE user_tbl SET isdeleted=1 WHERE userid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($userId)]);

            $this->logger("User Archive", "PATCH", "Archived user with ID $userId");
            return $this->generateResponse(null, "success", "User archived successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Archive (soft delete) a seller
    public function archiveSeller($sellerId) {
        try {
            $sqlString = "UPDATE seller_tbl SET isdeleted=1 WHERE sellerid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($sellerId)]);

            $this->logger("Seller Archive", "PATCH", "Archived seller with ID $sellerId");
            return $this->generateResponse(null, "success", "Seller archived successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }

    // Archive (soft delete) a product
    public function archiveProduct($productId) {
        try {
            $sqlString = "UPDATE products_tbl SET isdeleted=1 WHERE productid=?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([intval($productId)]);

            $this->logger("Product Archive", "PATCH", "Archived product with ID $productId");
            return $this->generateResponse(null, "success", "Product archived successfully.", 200);
        } catch (\PDOException $e) {
            return $this->generateResponse(null, "failed", $e->getMessage(), 400);
        }
    }
}

?>
