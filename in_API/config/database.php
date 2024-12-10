<?php
// Metadata
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-User");

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Auth-User");
    header("HTTP/1.1 200 OK");
    die();
}

date_default_timezone_set("Asia/Manila");

// Database Configuration
define("SERVER", "localhost");
define("DBASE", "inapi_db");
define("USER", "root");
define("PWORD", "");

// Security Configuration
define("TOKEN_KEY", "12E1561FB866FE9D966538F2125A5"); // Used for JWT token generation
define("SECRET_KEY", "Your_secret_key"); // Can be used for additional cryptographic purposes

class Connection {
    protected $connectionString;
    protected $options;

    public function __construct() {
        $this->connectionString = "mysql:host=" . SERVER . ";dbname=" . DBASE . ";charset=utf8";
        $this->options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false
        ];
    }

    /**
     * Establishes a connection to the database.
     * @return \PDO The PDO instance for database interaction.
     */
    public function connect() {
        try {
            return new \PDO($this->connectionString, USER, PWORD, $this->options);
        } catch (\PDOException $e) {
            echo json_encode([
                "code" => 500,
                "message" => "Database connection error: " . $e->getMessage()
            ]);
            die();
        }
    }
}
?>
