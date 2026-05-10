<?php
include "php/conexao.php";

$categoria_id = $_POST['categoria_id'] ?? 0;

if ($categoria_id == 0) {

    $sql = "
        SELECT fotos.*, categorias.nome AS categoria_nome
        FROM fotos
        INNER JOIN categorias
        ON fotos.categoria_id = categorias.id
        ORDER BY fotos.id DESC
    ";

} else {

    $sql = "
        SELECT fotos.*, categorias.nome AS categoria_nome
        FROM fotos
        INNER JOIN categorias
        ON fotos.categoria_id = categorias.id
        WHERE fotos.categoria_id = $categoria_id
        ORDER BY fotos.id DESC
    ";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    while($foto = $result->fetch_assoc()) {
?>

<div class="foto">

    <img src="<?php echo $foto['caminho_arquivo']; ?>">

    <div class="overlay">

        <h3><?php echo $foto['titulo']; ?></h3>

        <p><?php echo $foto['categoria_nome']; ?></p>

    </div>

</div>

<?php
    }

} else {
    echo "<p style='grid-column:1/-1;text-align:center;padding:50px;opacity:0.5;'>Nenhuma foto encontrada.</p>";
}
?>