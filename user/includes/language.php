<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ZAKKATPRO Language System
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Supported Languages
|--------------------------------------------------------------------------
*/

$supportedLanguages = [
    'en',
    'ha',
    'ar'
];

/*
|--------------------------------------------------------------------------
| Get Selected Language
|--------------------------------------------------------------------------
*/

$currentLanguage = $_GET['lang']
    ?? $_SESSION['language']
    ?? 'en';

if (!in_array($currentLanguage, $supportedLanguages, true)) {
    $currentLanguage = 'en';
}

/*
|--------------------------------------------------------------------------
| Save Language
|--------------------------------------------------------------------------
*/

$_SESSION['language'] = $currentLanguage;

/*
|--------------------------------------------------------------------------
| Load Translation File
|--------------------------------------------------------------------------
*/

$translationFile = __DIR__ . '/../lang/' . $currentLanguage . '.php';

if (file_exists($translationFile)) {
    $translations = require $translationFile;
} else {
    $translations = require __DIR__ . '/../lang/en.php';
}

/*
|--------------------------------------------------------------------------
| Translation Function
|--------------------------------------------------------------------------
*/

if (!function_exists('__')) {

    function __(string $key, string $default = ''): string
    {
        global $translations;

        if (isset($translations[$key])) {
            return $translations[$key];
        }

        return $default !== '' ? $default : $key;
    }
}

/*
|--------------------------------------------------------------------------
| Language Direction
|--------------------------------------------------------------------------
*/

$direction = $currentLanguage === 'ar' ? 'rtl' : 'ltr';