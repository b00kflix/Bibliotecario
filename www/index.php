<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="arquivos/scripts/jquery-3.4.1.js"></script>
    <link rel="icon" href="../img/faviconV2 (2).png" type="image/png">
    <title>BOOKFLIX</title>
</head>
<body>
<style>
  /* Estilos para o contêiner centralizado */
  .container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center; /* Centraliza o texto e o conteúdo inline dentro do contêiner */
  }
  button.centralizado {
    /* Estilos para o botão */
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 20px; /* Espaçamento opcional acima do botão */
  }
  body {
    font-family: Arial, sans-serif; /* Fonte padrão */
    background-color: #f0f0f0; /* Cor de fundo suave */
    margin: 0; /* Remove a margem padrão do corpo */
  }
</style>
<?php





?>

<div class="container">
  <button class="centralizado" id="openLink">Abrir sistema</button><br><br>
  <div>O acesso a este site é restrito a pessoas autorizadas. Caso tenha acessado por engano, solicitamos que, por gentileza, retorne à página anterior. Informamos que sua sessão está sendo gravada e o seu IP está sendo rastreado. Agradecemos pela compreensão.</div>
</div>
   

</body>
<script>
$(document).ready(function(){
  $('#openLink').click(function(){
    // Fazer chamada AJAX para executar o comando PHP
    $.get('open_external.php');
    
  });
 
});
</script>
</html>