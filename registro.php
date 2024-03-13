<?php
require_once("conexion.php");
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$db = new Database();
$conectar = $db->conectar();

$usua = $conectar->prepare("SELECT * FROM persona");
$usua->execute();
$asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["registro"]) && ($_POST["registro"] == "formu")) {
    $ced = $_POST['ced'];
    $nombre = $_POST['nombre'];
    $email = isset($_POST['email']) ? $_POST['email'] : null; // Validación para asegurarse de que $email no sea nulo
    if ($email !== null) {
        $codigo_barras = uniqid() . rand(1000, 9999);
        $generator = new BarcodeGeneratorPNG();
        $codigo_barras_imagenes = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);

        file_put_contents(__DIR__ . '/imagenes/' . $codigo_barras . '.png', $codigo_barras_imagenes);

        $Insertsql = $conectar->prepare("INSERT INTO persona (nombre, ced, codigo_barras, email) VALUES (?, ?, ?, ?)");
        $Insertsql->execute([$nombre, $ced, $codigo_barras, $email]);
    } else {
        echo "Error: El campo de correo electrónico no puede estar vacío.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <main class="contenedor sombra">
        <div class="container mt-3">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-transform: uppercase;">
                        <th>Cedula</th>
                        <th>Código de Barras</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asigna as $usua) { ?>
                        <tr>
                            <td><?= $usua["ced"] ?></td>
                            <td><img src="imagenes/<?= $usua["codigo_barras"] ?>.png" style="max-width: 400px;"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="container mt-5">
            <h2>Registrar Persona</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="ced">Cedula</label>
                    <input type="text" class="form-control" id="ced" name="ced" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <input type="submit" class="btn btn-success" value="Registrar">
                <input type="hidden" name="registro" value="formu">
            </form>
        </div>
    </main>
</body>

</html>
