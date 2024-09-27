<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'conexion.php';

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $username = $data->username;
    $password = md5($data->password);

    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM users WHERE username = :username AND password = :password";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = generateToken();

        // Actualizar el token en la base de datos
        $updateToken = "UPDATE users SET token = :token WHERE id = :id";
        $stmtToken = $db->prepare($updateToken);
        $stmtToken->bindParam(":token", $token);
        $stmtToken->bindParam(":id", $user['id']);
        $stmtToken->execute();

        echo json_encode([
            "message" => "Login exitoso",
            "token" => $token
        ]);
    } else {
        echo json_encode(["message" => "Credenciales incorrectas"]);
    }
}
?>
