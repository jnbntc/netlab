<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Descubridor de Vecinos IPv6</div>
  <div class="card-body">
    <p class="card-text">Utiliza el protocolo NDP para encontrar dispositivos con IPv6 activo en una VLAN.</p>
    <?php
      $interfaces_raw = shell_exec("/sbin/ip -o link show | /usr/bin/awk -F': ' '{print $2}'");
      $interfaces = array_filter(explode("\n", trim($interfaces_raw)));
      if (empty($interfaces)) {
        echo "<div class='alert alert-danger'>No se pudieron detectar interfaces de red.</div>";
      } else {
    ?>
    <form method="post" class="row g-3">
      <div class="col-md-10">
        <label for="interface" class="form-label">Seleccionar Interfaz de VLAN</label>
        <select name="interface" id="interface" class="form-select" required>
          <?php foreach ($interfaces as $iface): ?>
            <option value="<?= htmlspecialchars($iface) ?>"><?= htmlspecialchars($iface) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Descubrir</button>
      </div>
    </form>
    <?php } ?>
  </div>
</div>

<?php
if (!empty($_POST["interface"])) {
    // --- INICIO DE LA CORRECCIÓN ---
    $parts = explode('@', $_POST['interface']);
    $interface_for_command = escapeshellarg($parts[0]);
    // --- FIN DE LA CORRECCIÓN ---

    $command = "sudo /usr/bin/nmap -6 --script broadcast-ping6 -e $interface_for_command";
    
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
        echo '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Exportar Resultado</h5><a href="export.php?format=txt&tool=ipv6-discover" class="btn btn-secondary">Exportar a TXT</a><a href="export.php?format=csv&tool=ipv6-discover" class="btn btn-success">Exportar a CSV</a><a href="export.php?format=pdf&tool=ipv6-discover" class="btn btn-danger">Exportar a PDF</a></div></div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
