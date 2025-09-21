<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Whois</div>
  <div class="card-body">
    <form method="post" class="input-group">
      <input type="text" name="query" class="form-control" placeholder="dominio o IP" required>
      <button type="submit" class="btn btn-success">Buscar</button>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["query"])) {
    $command = "/usr/bin/whois " . escapeshellarg($_POST["query"]);

    exec($command . " 2>&1", $output_array, $return_code);
    $raw_output = implode("\n", $output_array);

    $temp_file = '/tmp/nettoolbox_result_' . session_id() . '.txt';
    file_put_contents($temp_file, $raw_output);

    echo "<div class=\"mt-4\"><pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";
    
    if ($return_code === 0) {
        echo "<div class=\"alert alert-success mt-2\">Comando completado exitosamente.</div>";
        
        echo '
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Exportar Resultado</h5>
                <a href="export.php?format=txt&tool=whois" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=whois" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=whois" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
