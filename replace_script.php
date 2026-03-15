<?php
$files = array_merge(
    glob(__DIR__ . '/resources/views/website/**/*.blade.php'),
    glob(__DIR__ . '/resources/views/website/*.blade.php')
);
foreach($files as $f) {
    if(is_file($f)) {
        $content = file_get_contents($f);
        $content = str_replace('FreshStore', 'Southwest Farmers', $content);
        file_put_contents($f, $content);
    }
}
echo "Done.";
