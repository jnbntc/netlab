<?php
// Forzamos la zona horaria a UTC-3.
date_default_timezone_set('America/Argentina/Buenos_Aires');

$format = isset($_GET['format']) ? $_GET['format'] : 'txt';
$tool = isset($_GET['tool']) ? preg_replace('/[^a-zA-Z0-9-]/', '', $_GET['tool']) : '';

// Use a portable temp dir and per-tool filenames so export matches tool writers.
$tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
$tool_suffix = $tool !== '' ? $tool : '';
$temp_file = $tmp . DIRECTORY_SEPARATOR . 'netlab_result_' . $tool_suffix . '.txt';

if (!file_exists($temp_file)) {
    http_response_code(404);
    die("Error: No se encontró ningún resultado para exportar. Ejecuta la herramienta primero.");
}

$content = file_get_contents($temp_file);

// Build a safe download filename with timestamp
$filename = ($tool !== '' ? $tool : 'result') . '_export_' . date('Y-m-d_H-i-s') . '.' . $format;

switch ($format) {
    case 'pdf':
        // require FPDF only when needed and check existence
        $fpdf_path = __DIR__ . DIRECTORY_SEPARATOR . 'fpdf186' . DIRECTORY_SEPARATOR . 'fpdf.php';
        if (!file_exists($fpdf_path)) {
            http_response_code(500);
            die("Error: Librería FPDF no encontrada en 'fpdf186/fpdf.php'. Instala FPDF 1.86 en esa ruta para habilitar export a PDF.");
        }
        require_once $fpdf_path;

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetTitle("Resultado de " . ($tool !== '' ? $tool : 'herramienta'));
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
