<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Lunar\Admin\Database\State\EnsureBaseRolesAndPermissions;

try {
    $state = new EnsureBaseRolesAndPermissions();
    $state->run();
    echo "Permissions seeded successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
