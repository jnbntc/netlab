<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">Ping Avanzado</div>
  <div class="card-body">
    <form method="post">
      <div class="mb-3">
        <label for="host" class="form-label">Host o Dirección IP</label>
  <input type="text" id="host" name="host" class="form-control" placeholder="Ejemplo: google.com, 8.8.8.8" required>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label for="count" class="form-label">Cantidad de Paquetes</label>
    <input type="number" id="count" name="count" class="form-control" placeholder="4 (por defecto)" min="1" max="100">
        </div>
        <div class="col-md-6">
          <label for="size" class="form-label">Tamaño del Paquete (bytes)</label>
    <input type="number" id="size" name="size" class="form-control" placeholder="56 (por defecto)" min="1" max="1500">
        </div>
      </div>

      <div class="mt-3">
        <p>
          <a class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" href="#advancedOptions" role="button" aria-expanded="false" aria-controls="advancedOptions">
            Mostrar Opciones Avanzadas
          </a>
        </p>
        <div class="collapse" id="advancedOptions">
          <div class="card card-body bg-light">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="interval" class="form-label">Intervalo (segundos)</label>
                <input type="text" id="interval" name="interval" class="form-control" placeholder="1 (por defecto)">
              </div>
              <div class="col-md-4">
                <label for="ttl" class="form-label">TTL (Time-To-Live)</label>
                <input type="number" id="ttl" name="ttl" class="form-control" placeholder="64 (por defecto)">
              </div>
              <div class="col-md-4">
                <label for="timeout" class="form-label">Timeout (segundos)</label>
                <input type="number" id="timeout" name="timeout" class="form-control" placeholder="Ping usa su propio default">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mt-4">
        <button type="submit" class="btn btn-success w-100">Ejecutar Ping</button>
      </div>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["host"])) {
    // --- CONSTRUCCIÓN DINÁMICA DEL COMANDO ---
    $command = "/bin/ping";
    
    // 1. Cantidad de paquetes (-c)
    $count = !empty($_POST['count']) && is_numeric($_POST['count']) ? intval($_POST['count']) : 4; // Por defecto 4
    $command .= " -c " . $count;

    // 2. Tamaño del paquete (-s) - Solo si se especifica
    if (!empty($_POST['size']) && is_numeric($_POST['size'])) {
        $command .= " -s " . intval($_POST['size']);
    }
    
    // 3. Intervalo (-i) - Solo si se especifica y es un número válido
    if (!empty($_POST['interval']) && is_numeric($_POST['interval'])) {
        $command .= " -i " . floatval($_POST['interval']);
    }

    // 4. TTL (-t) - Solo si se especifica
    if (!empty($_POST['ttl']) && is_numeric($_POST['ttl'])) {
        $command .= " -t " . intval($_POST['ttl']);
    }

    // 5. Timeout (-W) - Solo si se especifica
    if (!empty($_POST['timeout']) && is_numeric($_POST['timeout'])) {
        $command .= " -W " . intval($_POST['timeout']);
    }

    // Añadir el host al final
    $command .= " " . escapeshellarg($_POST["host"]);
    // --- FIN DE LA CONSTRUCCIÓN DEL COMANDO ---


    // El resto del código es el mismo que ya teníamos
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
                <a href="export.php?format=txt&tool=ping" class="btn btn-secondary">Exportar a TXT</a>
                <a href="export.php?format=csv&tool=ping" class="btn btn-success">Exportar a CSV</a>
                <a href="export.php?format=pdf&tool=ping" class="btn btn-danger">Exportar a PDF</a>
            </div>
        </div>';
    } else { 
        echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> El comando finalizó con un código de error ($return_code).</div>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
