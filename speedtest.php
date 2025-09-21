<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Speedtest</div>
  <div class="card-body">
    <form method="post"><button type="submit" class="btn btn-primary">Iniciar Prueba de Velocidad</button></form>
  </div>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = "/usr/bin/speedtest-cli --simple";

    ob_start();
    passthru($command . " 2>&1", $return_code);
    $raw_output = ob_get_clean();

    $temp_file = '/tmp/nettoolbox_result_' . session_id() . '.txt';
    file_put_contents($temp_file, $raw_output);

    echo "<div class=\"mt-4\"><pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";

    if ($return_code === 0) {
        echo "<div class=\"alert alert-success mt-2\">Comando completado exitosamente.</div>";
        
        echo '
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Exportar Resultado</h5>
                <a href="export.php?format=txt&tool=speedtest" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=speedtest" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=speedtest" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
