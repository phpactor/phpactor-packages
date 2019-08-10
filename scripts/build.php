<?php

if (!file_exists(__DIR__ . '/../build')) {
    mkdir(__DIR__ . '/../build', 0777);
}
echo `php scripts/generate-package-meta.php > build/package-meta.json`;
echo `php scripts/generate-composer-json.php > composer.json.new`;
echo `mv composer.json.new composer.json`;
echo `php scripts/generate-split-list.php > build/split-list.php`;
