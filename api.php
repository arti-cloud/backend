<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('UserController.php');
$controller = new UserController($connection);

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST' && isset($data['_method'])) {
    $method = strtoupper($data['_method']);
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$request = explode('/', trim($requestPath, '/'));
$id = isset($request[1]) ? intval($request[1]) : null;

if (!$id && isset($_GET['id'])) {
    $id = intval($_GET['id']);
} elseif (!$id && isset($data['id'])) {
    $id = intval($data['id']);
}

if (in_array($method, ['POST', 'PUT']) && isset($data['email'])) {
    $email = $data['email'];
    $userId = $id ?? 0;

    $stmt = $connection->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409); 
        echo json_encode(['error' => 'Email already exists.']);
        exit;
    }
    $stmt->close();
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
