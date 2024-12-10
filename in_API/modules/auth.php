<?php
class Authentication {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function isAuthorized() {
        // Compare request token to db token
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        return $this->getToken() === $headers['authorization'];
    }

    private function getToken() {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);

        $sqlString = "SELECT token FROM accounts_tbl WHERE username = ?";
        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$headers['x-auth-user']]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['token'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return "";
    }

    private function generateHeader() {
        $header = [
            "typ" => "JWT",
            "alg" => "HS256",
            "app" => "E-Commerce API",
            "dev" => "Ryu Pascua, Robert Inocencio"
        ];
        return base64_encode(json_encode($header));
    }

    private function generatePayload($userid, $username) {
        $payload = [
            "uid" => $userid,
            "username" => $username,
            "email" => "202312287@gordoncollege.edu.ph",
            "date" => date("Y-m-d H:i:s"),
            "exp" => date("Y-m-d H:i:s", strtotime("+1 hour"))
        ];
        return base64_encode(json_encode($payload));
    }

    private function generateToken($userid, $username) {
        $header = $this->generateHeader();
        $payload = $this->generatePayload($userid, $username);
        $signature = hash_hmac("sha256", "$header.$payload", TOKEN_KEY);
        return "$header.$payload." . base64_encode($signature);
    }

    private function isSamePassword($inputPassword, $existingHash) {
        return password_verify($inputPassword, $existingHash);
    }

    private function encryptPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function saveToken($token, $username) {
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = "UPDATE accounts_tbl SET token = ? WHERE username = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute([$token, $username]);

            $code = 200;
            $data = null;

            return ["data" => $data, "code" => $code];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }

    public function login($body) {
        $username = $body->username;
        $password = $body->password;

        $code = 0;
        $payload = "";
        $remarks = "";
        $message = "";

        try {
            $sqlString = "SELECT id, username, password, token FROM accounts_tbl WHERE username = ?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($this->isSamePassword($password, $result['password'])) {
                    $code = 200;
                    $remarks = "success";
                    $message = "Logged in successfully";

                    $token = $this->generateToken($result['id'], $result['username']);
                    $token_arr = explode('.', $token);
                    $this->saveToken($token_arr[2], $result['username']);
                    $payload = ["id" => $result['id'], "username" => $result['username'], "token" => $token_arr[2]];
                } else {
                    $code = 401;
                    $payload = null;
                    $remarks = "failed";
                    $message = "Incorrect Password.";
                }
            } else {
                $code = 401;
                $payload = null;
                $remarks = "failed";
                $message = "Username does not exist.";
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $remarks = "failed";
            $code = 400;
        }
        return ["payload" => $payload, "remarks" => $remarks, "message" => $message, "code" => $code];
    }

    public function addAccount($body) {
        $values = [];
        $errmsg = "";
        $code = 0;

        $body->password = $this->encryptPassword($body->password);

        foreach ($body as $value) {
            array_push($values, $value);
        }

        try {
            $sqlString = "INSERT INTO accounts_tbl (username, password, email) VALUES (?, ?, ?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $code = 200;
            $data = null;

            return ["data" => $data, "code" => $code];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }
}
?>
