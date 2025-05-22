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

// Consulta com Limite, Offset e Filtros
$query = "
    SELECT a.id, a.titulo,a.imagem_caminho,a.sinopse, a.autor, a.arquivo_caminho, a.editora, c.titulo AS categoria
    FROM cad_acervo_digital a
    LEFT JOIN cad_categoria c ON a.categoria = c.id
    
";
$result = $db->query($query);
?>
<br>
<h3>Tabela de Acervos Digitais</h3>
<table class="table table-bordered table-striped" id="tabelaDigitais">
    <thead>
        <tr>
            <th>#</th>
            <th class="text-center">Capa</th>
            <th>Título</th>
            <th>Resumo</th>
            <th class="text-center">Visualizar</th>
                
                
            <th>Categoria</th>
           
        </tr>
    </thead>
    <tbody>
        <?php
        $ordem = 1;
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $id = htmlspecialchars($row['id']);
                $titulo = htmlspecialchars($row['titulo']);
                $autor = htmlspecialchars($row['autor']);
                $sinopse = htmlspecialchars($row['sinopse']);
                $editora = htmlspecialchars($row['editora']);
                $categoria = htmlspecialchars($row['categoria']);
                $capa = htmlspecialchars($row['imagem_caminho']);
                $arquivo = htmlspecialchars($row['arquivo_caminho']);
             
        ?>
        <tr>
            <td><?php echo $ordem ?></td>
            <td class="text-center"><img src='../uploads/imagens/<?php echo $capa ?>' width='100'></td>
            <td><?php echo $titulo ?></td>
            <td><?php echo $sinopse ?></td>
            <td class="text-center"><a href="<?php echo '../uploads/arquivos/'.$arquivo ?>" target="blanck" class="btn btn-success">Abrir</a></td>
            <td><?php echo $categoria ?></td>

            
        </tr>

        <?php
        $ordem++;
            }

        ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#tabelaDigitais').DataTable({
            language: {
                url: "../arquivos/vendors/datatables-pt-BR/pt-BR.json"
            },
            pageLength: 10, // Define o número de registros exibidos na primeira página
            dom: 'lfrtip', // Ativa o uso dos botões
           
            columnDefs: [
            {
                targets: 0, // Primeira coluna
                orderable: false, // Desabilita a ordenação nesta coluna
                searchable: false, // Desabilita a pesquisa nesta coluna
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Calcula o índice da linha (começa em 0) e soma 1
                }
            }
        ],
        order: [[3, 'asc']] // Define a terceira coluna como padrão para ordenação
        });

   

            
});
</script>

