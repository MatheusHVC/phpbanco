<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'sql10.freesqldatabase.com';
$dbname = 'u55sql10752201';
$username = 'sql10752201';
$password = ' St6kSGi89s';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Conexão falhou: ' . $e->getMessage()]);
    exit;
}

// Recebe os dados do POST
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validação básica
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Todos os campos são obrigatórios');
        }

        // Verifica se o email já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->rowCount() > 0) {
            throw new Exception('Email já cadastrado');
        }

        // Hash da senha
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insere o novo usuário
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $hashedPassword
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Usuário registrado com sucesso'
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>