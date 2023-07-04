<?php

$moduleFields = [];
// Metadata override if needed
$file = __DIR__.'/../../../Custom/Solutions/lib/suitecrm8/metadata.php';
if (file_exists($file)) {
    require $file;
}
