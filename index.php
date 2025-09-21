<?php
/**
 * Network Toolbox Web
 * Autor: Juan Bentancour <juan.bentancour@furlong.com.ar>
 */
// Desactivar buffering para permitir streaming de salida

session_start();
include 'header.php';

// Array asociativo con la información de todas las herramientas para generar las tarjetas
$tools = [
    ['name' => 'Ping Avanzado', 'file' => 'ping.php', 'desc' => 'Verifica conectividad y latencia con un host usando paquetes ICMP.'],
    ['name' => 'Traceroute Avanzado', 'file' => 'traceroute.php', 'desc' => 'Traza la ruta de red completa hasta un host de destino.'],
    ['name' => 'Nmap Scanner', 'file' => 'nmap/', 'desc' => 'Descubre puertos, servicios y vulnerabilidades en un objetivo.'],
    ['name' => 'ARP Scan', 'file' => 'arpscan.php', 'desc' => 'Escanea la red local para descubrir dispositivos por su MAC address.'],
    ['name' => 'DHCP Discover', 'file' => 'dhcp-discover.php', 'desc' => 'Encuentra servidores DHCP activos en una interfaz de VLAN.'],
    ['name' => 'IPv6 Discover', 'file' => 'ipv6-discover.php', 'desc' => 'Encuentra vecinos con IPv6 activo en una interfaz de VLAN.'],
    ['name' => 'Switch Port Analyzer', 'file' => 'switch-port-analyzer.php', 'desc' => 'Identifica el puerto del switch al que está conectado el servidor.'],
    ['name' => 'Packet Capture', 'file' => 'packet-capture.php', 'desc' => 'Captura y analiza tráfico de red en vivo en una interfaz.'],
    ['name' => 'DNS Lookup', 'file' => 'dnslookup.php', 'desc' => 'Realiza consultas DNS para cualquier tipo de registro.'],
    ['name' => 'HTTP Headers', 'file' => 'httpheaders.php', 'desc' => 'Inspecciona las cabeceras de respuesta HTTP de una URL.'],
    ['name' => 'GeoIP Lookup', 'file' => 'geoip.php', 'desc' => 'Obtiene la ubicación geográfica de una dirección IP pública.'],
    ['name' => 'Whois Lookup', 'file' => 'whois.php', 'desc' => 'Consulta la información de registro de un dominio o IP.'],
    ['name' => 'iPerf3 Avanzado', 'file' => 'iperf.php', 'desc' => 'Mide el rendimiento máximo del ancho de banda de la red.'],
    ['name' => 'Speedtest', 'file' => 'speedtest.php', 'desc' => 'Mide la velocidad de la conexión a internet de este servidor.']
];
?>

<div class="container mt-4">
    <div class="p-5 mb-4 bg-light rounded-3">
      <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Network Toolbox</h1>
        <p class="col-md-8 fs-4">Un centro de diagnóstico de red centralizado. Selecciona una herramienta a continuación para comenzar tu análisis.</p>
      </div>
    </div>

    <div class="row">
        <?php foreach ($tools as $tool): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($tool['name']) ?></h5>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars($tool['desc']) ?></p>
                    <a href="/netlab/<?= $tool['file'] ?>" class="btn btn-primary mt-auto">Ir a la Herramienta</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
