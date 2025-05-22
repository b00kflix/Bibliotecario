<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit(); // Garante que o script não continue sendo executado
} else {
    // Inclui a configuração do banco de dados SQLite3
    $db = new SQLite3('../data/bibliotecario.db'); 

    $id_usuario = $_SESSION['user_id'];

    // Consulta segura usando prepared statements
    $stmt = $db->prepare("SELECT id, nivel, nome, foto, email, fone, login FROM cad_usuario WHERE id = :id");
    $stmt->bindValue(':id', $id_usuario, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // Verifica se o usuário foi encontrado
    $dados = $result->fetchArray(SQLITE3_ASSOC);
    if ($dados) {
        // Extrai os dados do usuário
        $id_usuario = $dados['id'] ?? '';
        $nivel_usuario = $dados['nivel'] ?? '';
        $nome_usuario = $dados['nome'] ?? '';
        $foto_usuario = $dados['foto'] ?? '';
        $email_usuario = $dados['email'] ?? '';
        $fone_usuario = $dados['fone'] ?? '';
        $login_usuario = $dados['login'] ?? '';

        // Define a foto padrão, caso esteja vazia
        if (empty($foto_usuario)) {
            $foto_usuario = '../img/default.jpg';
        } else {
            $foto_usuario = '../uploads/imagens/' . $foto_usuario;
        }
    } else {
        header("Location: ../index.php");
        exit();
    }

    $stmt->close();
    $db->close();
}
?>

<script src="../arquivos/vendors/parsleyjs/dist/parsley.min.js"></script>
<script src="../arquivos/vendors/parsleyjs/dist/i18n/pt-br.js"></script>

<div class="page-title">
    <div class="title_left">
        <h3>Perfil de Usuário</h3>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Atualizar</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form id="cadastroForm" enctype="multipart/form-data" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                    <!-- Foto de Perfil -->
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Foto de Perfil</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="row">
                                <!-- Foto Anterior -->
                                <div class="col-md-6">
                                    <p>Foto Anterior:</p>
                                    <img id="foto-anterior" src="<?php echo $foto_usuario ?>" alt="Foto Anterior" style="width: 100%; max-width: 120px;">
                                </div>
                                <!-- Foto Atual -->
                                <div class="col-md-6">
                                    <p>Foto Atual:</p>
                                    <img id="foto-atual" src="<?php echo $foto_usuario ?>" alt="Foto Atual" style="width: 100%; max-width: 120px;">
                                </div>
                            </div>
                            <br>
                            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                        </div>
                    </div>

                    <!-- Nome -->
                    <div class="form-group">
                        <label for="nome" class="control-label col-md-3 col-sm-3 col-xs-12">Nome</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="nome" name="nome" value="<?php echo $nome_usuario ?>" required class="form-control">
                        </div>
                    </div>

                    <!-- Login -->
                    <div class="form-group">
                        <label for="login" class="control-label col-md-3 col-sm-3 col-xs-12">Login</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" id="login" name="login" value="<?php echo $login_usuario ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Fone -->
                    <div class="form-group">
                        <label for="fone" class="control-label col-md-3 col-sm-3 col-xs-12">Fone</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="tel" id="fone" name="fone" value="<?php echo $fone_usuario ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Senha Atual -->
                    <div class="form-group">
                        <label for="senhaAtual" class="control-label col-md-3 col-sm-3 col-xs-12">Senha Atual</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="senhaAtual" name="senhaAtual" type="password" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                        </div>
                    </div>

                    <!-- Nova Senha -->
                    <div class="form-group">
                        <label for="novaSenha" class="control-label col-md-3 col-sm-3 col-xs-12">Nova Senha (opcional)</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="novaSenha" name="novaSenha" type="password" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <button class="btn btn-primary cancelar" type="button">Cancelar</button>
                            <button type="submit" class="btn btn-success salvar">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Máscara para telefone
        $('#fone').mask('(00) 00000-0000');

        // Pré-visualizar imagem
        $('#foto').change(function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#foto-atual').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Submissão do formulário
        $('#cadastroForm').on('submit', function(e) {
            e.preventDefault();

            var senhaAtual = $('#senhaAtual').val().trim();
            var novaSenha = $('#novaSenha').val().trim();

            if ((senhaAtual && !novaSenha) || (!senhaAtual && novaSenha)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Preencha tanto a senha atual quanto a nova para alterá-las.'
                });
                return;
            }

            if (novaSenha && novaSenha.length < 5) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'A nova senha deve ter pelo menos 5 caracteres.'
                });
                return;
            }

            var formData = new FormData(this);

            $.ajax({
                url: '../perfil/salva_perfil.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
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
                        text: 'Erro ao salvar os dados. Tente novamente!'
                    });
                }
            });
        });
    });
</script>