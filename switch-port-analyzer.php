<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Analizador de Puerto de Switch (CDP/LLDP)</div>
  <div class="card-body">
    <p class="card-text">Escucha paquetes de descubrimiento (CDP/LLDP) para identificar a qué switch y puerto físico está conectado el servidor.</p>
    <?php
      $interfaces_raw = shell_exec("/sbin/ip -o link show | /usr/bin/awk -F': ' '{print $2}'");
      $interfaces = array_filter(explode("\n", trim($interfaces_raw)));
      if (empty($interfaces)) {
        echo "<div class='alert alert-danger'>No se pudieron detectar interfaces de red.</div>";
      } else {
    ?>
    <form method="post" class="row g-3">
      <div class="col-md-10">
        <label for="interface" class="form-label">Seleccionar Interfaz</label>
        <select name="interface" id="interface" class="form-select" required>
          <?php foreach ($interfaces as $iface): ?>
            <option value="<?= htmlspecialchars($iface) ?>"><?= htmlspecialchars($iface) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Consultar</button>
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
    
    $command = "sudo /usr/bin/tcpdump -i $interface_for_command -c 1 -vv 'ether proto 0x88cc or ether[20:2] == 0x2000'";
    
    exec($command . " 2>&1", $output_array, $return_code);
    $raw_output = implode("\n", $output_array);

  $temp_file = '/tmp/netlab_result_' . session_id() . '.txt';
    file_put_contents($temp_file, $raw_output);

    echo "<div class=\"mt-4\">";
    echo "<h6>Comando Ejecutado: <small class='text-muted'><code>" . htmlspecialchars($command) . "</code></small></h6>";
    echo "<pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";
    
    if ($return_code === 0 && !empty($raw_output)) {
        echo "<div class=\"alert alert-success mt-2\">Paquete de descubrimiento capturado.</div>";
        echo '<div class="card mt-3"><div class="card-body"><h5 class="card-title">Exportar Resultado</h5><a href="export.php?format=txt&tool=switch-port" class="btn btn-secondary">Exportar a TXT</a><a href="export.php?format=csv&tool=switch-port" class="btn btn-success">Exportar a CSV</a><a href="export.php?format=pdf&tool=switch-port" class="btn btn-danger">Exportar a PDF</a></div></div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> No se capturó ningún paquete CDP/LLDP. Asegúrate de que el switch lo tenga activado en este puerto.</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>```

