<?php
// Delete accounts file to force reinstallation
$accountsFile = __DIR__.'/storage/data/cockpit.accounts.php';
if (file_exists($accountsFile)) {
    unlink($accountsFile);
    echo "Accounts reset! Go to the home page to create a new admin account.";
} else {
    echo "No accounts file found. Go to /install to set up Cockpit.";
}
