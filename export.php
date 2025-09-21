<?php
session_start();
require('fpdf186/fpdf.php');

// --- LÍNEA AÑADIDA Y CONFIGURADA ---
// Forzamos la zona horaria a UTC-3.
// 'America/Argentina/Buenos_Aires' es una zona horaria estándar para UTC-3 que maneja correctamente
// cualquier regla horaria local. Otras opciones podrían ser 'America/Sao_Paulo' o 'America/Santiago'.
date_default_timezone_set('America/Argentina/Buenos_Aires');
// --- FIN DE LA MODIFICACIÓN ---

$format = isset($_GET['format']) ? $_GET['format'] : 'txt';
$tool = isset($_GET['tool']) ? preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['tool']) : 'desconocido';

$temp_file = '/tmp/netlab_result_' . session_id() . '.txt';

if (!file_exists($temp_file)) {
    die("Error: No se encontró ningún resultado para exportar. Por favor, ejecuta una herramienta primero.");
}

$content = file_get_contents($temp_file);

// El formato del nombre del archivo ya está corregido (Y-m-d_H-i-s).
$filename = $tool . '_export_' . date('Y-m-d_H-i-s') . '.' . $format;

switch ($format) {
    case 'pdf':
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetTitle("Resultado de " . $tool);
        $pdf->SetFont('Courier', '', 10);
        
        $pdf->MultiCell(0, 5, $content);
        
        $pdf->Output('D', $filename);
        break;

    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Resultado']);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            fputcsv($output, [$line]);
        }
        
        fclose($output);
        break;

    case 'txt':
    default:
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $content;
        break;
}
?>
