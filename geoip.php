<?php
session_start();
include 'header.php';
?>

<div class="card shadow">
  <div class="card-header bg-dark text-white">GeoIP Lookup</div>
  <div class="card-body">
    <form method="post" class="input-group">
      <input type="text" name="ip" class="form-control" placeholder="IP pública" required>
      <button type="submit" class="btn btn-success">Buscar</button>
    </form>
  </div>
</div>

<?php
if (!empty($_POST["ip"])) {
    $raw_output = ""; // Inicializar la variable
    if (filter_var($_POST["ip"], FILTER_VALIDATE_IP)) {
        $command = "/usr/bin/curl -s http://ip-api.com/line/" . escapeshellarg($_POST["ip"]);
        exec($command . " 2>&1", $output, $return_code);

        if ($return_code === 0 && !empty($output) && $output[0] !== 'fail') {
            $labels = ['Estado', 'País', 'Código País', 'Región', 'Cód. Región', 'Ciudad', 'Código Postal', 'Latitud', 'Longitud', 'Timezone', 'ISP', 'Organización', 'AS'];
            $formatted_output = "";
            foreach ($output as $index => $line) {
                if(isset($labels[$index])) {
                    $formatted_output .= str_pad($labels[$index] . ":", 15) . htmlspecialchars($line) . "\n";
                }
            }
            $raw_output = $formatted_output;
        } else {
            $raw_output = "No se pudo obtener información para la IP: " . htmlspecialchars($_POST["ip"]);
            if (!empty($output)) {
                $raw_output .= "\nRespuesta de la API: " . htmlspecialchars($output[1] ?? $output[0]);
            }
        }
        
    $temp_file = '/tmp/netlab_result_' . session_id() . '.txt';
        file_put_contents($temp_file, $raw_output);
        
        echo "<div class=\"mt-4\"><pre class=\"bg-dark text-white p-3 border rounded\">" . htmlspecialchars($raw_output) . "</pre>";

        if ($return_code === 0 && $output[0] !== 'fail') {
            echo "<div class=\"alert alert-success mt-2\">Comando completado exitosamente.</div>";
            echo '
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Exportar Resultado</h5>
                    <a href="export.php?format=txt&tool=geoip" class="btn btn-secondary">Exportar a TXT</a>
                    <a href="export.php?format=csv&tool=geoip" class="btn btn-success">Exportar a CSV</a>
                    <a href="export.php?format=pdf&tool=geoip" class="btn btn-danger">Exportar a PDF</a>
                </div>
            </div>';
        } else {
            echo "<div class=\"alert alert-warning mt-2\"><strong>Aviso:</strong> No se pudo obtener la información.</div>";
        }
        echo "</div>";

    } else {
        echo "<div class='alert alert-danger mt-4'>La dirección IP proporcionada no es válida.</div>";
    }
}
?>

<?php include 'footer.php'; ?>
