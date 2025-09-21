<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Traceroute Avanzado</div>
  <div class="card-body">
    <form method="post">
      <div class="mb-3">
        <label for="host" class="form-label">Host o Dirección IP</label>
  <input type="text" id="host" name="host" class="form-control" placeholder="Ejemplo: google.com, 8.8.8.8" required>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label for="protocol" class="form-label">Protocolo del Paquete</label>
          <select id="protocol" name="protocol" class="form-select">
            <option value="udp">UDP (por defecto)</option>
            <option value="icmp">ICMP (-I)</option>
            <option value="tcp">TCP (-T)</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="port" class="form-label">Puerto de Destino (para TCP)</label>
    <input type="number" id="port" name="port" class="form-control" placeholder="Ejemplo: 80, 443" min="1" max="65535">
        </div>
      </div>
      
      <div class="mt-4">
        <button type="submit" class="btn btn-success w-100">Ejecutar Traceroute</button>
      </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["host"])) {
    // --- CONSTRUCCIÓN DINÁMICA DEL COMANDO ---
    $command = "/usr/bin/traceroute";
    
    // Añadir opciones según el protocolo
    $protocol = $_POST['protocol'] ?? 'udp';
    if ($protocol === 'icmp') {
        $command .= " -I";
    } elseif ($protocol === 'tcp') {
        $command .= " -T";
        // Añadir el puerto solo si es TCP y se ha especificado
        if (!empty($_POST['port']) && is_numeric($_POST['port'])) {
            $command .= " -p " . intval($_POST['port']);
        }
    }

    $command .= " " . escapeshellarg($_POST["host"]);
    // --- FIN DE LA CONSTRUCCIÓN DEL COMANDO ---

    ob_start();
    passthru($command . " 2>&1", $return_code);
    $raw_output = ob_get_clean();

    $temp_file = '/tmp/nettoolbox_result_' . session_id() . '.txt';
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
                <a href="export.php?format=txt&tool=traceroute" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=traceroute" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=traceroute" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
