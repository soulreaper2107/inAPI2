<?php

class Common {

    // Logger to write logs to a file
    protected function logger($user, $method, $action) {
        $filename = date("Y-m-d") . ".log";
        $datetime = date("Y-m-d H:i:s");
        $logMessage = "$datetime, $method, $user, $action" . PHP_EOL;
        error_log($logMessage, 3, "./logs/$filename"); // Appends log messages to the file
    }

    // Generates SQL string for INSERT statements
    private function generateInsertString($tableName, $body) {
        $keys = array_keys($body);
        $fields = implode(",", $keys);
        $parameterArray = array_fill(0, count($keys), "?");
        $parameters = implode(",", $parameterArray);
        return "INSERT INTO $tableName ($fields) VALUES ($parameters)";
    }

    // Retrieve data from a table based on condition (using prepared statements)
    protected function getDataByTable($tableName, $condition, \PDO $pdo) {
        $sqlString = "SELECT * FROM $tableName WHERE $condition";
        $data = [];
        $errmsg = "";
        $code = 0;

        try {
            $stmt = $pdo->prepare($sqlString);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($data) {
                $code = 200;
                return ["code" => $code, "data" => $data];
            } else {
                $errmsg = "No data found.";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }

        return ["code" => $code, "errmsg" => $errmsg];
    }

    // Generate a structured response
    protected function generateResponse($data, $remark, $message, $statusCode) {
        $status = [
            "remark" => $remark,
            "message" => $message
        ];

        http_response_code($statusCode);

        return [
            "payload" => $data,
            "status" => $status,
            "prepared_by" => "Ryu, Robert",
            "date_generated" => date("Y-m-d H:i:s")
        ];
    }

    // Insert data into a table
    public function postData($tableName, $body, \PDO $pdo) {
        $values = array_values($body);
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = $this->generateInsertString($tableName, $body);
            $stmt = $pdo->prepare($sqlString);
            $stmt->execute($values);

            // Retrieve the last inserted ID for auto-incremented columns
            $lastInsertId = $pdo->lastInsertId();

            $code = 200;
            return ["data" => ["lastInsertId" => $lastInsertId], "code" => $code];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }

    // Update a JSON column in a table (e.g., for carts or productavl)
    public function updateJsonColumn($tableName, $column, $condition, $newData, \PDO $pdo) {
        $sqlString = "UPDATE $tableName SET $column = :data WHERE $condition";
        $errmsg = "";
        $code = 0;

        try {
            $stmt = $pdo->prepare($sqlString);
            $stmt->execute(['data' => json_encode($newData)]);

            $code = 200;
            return ["code" => $code, "message" => "Successfully updated."];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }
}

?>
