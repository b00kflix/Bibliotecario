<?php
session_start();

header('Content-Type: application/json');

// Verificar se os dados do formulário foram enviados
if (!isset($_POST['login']) || !isset($_POST['senha'])) {
    echo json_encode(["status" => "error", "message" => "Preencha todos os campos."]);
    exit();
}

// Capturar e limpar os dados do formulário
$login = trim($_POST['login']);
$senha = trim($_POST['senha']);

try {
    // Conexão com o banco de dados
    $db = new SQLite3('../../data/bibliotecario.db');

    // Consulta para buscar o usuário pelo login
    $stmt = $db->prepare("SELECT id, senha, nivel FROM cad_usuario WHERE login = :login");
    $stmt->bindValue(':login', $login, SQLITE3_TEXT);
    $result = $stmt->execute();

    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        // Verificar se a senha está correta
        if (password_verify($senha, $user['senha'])) {
            // Login bem-sucedido, criar sessão
            $_SESSION['user_id'] = $user['id'];

            // Redirecionar com base no nível do usuário
            if ($user['nivel'] == "1") { // Administrador
                echo json_encode([
                    "status" => "success",
                    "message" => "Login realizado com sucesso.",
                    "redirect" => "admin/painel.php"
                ]);
            } elseif ($user['nivel'] == "2") { // Usuário
                echo json_encode([
                    "status" => "success",
                    "message" => "Login realizado com sucesso.",
                    "redirect" => "usuarios/painel.php"
                ]);
            } else { // Caso o nível não seja reconhecido
                echo json_encode([
                    "status" => "error",
                    "message" => "Nível de usuário não reconhecido."
                ]);
            }
        } else {
            // Senha incorreta
            echo json_encode(["status" => "error", "message" => "Senha incorreta."]);
        }
    } else {
        // Login não encontrado
        echo json_encode(["status" => "error", "message" => "Login não encontrado."]);
    }
} catch (Exception $e) {
    // Tratar exceções e registrar erros
    error_log("Erro no login: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Erro no sistema."]);
}
?>