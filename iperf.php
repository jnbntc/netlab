<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">iPerf3 Avanzado</div>
  <div class="card-body">
    <form method="post">
        <div class="mb-3">
            <label for="server" class="form-label">Servidor iPerf3</label>
            <input type="text" id="server" name="server" class="form-control" placeholder="iperf.he.net" required>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="streams" class="form-label">Streams Paralelos (-P)</label>
                <input type="number" id="streams" name="streams" class="form-control" placeholder="1 (por defecto)">
            </div>
            <div class="col-md-6">
                <label for="duration" class="form-label">Duración (-t, segundos)</label>
                <input type="number" id="duration" name="duration" class="form-control" placeholder="10 (por defecto)">
            </div>
        </div>
        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="reverse" value="1" id="reverse">
            <label class="form-check-label" for="reverse">
                Modo Inverso (-R) <small class="text-muted">- Prueba la velocidad de descarga.</small>
            </label>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-success w-100">Probar</button>
        </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["server"])) {
    $command = "/usr/bin/iperf3 -c " . escapeshellarg($_POST['server']);

    if (isset($_POST['reverse'])) {
        $command .= " -R";
    }
    if (!empty($_POST['streams']) && is_numeric($_POST['streams'])) {
        $command .= " -P " . intval($_POST['streams']);
    }
    if (!empty($_POST['duration']) && is_numeric($_POST['duration'])) {
        $command .= " -t " . intval($_POST['duration']);
    }
    
    ob_start();
    passthru($command . " 2>&1", $return_code);
    $raw_output = ob_get_clean();

    $temp_file = '/tmp/netlab_result_' . session_id() . '.txt';
    file_put_contents($temp_file, $raw_output);

    echo "<div class=\"mt-4\">";
    echo "<h6>Comando Ejecutado: <small class='text-muted'><code>" . htmlspecialchars($command) . "</code></small></h6>";
    echo "<pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";
    
    if ($return_code === 0) {
        echo "<div class=\"alert alert-success mt-2\">Comando completado exitosamente.</div>";
        echo '
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Exportar Resultado</h5>
                <a href="export.php?format=txt&tool=iperf" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=iperf" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=iperf" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>
<?php include 'footer.php'; ?>
