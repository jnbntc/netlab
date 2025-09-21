<?php
session_start();
// La ruta al header debe ser '../' porque estamos en un subdirectorio.
include '../header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Nmap Web Scanner</div>
  <div class="card-body">
    <form method="post">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="target" class="form-label">IP, Host o Rango</label>
          <input type="text" id="target" name="target" class="form-control" placeholder="192.168.0.0/24" required>
          <small class="form-text text-muted">Ejemplo: 192.168.0.1, 192.168.0.0/24, google.com</small>
        </div>
        <div class="col-md-6">
          <label for="scan_type" class="form-label">Tipo de Escaneo</label>
          <select id="scan_type" name="scan_type" class="form-select">
            <option value="quick">Rápido (-T4 -F)</option>
            <option value="intense">Intenso (-T4 -A -v)</option>
            <option value="ping">Solo Ping (-sn)</option>
          </select>
        </div>
      </div>
      
      <div class="mt-3">
          <label for="ports" class="form-label">Puertos Específicos (-p)</label>
          <input type="text" id="ports" name="ports" class="form-control" placeholder="Opcional. Ej: 22,80,443">
      </div>

      <div class="mt-3">
          <div class="form-check">
              <input class="form-check-input" type="checkbox" name="no_ping" value="1" id="no_ping">
              <label class="form-check-label" for="no_ping">
                  No hacer Ping (-Pn)
              </label>
              <small class="form-text text-muted d-block">Útil para hosts que bloquean pings.</small>
          </div>
          <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="detect_version" value="1" id="detect_version">
              <label class="form-check-label" for="detect_version">
                  Detectar Versión de Servicios (-sV)
              </label>
          </div>
      </div>
      
      <div class="mt-4">
        <button type="submit" class="btn btn-success w-100 py-2">Iniciar Escaneo</button>
      </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["target"])) {
    $command = "sudo /usr/bin/nmap";
    $options = "";

    if ($_POST["scan_type"] == "quick") $options = "-T4 -F";
    if ($_POST["scan_type"] == "intense") $options = "-T4 -A -v";
    if ($_POST["scan_type"] == "ping") $options = "-sn";
    $command .= " " . $options;

    if (!empty($_POST['ports'])) {
        $command .= " -p " . escapeshellarg($_POST['ports']);
    }
    if (isset($_POST['no_ping'])) {
        $command .= " -Pn";
    }
    if (isset($_POST['detect_version'])) {
        $command .= " -sV";
    }

    $command .= " " . escapeshellarg($_POST['target']);
    
    ob_start();
    passthru($command . " 2>&1", $return_code);
    $raw_output = ob_get_clean();

    $temp_file = '/tmp/nettoolbox_result_' . session_id() . '.txt';
    file_put_contents($temp_file, $raw_output);
    
    echo "<div class=\"mt-4\">";
    echo "<h6>Comando Ejecutado: <small class='text-muted'><code>" . htmlspecialchars($command) . "</code></small></h6>";
    echo "<pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";
    
    if ($return_code === 0) { 
        echo "<div class=\"alert alert-success mt-2\">Escaneo completado exitosamente.</div>";
        echo '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Exportar Resultado</h5><a href="../export.php?format=txt&tool=nmap" class="btn btn-secondary">Exportar a TXT</a><a href="../export.php?format=csv&tool=nmap" class="btn btn-success">Exportar a CSV</a><a href="../export.php?format=pdf&tool=nmap" class="btn btn-danger">Exportar a PDF</a></div></div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El escaneo finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>
<?php include '../footer.php'; ?>
