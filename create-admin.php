<?php
$user = [
    'user' => 'admin',
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'active' => true,
    'group' => 'admin',
    'password' => password_hash('admin123', PASSWORD_DEFAULT)
];

// Ensure directory exists
@mkdir(__DIR__.'/storage/data', 0777, true);

// Write accounts file directly
file_put_contents(
    __DIR__.'/storage/data/cockpit.accounts.php', 
    '<?php return '.var_export(['admin' => $user], true).';'
);

echo "Admin account created!<br>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<a href=\"/\">Go to login</a>";
