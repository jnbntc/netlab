<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">ARP Scan</div>
  <div class="card-body">
    <?php
      $interfaces_raw = shell_exec("/sbin/ip -o link show | /usr/bin/awk -F': ' '{print $2}'");
      $interfaces = array_filter(explode("\n", trim($interfaces_raw)));
      if (empty($interfaces)) {
        echo "<div class='alert alert-danger'>No se pudieron detectar interfaces de red.</div>";
      } else {
    ?>
    <form method="post" class="row g-3">
      <div class="col-md-10">
        <label for="interface" class="form-label">Seleccionar Interfaz de Red</label>
        <select name="interface" id="interface" class="form-select" required>
          <?php foreach ($interfaces as $iface): ?>
            <option value="<?= htmlspecialchars($iface) ?>"><?= htmlspecialchars($iface) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Escanear</button>
      </div>
    </form>
    <?php } ?>
  </div>
</div>

<?php
if (!empty($_POST["interface"])) {
    $submitted_interface = $_POST['interface'];
    $interface_clean = preg_replace('/[^a-zA-Z0-9.-@]/', '', $submitted_interface);

    if ($interface_clean === $submitted_interface) {
        $parts = explode('@', $interface_clean);
        $interface_for_command = $parts[0];
        $command = "sudo /usr/sbin/arp-scan --interface=" . escapeshellarg($interface_for_command) . " --localnet";
        
        ob_start();
        passthru($command . " 2>&1", $return_code);
        $raw_output = ob_get_clean();

        $temp_file = '/tmp/nettoolbox_result_' . session_id() . '.txt';
        file_put_contents($temp_file, $raw_output);

        echo "<div class=\"mt-4\"><pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";
        
        if ($return_code === 0) {
            echo "<div class=\"alert alert-success mt-2\">Escaneo completado exitosamente.</div>";

            echo '
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Exportar Resultado</h5>
                    <a href="export.php?format=txt&tool=arpscan" class="btn btn-secondary">Exportar a TXT</a>
                    <a href="export.php?format=csv&tool=arpscan" class="btn btn-success">Exportar a CSV</a>
                    <a href="export.php?format=pdf&tool=arpscan" class="btn btn-danger">Exportar a PDF</a>
                </div>
            </div>';
        } else {
            echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El escaneo finalizó con un código de error ($return_code).</div>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger mt-4'>Nombre de interfaz no válido.</div>";
    }
}
?>

<?php include 'footer.php'; ?>
