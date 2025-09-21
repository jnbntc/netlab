<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Captura de Paquetes (tcpdump)</div>
  <div class="card-body">
    <form method="post">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="interface" class="form-label">Interfaz de Captura</label>
          <select name="interface" id="interface" class="form-select" required>
            <?php
              $interfaces_raw = shell_exec("/sbin/ip -o link show | /usr/bin/awk -F': ' '{print $2}'");
              $interfaces = array_filter(explode("\n", trim($interfaces_raw)));
              foreach ($interfaces as $iface) {
                  echo "<option value=\"" . htmlspecialchars($iface) . "\">" . htmlspecialchars($iface) . "</option>";
              }
            ?>
          </select>
        </div>
        <div class="col-md-6">
          <label for="count" class="form-label">Cantidad de Paquetes a Capturar</label>
          <input type="number" id="count" name="count" class="form-control" value="50" required>
        </div>
      </div>
      <div class="mt-3">
        <label for="filter" class="form-label">Filtro de Captura (opcional)</label>
        <input type="text" id="filter" name="filter" class="form-control" placeholder="Ej: host 1.1.1.1, port 53, arp, etc.">
        <small class="text-muted">Usa la sintaxis de filtros de pcap.</small>
      </div>
      <div class="mt-4">
        <button type="submit" class="btn btn-danger w-100">Iniciar Captura</button>
      </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["interface"])) {
    // --- INICIO DE LA CORRECCIÓN ---
    $parts = explode('@', $_POST['interface']);
    $interface_for_command = escapeshellarg($parts[0]);
    // --- FIN DE LA CORRECCIÓN ---

    $count = (isset($_POST['count']) && is_numeric($_POST['count'])) ? intval($_POST['count']) : 50;
    $command = "sudo /usr/bin/tcpdump -i $interface_for_command -n -c $count";

    if (!empty($_POST['filter'])) {
        $command .= " " . $_POST['filter'];
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
        echo "<div class=\"alert alert-success mt-2\">Captura completada.</div>";
        echo '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Exportar Resultado</h5><a href="export.php?format=txt&tool=packet-capture" class="btn btn-secondary">Exportar a TXT</a><a href="export.php?format=csv&tool=packet-capture" class="btn btn-success">Exportar a CSV</a><a href="export.php?format=pdf&tool=packet-capture" class="btn btn-danger">Exportar a PDF</a></div></div>';
    } else { 
        echo "<div class=\"alert alert-danger mt-2\"><strong>Error:</strong> El comando finalizó con un código de error ($return_code). Verifica la sintaxis de tu filtro.</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
