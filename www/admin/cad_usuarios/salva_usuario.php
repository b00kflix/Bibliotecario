<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado."]);
    exit();
}

$db = new SQLite3('../../data/bibliotecario.db');

try {
    if (!isset($_POST['nome']) || empty(trim($_POST['nome'])) ||
        !isset($_POST['nivel']) || empty(trim($_POST['nivel'])) ||
        !isset($_POST['senha']) || empty(trim($_POST['senha']))) {
        echo json_encode(["status" => "error", "message" => "Os campos Nome, Nível e Senha são obrigatórios."]);
        exit();
    }

    $nome = trim($_POST['nome']);
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $fone = isset($_POST['fone']) ? trim($_POST['fone']) : null;
    $nivel = intval($_POST['nivel']);
    $turma = isset($_POST['turma']) ? trim($_POST['turma']) : null;
    $senha = trim($_POST['senha']);

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "O e-mail informado é inválido."]);
        exit();
    }

    $primeiro_nome = strtolower(explode(' ', $nome)[0]);
    $login_gerado = '';

    do {
        $numero_aleatorio = rand(1000, 9999);
        $login_gerado = $primeiro_nome . $numero_aleatorio;

        $stmt = $db->prepare("SELECT COUNT(*) as total FROM cad_usuario WHERE login = :login");
        $stmt->bindValue(':login', $login_gerado, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
    } while ($row['total'] > 0);

    // Hash da senha
    $hash_senha = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        "INSERT INTO cad_usuario (nome, email, fone, nivel, turma, login, senha) VALUES (:nome, :email, :fone, :nivel, :turma, :login, :senha)"
    );
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':fone', $fone, SQLITE3_TEXT);
    $stmt->bindValue(':nivel', $nivel, SQLITE3_INTEGER);
    $stmt->bindValue(':turma', $turma, SQLITE3_TEXT);
    $stmt->bindValue(':login', $login_gerado, SQLITE3_TEXT);
    $stmt->bindValue(':senha', $hash_senha, SQLITE3_TEXT);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Usuário cadastrado com sucesso. Login gerado: $login_gerado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao cadastrar o usuário."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Erro: " . $e->getMessage()]);
} finally {
    $db->close();
}
?>