<?php
echo "<footer class='mt-5 text-center text-muted'>";
echo "<small>© " . date("Y") . " Juan Bentancour - Network Toolbox Web</small>";
echo "</footer></div>"; // El div .container se cierra aquí

// --- INICIO DE LA MODIFICACIÓN ---
// Se añade el script de Bootstrap Bundle JS. Es esencial para la interactividad
// de componentes como el botón "collapse", menús desplegables, modales, etc.
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>';
// --- FIN DE LA MODIFICACIÓN ---

echo "</body></html>";
?>
