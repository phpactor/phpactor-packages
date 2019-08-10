<?php

if (!file_exists(__DIR__ . '/../build')) {
    mkdir('/../build', 0777);
}
echo `php bin/generate-package-meta.php > build/package-meta.json`;
echo `php bin/generate-composer-json.php > composer.json.new`;
echo `cp composer.json.new composer.json`;
