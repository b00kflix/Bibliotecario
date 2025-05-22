<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
} else {
    // Cria a conexão com o banco de dados SQLite3
    $db = new SQLite3('../../data/bibliotecario.db'); // Certifique-se de que o arquivo do banco de dados existe e está no caminho correto

    $id_usuario = $_SESSION['user_id'];
}

$id = intval($_GET['id']); // Certifique-se de que $id é um número inteiro

// Consulta os dados do usuário na tabela cad_usuario
$result = $db->query("SELECT * FROM cad_usuario WHERE id='$id'");
$row = $result->fetchArray(SQLITE3_ASSOC);

$nome = htmlspecialchars($row['nome'] ?? '');
$email = htmlspecialchars($row['email'] ?? '');
$turma = htmlspecialchars($row['turma'] ?? '');
$fone = htmlspecialchars($row['fone'] ?? '');
$login = htmlspecialchars($row['login'] ?? '');
$nivel = htmlspecialchars($row['nivel'] ?? '');
?>
<br>
<script src="../arquivos/vendors/parsleyjs/dist/parsley.min.js"></script>
<!-- Inclua o arquivo de idioma do Parsley para português -->
<script src="../arquivos/vendors/parsleyjs/dist/i18n/pt-br.js"></script>
<br>
<h3>Editar Cadastro de Usuário</h3>
<hr>
<form id="cadastroForm" enctype="multipart/form-data" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">

<div class="row">
    <div class="col-lg-12">    
        <div class="form-group">
            <label for="Nome">Nome</label>
            <input type="text" style="border: 1px solid #C0C0C0;" required class="form-control" id="nome" name="nome" placeholder="Nome" value="<?php echo $nome; ?>">
        </div>
    </div>
    <div class="col-lg-8">    
        <div class="form-group">
            <label for="Email">Email</label>
            <input type="email" style="border: 1px solid #C0C0C0;" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
        </div>
    </div>
    <div class="col-lg-4">    
        <div class="form-group">
            <label for="Fone">Fone (WhatsApp)</label>
            <input type="text" style="border: 1px solid #C0C0C0;" class="form-control" id="fone" name="fone" placeholder="(00) 00000-0000" value="<?php echo $fone; ?>">
        </div>
    </div>
    <div class="col-lg-4">    
        <div class="form-group">
            <label for="Nível">Nível</label>
            <select class="form-control" id="nivel" name="nivel" required style="border: 1px solid #C0C0C0;">
                <option value="">Selecione...</option>
                <option value="1" <?php echo ($nivel == '1') ? 'selected' : ''; ?>>Administrador(a)</option>
                <option value="2" <?php echo ($nivel == '2') ? 'selected' : ''; ?>>Usuário</option>                        
            </select>
        </div>
    </div>
    <div class="col-lg-4">    
        <div class="form-group">
            <label for="Turma">Turma/Setor</label>
            <select class="form-control" id="turma" name="turma" style="border: 1px solid #C0C0C0;">
                <option value="">Selecione...</option>
                <?php
                // Consulta todos os registros da tabela cad_turma
                $result = $db->query("SELECT * FROM cad_turma");
                while ($turma_row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $id_turma = htmlspecialchars($turma_row['id']);
                    $nome_turma = htmlspecialchars($turma_row['nome']);
                    $selected = ($id_turma == $turma) ? 'selected' : '';
                    echo "<option value='$id_turma' $selected>$nome_turma</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="Login">Login</label>
            <input type="text" style="border: 1px solid #C0C0C0;" class="form-control" id="login" name="login" placeholder="Login" value="<?php echo $login; ?>">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="NovaSenha">Nova Senha (opcional)</label>
            <input type="password" style="border: 1px solid #C0C0C0;" class="form-control" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para manter a senha atual">
        </div>
    </div>
</div>

<div class="">
    <div class="form-group">
        <br><br>
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <button type="submit" class="btn btn-primary me-2">Salvar Alterações</button>
        <button type="button" class="btn btn-light" id="cancelButton">Cancelar</button>
    </div>
</div>

</form>
<script>
    $(document).ready(function() {
        // Máscara para o campo de telefone
        $('#fone').mask('(00) 00000-0000');

        // Submissão do formulário
        $('#cadastroForm').on('submit', function(e) {
            e.preventDefault(); // Evita o envio padrão do formulário

            var formData = new FormData(this);

            $.ajax({
                url: 'cad_usuarios/salva_editar_usuario.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            $("#conteudo").load("cad_usuarios/painel.php");
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao salvar os dados. Tente novamente!'
                    });
                }
            });
        });

        // Cancelar edição e recarregar a tabela
        $('#cancelButton').on('click', function() {
            $('#cadastroForm')[0].reset();
            $('#tabela').load("cad_usuarios/tabela.php");
        });
    });
</script>