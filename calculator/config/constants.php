<?php
// config/constants.php
declare(strict_types=1);

define('SITE_NAME', 'ZakatPro');
define('SITE_URL',  'http://localhost/athanii');
define('ADMIN_URL', SITE_URL . '/admin');

define('BASE_PATH',    dirname(__DIR__));
define('UPLOAD_PATH',  BASE_PATH . '/assets/uploads');
define('RECEIPT_PATH', UPLOAD_PATH . '/receipts');

define('ZAKAT_RATE',         2.5);
define('NISAB_GOLD_GRAMS',   85);
define('NISAB_SILVER_GRAMS', 595);