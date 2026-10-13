<?php
$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$userPass  = password_hash('user123', PASSWORD_DEFAULT);

echo '<h2>Password Hashes</h2>';
echo '<p><strong>Admin (admin123):</strong></p>';
echo '<code style="background:#f0f0f0;padding:10px;display:block;word-break:break-all;">' . $adminPass . '</code>';

echo '<p><strong>User (user123):</strong></p>';
echo '<code style="background:#f0f0f0;padding:10px;display:block;word-break:break-all;">' . $userPass . '</code>';

echo '<hr>';
echo '<h3>Copy SQL ɗin nan a phpMyAdmin (decission_i):</h3>';
echo '<pre style="background:#fff3cd;padding:15px;border-radius:8px;overflow-x:auto;">';
echo "USE decission_i;\n\n";
echo "UPDATE users SET password = '$adminPass' WHERE email = 'admin@zakatpro.com';\n\n";
echo "UPDATE users SET password = '$userPass' WHERE email = 'user@zakatpro.com';\n";
echo '</pre>';