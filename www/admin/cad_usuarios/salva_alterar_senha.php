<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado."]);
    exit();
}

if (!isset($_POST['senhaAtual']) || !isset($_POST['novaSenha'])) {
    echo json_encode(["status" => "error", "message" => "Preencha todos os campos."]);
    exit();
}

$senhaAtual = trim($_POST['senhaAtual']);
$novaSenha = trim($_POST['novaSenha']);

try {
    $db = new SQLite3('../../data/bibliotecario.db');
    $userId = $_SESSION['user_id'];

    // Consulta a senha atual do usuário
    $stmt = $db->prepare("SELECT senha FROM cad_usuario WHERE id = :id");
    $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($senhaAtual, $user['senha'])) {
        if (strlen($novaSenha) < 8) {
            echo json_encode(["status" => "error", "message" => "A nova senha deve ter pelo menos 8 caracteres."]);
            exit();
        }

        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualiza a senha no banco de dados
        $stmt = $db->prepare("UPDATE cad_usuario SET senha = :senha WHERE id = :id");
        $stmt->bindValue(':senha', $novaSenhaHash, SQLITE3_TEXT);
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Senha alterada com sucesso."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Senha atual incorreta."]);
    }
} catch (Exception $e) {
    error_log("Erro ao alterar senha: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Erro no sistema."]);
}
?>