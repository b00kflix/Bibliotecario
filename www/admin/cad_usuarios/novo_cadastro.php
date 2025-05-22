<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
} else {
    $db = new SQLite3('../../data/bibliotecario.db');
    $id_usuario = $_SESSION['user_id'];
}
?>

<br>
<script src="../arquivos/vendors/parsleyjs/dist/parsley.min.js"></script>
<script src="../arquivos/vendors/parsleyjs/dist/i18n/pt-br.js"></script>
<br>
<h3>Novo Cadastro de Usuário</h3><hr>
<form id="cadastroForm" enctype="multipart/form-data" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
    <div class="row">
        <div class="col-lg-12">    
            <div class="form-group">
                <label for="Nome">Nome</label>
                <input type="text" style="border: 1px solid #C0C0C0;" required class="form-control" id="nome" name="nome" placeholder="Nome">
            </div>
        </div>
        <div class="col-lg-8">    
            <div class="form-group">
                <label for="Email">Email</label>
                <input type="email" style="border: 1px solid #C0C0C0;" class="form-control" id="email" name="email" placeholder="Email">
            </div>
        </div>
        <div class="col-lg-4">    
            <div class="form-group">
                <label for="Fone">Fone (WhatsApp)</label>
                <input type="text" style="border: 1px solid #C0C0C0;" class="form-control" id="fone" name="fone" placeholder="(00) 00000-0000">
            </div>
        </div>
        <div class="col-lg-6">    
            <div class="form-group">
                <label for="Nível">Nível</label>
                <select class="form-control" id="nivel" name="nivel" required style="border: 1px solid #C0C0C0;">
                    <option value="">Selecione...</option>
                    <option value="1">Administrador(a)</option>
                    <option value="2">Usuário</option>                        
                </select>
            </div>
        </div>
        <div class="col-lg-6">    
            <div class="form-group">
                <label for="Senha">Senha</label>
                <input type="password" style="border: 1px solid #C0C0C0;" class="form-control" id="senha" name="senha" placeholder="Senha" required>
            </div>
        </div>
        <div class="col-lg-6">    
            <div class="form-group">
                <label for="Turma">Turma/Setor</label>
                <select class="form-control" id="turma" name="turma" style="border: 1px solid #C0C0C0;">
                    <option value="">Selecione...</option>
                    <?php
                    $mysqli = new SQLite3('../../data/bibliotecario.db');
                    $result = $mysqli->query("SELECT * FROM cad_turma");
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $id_turma = htmlspecialchars($row['id']);
                        $nome = htmlspecialchars($row['nome']);
                        echo "<option value='$id_turma'>$nome</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="">
        <div class="form-group">
            <br><br>
            <button type="submit" class="btn btn-primary me-2">Submit</button>
            <button type="button" class="btn btn-light" id="cancelButton">Cancelar</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('#fone').mask('(00) 00000-0000');

        $('#cadastroForm').on('submit', function(e) {
            e.preventDefault(); 

            var formData = new FormData(this);

            $.ajax({
                url: 'cad_usuarios/salva_usuario.php',
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
                            text: response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao salvar os dados!',
                        text: 'Ocorreu um erro ao tentar salvar os dados. Tente novamente.',
                    });
                }
            });
        });

        $('#cancelButton').on('click', function() {
            $('#cadastroForm')[0].reset();
            $('#tabela').load("cad_usuarios/tabela.php");
        });
    });
</script>