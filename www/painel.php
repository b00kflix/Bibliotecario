<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="../img/faviconV2 (2).png" type="image/png">
    <title>BOOKFLX</title>
    <!-- Bootstrap -->
    <link href="arquivos/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="arquivos/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="arquivos/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="arquivos/vendors/animate.css/animate.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="arquivos/build/css/custom.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link href="arquivos/scripts/sweetalert/sweetalert2.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <!-- Formulário de Login -->
            <form id="loginForm" method="POST" action="cad_usuarios/login.php">
              <h1>BOOKFLIX <br><br>Bibliotecário</h1>
              <div>
                <input type="text" id="login" name="login" class="form-control" placeholder="Login" required />
              </div>
              <div>
                <input type="password" id="senha" name="senha" class="form-control" placeholder="Senha" required />
              </div>
              <div>
                <button type="submit" class="btn btn-primary submit">Entrar</button>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-graduation-cap"></i> BOOKFLIX</h1>
                  www.bookflix.uninove.br
                  <p>© 2025 Todos os direitos reservados.<br>Desenvolvido por <a href="https://github.com/b00kflix" target="_blank">TEAM BOOKFLIX.</a>.</p>
                </div>
              </div>
            </form>
            <!-- Fim do Formulário de Login -->
          </section>
        </div>
      </div>
    </div>
   
    <!-- jQuery -->
    <script src="arquivos/scripts/jquery-3.4.1.js"></script>
    <!-- SweetAlert JS -->
    <script src="arquivos/scripts/sweetalert/sweetalert.js"></script>

    <script>
    $(function(){
        $("#loginForm").on('submit', function(e){
            e.preventDefault();
            
            var login = $("#login").val();
            var senha = $("#senha").val();

            $.ajax({
                url: 'admin/cad_usuarios/login.php',
                type: 'POST',
                data: { login: login, senha: senha },
                dataType: 'json', 
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = response.redirect || "cad_usuarios/painel.php";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Login ou senha incorretos!'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro no login. Tente novamente!'
                    });
                }
            });
        });
    });
    </script>
  </body>
</html>
