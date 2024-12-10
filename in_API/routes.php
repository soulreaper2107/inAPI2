<?php

// Import necessary files
require_once "c:/xampp/htdocs/inapi/config/database.php";
require_once "c:/xampp/htdocs/inapi/modules/auth.php";
require_once "c:/xampp/htdocs/inapi/modules/post.php";
require_once "c:/xampp/htdocs/inapi/modules/get.php";
require_once "c:/xampp/htdocs/inapi/modules/patch.php";
require_once "c:/xampp/htdocs/inapi/modules/delete.php";
require_once "c:/xampp/htdocs/inapi/modules/common.php";

$db = new Connection();
$pdo = $db->connect();

// Instantiate post, get, patch, delete, and auth classes
$post = new Post($pdo);
$patch = new Patch($pdo);
$get = new Get($pdo);
$delete = new Delete($pdo);
$auth = new Authentication($pdo);

// Retrieve and split endpoints
if (isset($_REQUEST['request'])) {
    $request = explode("/", $_REQUEST['request']);
} else {
    echo "URL does not exist.";
}

// Handle GET, POST, PATCH, DELETE requests
switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if ($auth->isAuthorized()) {
            switch ($request[0]) {
                case "users":
                    $dataString = json_encode($get->getUsers($request[1] ?? null));
                    echo $dataString;
                    break;
                
                case "products":
                    $dataString = json_encode($get->getProducts($request[1] ?? null));
                    echo $dataString;
                    break;

                case "sellers":
                    $dataString = json_encode($get->getSellers($request[1] ?? null));
                    echo $dataString;
                    break;

                default:
                    http_response_code(401);
                    echo "This is an invalid endpoint";
                    break;
            }
        } else {
            http_response_code(401);
        }
        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"), true);
        switch ($request[0]) {
            case "login":
                echo json_encode($auth->login($body));
                break;

            case "user":
                echo json_encode($auth->addAccount($body));
                break;

            case "products":
                echo json_encode($post->postProduct($body));
                break;

            case "sellers":
                echo json_encode($post->postSeller($body));
                break;

            default:
                http_response_code(401);
                echo "This is an invalid endpoint";
                break;
        }
        break;

    case "PATCH":
        $body = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            case "users":
                echo json_encode($patch->patchUser($body, $request[1]));
                break;

            default:
                http_response_code(401);
                echo "This is an invalid endpoint";
                break;
        }
        break;

    case "DELETE":
        switch ($request[0]) {
            case "users":
                echo json_encode($delete->deleteUser($request[1]));
                break;

            case "products":
                echo json_encode($delete->deleteProduct($request[1]));
                break;

            case "sellers":
                echo json_encode($delete->deleteSeller($request[1]));
                break;

            default:
                http_response_code(401);
                echo "This is an invalid endpoint";
                break;
        }
        break;

    default:
        http_response_code(400);
        echo "Invalid Request Method.";
        break;
}

$pdo = null;
?>
