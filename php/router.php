<?php

/*
 * php cli web server
 * router for php built in server to show directory listings.
 * php -S localhost:8001 router.php
 *
 * 未做验证 只是个例子
 * 
 */


$path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER["REQUEST_URI"];
$uri = $_SERVER["REQUEST_URI"];

// let server handle files or 404s
if (!file_exists($path) || is_file($path))  {
    return false;
}

// append / to directories
if (is_dir($path) && $uri[strlen($uri) -1] != '/') {
    header('Location: ' . $uri . '/');
    exit;
}

// send index.html and index.php
$indexes = ['index.php', 'index.html'];
foreach($indexes as $index) {
    $file = $path . '/' . $index;
    if (is_file($file)) {
        return require($file);
    }
}

// show directory list
echo "<h2>Index of " . $uri . "</h2>";
$g = array_map(function($path) {
    if (is_dir($path)) {
        $path .= '/';
    }
    return str_replace('//', '/', $path);
}, glob($path . '/*'));

usort($g, function($a,$b) {
    if(is_dir($a) == is_dir($b))
        return strnatcasecmp($a,$b);
    else
        return is_dir($a) ? -1 : 1;
});

echo implode("<br>", array_map(function($a) {
    $url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $a);
    return '<a href="' . $url . '">' . substr($url, 1) . '</a>';
}, $g));

