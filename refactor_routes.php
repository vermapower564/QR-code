<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

// Clean up AdminSettingsController patch mapping from index to update (if the method exists, though maybe it's handled in index, let's keep it safe but correct)
$content = str_replace(
    "Route::patch('/settings', [AdminSettingsController::class, 'index']);",
    "Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');",
    $content
);

file_put_contents($file, $content);
echo "Routes refined\n";
?>
