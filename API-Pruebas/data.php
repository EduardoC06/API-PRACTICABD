<?php
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';

    if ($token) {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM users WHERE token = :token";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Acceso autorizado", "data" => "Datos seguros aquí"]);
        } else {
            echo json_encode(["message" => "Token inválido"]);
        }
    } else {
        echo json_encode(["message" => "Falta el token de autorización"]);
    }
}
?>
