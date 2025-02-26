<?php
echo "<h1>Cockpit CMS Requirements Check</h1>";

// Check PHP version
echo "PHP Version: " . phpversion() . " - Required: >= 8.3 - ";
echo (version_compare(PHP_VERSION, '8.3.0') >= 0) ? "✅ OK" : "❌ FAILED";
echo "<br>";

// Check PDO + SQLite
echo "PDO Extension: ";
echo extension_loaded('pdo') ? "✅ OK" : "❌ FAILED";
echo "<br>";

echo "PDO SQLite Extension: ";
echo extension_loaded('pdo_sqlite') ? "✅ OK" : "❌ FAILED";
echo "<br>";

// Check GD extension
echo "GD Extension: ";
echo extension_loaded('gd') ? "✅ OK" : "❌ FAILED";
echo "<br>";

// Check mod_rewrite (more complex)
echo "mod_rewrite: ";
if (function_exists('apache_get_modules')) {
    echo in_array('mod_rewrite', apache_get_modules()) ? "✅ OK" : "❌ FAILED";
} else {
    echo "⚠️ Cannot check (probably enabled if running in Apache)";
}
echo "<br>";

// Check DOCUMENT_ROOT
echo "DOCUMENT_ROOT: ";
echo isset($_SERVER['DOCUMENT_ROOT']) ? "✅ Set to: " . $_SERVER['DOCUMENT_ROOT'] : "❌ NOT SET";
echo "<br>";

// Check storage permissions
echo "<h2>Storage Permissions</h2>";
$storageDir = __DIR__ . '/storage';
echo "Storage dir exists: ";
echo file_exists($storageDir) ? "✅ YES" : "❌ NO";
echo "<br>";

echo "Storage is writable: ";
echo is_writable($storageDir) ? "✅ YES" : "❌ NO";
echo "<br>";

// Check storage subdirectories
$subDirs = ['cache', 'tmp', 'uploads', 'data', 'database'];
foreach ($subDirs as $dir) {
    $path = $storageDir . '/' . $dir;
    echo "$dir exists: ";
    echo file_exists($path) ? "✅ YES" : "❌ NO";
    echo " - Writable: ";
    echo (file_exists($path) && is_writable($path)) ? "✅ YES" : "❌ NO";
    echo "<br>";
}

// Try to create a test file
echo "<h2>Write Test</h2>";
try {
    $testFile = $storageDir . '/test_' . time() . '.txt';
    file_put_contents($testFile, 'Test');
    echo "Created test file: ✅ SUCCESS";
    unlink($testFile);
    echo " (and deleted it)";
} catch (Exception $e) {
    echo "Failed to create test file: ❌ ERROR - " . $e->getMessage();
}
echo "<br>";

echo "<h2>Installation Steps</h2>";
echo "1. Make sure all requirements above are green<br>";
echo "2. Visit <a href='/install'>/install</a> to set up your admin account<br>";
