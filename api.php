<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('UserController.php');
$controller = new UserController($connection);

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$request = explode('/', trim($requestPath, '/'));

$id = isset($request[1]) ? intval($request[1]) : null;

// Read JSON body (for POST/PUT/DELETE)
$data = json_decode(file_get_contents('php://input'), true);

// Allow method override for browsers that block PUT/DELETE
if ($method === 'POST' && isset($data['_method'])) {
    $method = strtoupper($data['_method']);
}

// Also check ?id= for DELETE
if (!$id && isset($_GET['id'])) {
    $id = intval($_GET['id']);
} elseif (!$id && isset($data['id'])) {
    $id = intval($data['id']);
}


switch ($method) {
    case 'GET':
        if ($id) {
            $controller->getUser($id);
        } else {
            $controller->getUsers();
        }
        break;

    case 'POST':
        $controller->createUser($data);
        break;

    case 'PUT':
        if ($id) {
            $controller->updateUser($id, $data);
        } else {
            echo json_encode(['error' => 'Missing ID for update']);
        }
        break;

    case 'DELETE':
        if ($id) {
            $controller->deleteUser($id, $data);
        } else {
            echo json_encode(['error' => 'Missing ID for delete']);
        }
        break;

    default:
        echo json_encode(['error' => 'Unsupported Method']);
        break;
}
?>
