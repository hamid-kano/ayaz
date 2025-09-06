<?php

// Cron job script for Hostinger
// Add this to your cron jobs: */5 * * * * /usr/bin/php /path/to/your/project/cron.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('schedule:run');

echo "Cron job executed at " . date('Y-m-d H:i:s') . "\n";