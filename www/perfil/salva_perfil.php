<?php
session_start();

header('Content-Type: application/json');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado."]);
    exit();
}

$userId = $_SESSION['user_id'];
$nome = trim($_POST['nome'] ?? '');
$login = trim($_POST['login'] ?? '');
$fone = trim($_POST['fone'] ?? '');
$senhaAtual = trim($_POST['senhaAtual'] ?? '');
$novaSenha = trim($_POST['novaSenha'] ?? '');

try {
    $db = new SQLite3('../data/bibliotecario.db');

    // Processa a nova foto se enviada
    $novaFoto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoNome = basename($_FILES['foto']['name']);
        $fotoDestino = '../uploads/imagens/' . $fotoNome;

        // Move o arquivo enviado para o diretório de destino
        if (move_uploaded_file($fotoTmp, $fotoDestino)) {
            $novaFoto = $fotoNome;

            // Atualiza a foto no banco de dados
            $stmt = $db->prepare("UPDATE cad_usuario SET foto = :foto WHERE id = :id");
            $stmt->bindValue(':foto', $novaFoto, SQLITE3_TEXT);
            $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
            $stmt->execute();
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao salvar a foto."]);
            exit();
        }
    }

    // Apenas valida e atualiza a senha se os campos forem preenchidos
    if (!empty($senhaAtual) || !empty($novaSenha)) {
        $stmt = $db->prepare("SELECT senha FROM cad_usuario WHERE id = :id");
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if (!$user || !password_verify($senhaAtual, $user['senha'])) {
            echo json_encode(["status" => "error", "message" => "Senha atual incorreta."]);
            exit();
        }

        if (!empty($novaSenha)) {
            if (strlen($novaSenha) < 5) {
                echo json_encode(["status" => "error", "message" => "A nova senha deve ter pelo menos 5 caracteres."]);
                exit();
            }

            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE cad_usuario SET senha = :senha WHERE id = :id");
            $stmt->bindValue(':senha', $novaSenhaHash, SQLITE3_TEXT);
            $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
            $stmt->execute();
        }
    }

    // Atualiza outros dados do perfil
    $stmt = $db->prepare("UPDATE cad_usuario SET nome = :nome, login = :login, fone = :fone WHERE id = :id");
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':login', $login, SQLITE3_TEXT);
    $stmt->bindValue(':fone', $fone, SQLITE3_TEXT);
    $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Perfil atualizado com sucesso."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erro no sistema: " . $e->getMessage()]);
}
?>