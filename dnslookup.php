<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">DNS Lookup Avanzado</div>
  <div class="card-body">
    <form method="post">
        <div class="row g-3">
            <div class="col-md-8">
                <label for="domain" class="form-label">Dominio</label>
                <input type="text" id="domain" name="domain" class="form-control" placeholder="google.com" required>
            </div>
            <div class="col-md-4">
                <label for="record_type" class="form-label">Tipo de Registro</label>
                <select id="record_type" name="record_type" class="form-select">
                    <option value="ANY">ANY</option>
                    <option value="A">A (IPv4)</option>
                    <option value="AAAA">AAAA (IPv6)</option>
                    <option value="MX">MX (Mail)</option>
                    <option value="TXT">TXT (SPF, DKIM)</option>
                    <option value="NS">NS (Name Server)</option>
                    <option value="CNAME">CNAME</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-success w-100">Consultar</button>
        </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["domain"])) {
    $record_type = !empty($_POST['record_type']) ? escapeshellarg($_POST['record_type']) : 'ANY';
    $command = "/usr/bin/dig " . escapeshellarg($_POST["domain"]) . " " . $record_type;

    exec($command . " 2>&1", $output_array, $return_code);
    $raw_output = implode("\n", $output_array);

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
                <a href="export.php?format=txt&tool=dnslookup" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=dnslookup" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=dnslookup" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>
<?php include 'footer.php'; ?>
