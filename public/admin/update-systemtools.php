<?php

/**
 * Script de actualización automática - SystemTools Fix
 * Ejecuta: https://dentalflow.digitalwebsolution.info/admin/update-systemtools.php
 */

echo "<h1>Actualización de SystemTools</h1>";
echo "<hr>";

try {
    // Bootstrap de Laravel
    require __DIR__ . '/../../vendor/autoload.php';
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    // Contenido corregido del archivo SystemTools.php
    $systemToolsPath = __DIR__ . '/../../app/Filament/Pages/SystemTools.php';
    
    if (!file_exists($systemToolsPath)) {
        throw new Exception('No se encontró SystemTools.php');
    }

    $content = file_get_contents($systemToolsPath);
    
    // Reemplazar tenant_id por clinic_id
    $content = str_replace('$domain->tenant_id', '$domain->clinic_id', $content);
    $content = str_replace("'tenant_id' => 'clinicatest'", "'clinic_id' => 'clinicatest'", $content);
    
    // Guardar archivo actualizado
    file_put_contents($systemToolsPath, $content);
    
    echo "✅ Archivo SystemTools.php actualizado exitosamente<br>";
    echo "✅ Cambios aplicados:<br>";
    echo "  - tenant_id → clinic_id<br>";
    
    // Limpiar caché
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    echo "<br>✅ Caché limpiada<br>";
    echo "<br><strong style='color: green;'>¡LISTO! Ya puedes usar el botón Fix Dominios</strong><br>";
    echo "<br><a href='/admin/system-tools'>Volver a System Tools</a>";

} catch (\Exception $e) {
    echo "<strong style='color: red;'>ERROR: " . $e->getMessage() . "</strong><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
