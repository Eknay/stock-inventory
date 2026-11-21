<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$files = glob(__DIR__ . '/Unit/*Test.php');
sort($files);

$failures = 0;
$testCount = 0;

foreach ($files as $file) {
    require_once $file;
    $class = 'App\\Tests\\Unit\\' . basename($file, '.php');
    if (!class_exists($class)) {
        echo "FAIL  Test sınıfı bulunamadı: {$class}" . PHP_EOL;
        ++$failures;
        continue;
    }

    $test = new $class();
    ++$testCount;
    $failures += $test->run();
}

echo PHP_EOL . sprintf(
    '%d test sınıfı çalıştırıldı, %d hata bulundu.%s',
    $testCount,
    $failures,
    PHP_EOL
);

exit($failures === 0 ? 0 : 1);
