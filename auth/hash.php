<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> " . $password . "</p>";
echo "<p><strong>Hash:</strong></p>";
echo "<code style='background:#f0f0f0;padding:10px;display:block;word-break:break-all;font-size:12px;'>";
echo $hash;
echo "</code>";

echo "<br><br>";
echo "<h3>Copy wannan hash ɗin a database:</h3>";
echo "<p>Sannan ka yi UPDATE a phpMyAdmin:</p>";
echo "<code style='background:#fff3cd;padding:10px;display:block;word-break:break-all;font-size:12px;'>";
echo "UPDATE users SET password = '" . $hash . "' WHERE email = 'admin@zakatpro.com';";
echo "</code>";

echo "<br><br>";
echo "<h3>Don user@zakatpro.com (password: user123):</h3>";
$hash2 = password_hash('user123', PASSWORD_DEFAULT);
echo "<code style='background:#fff3cd;padding:10px;display:block;word-break:break-all;font-size:12px;'>";
echo "UPDATE users SET password = '" . $hash2 . "' WHERE email = 'user@zakatpro.com';";
echo "</code>";