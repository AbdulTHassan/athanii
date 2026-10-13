<?php
// includes/language.php
declare(strict_types=1);

/**
 * ============================================================
 * ZakatPro - Language System
 * ============================================================
 */

define('AVAILABLE_LANGUAGES', [
    'en' => [
        'name'   => 'English',
        'native' => 'English',
        'flag'   => '🇬🇧',
        'dir'    => 'ltr',
    ],
    'ha' => [
        'name'   => 'Hausa',
        'native' => 'Hausa',
        'flag'   => '🇳🇬',
        'dir'    => 'ltr',
    ],
    'ar' => [
        'name'   => 'Arabic',
        'native' => 'العربية',
        'flag'   => '🇸🇦',
        'dir'    => 'rtl',
    ],
]);

define('DEFAULT_LANGUAGE', 'en');

/**
 * Detect and set current language
 */
function detectLanguage(): string {
    // 1. URL parameter (?lang=ha)
    if (!empty($_GET['lang']) && array_key_exists($_GET['lang'], AVAILABLE_LANGUAGES)) {
        $lang = $_GET['lang'];
        $_SESSION['language'] = $lang;
        
        setcookie('zakatpro_lang', $lang, [
            'expires'  => time() + (365 * 24 * 60 * 60),
            'path'     => '/',
            'samesite' => 'Lax',
        ]);
        
        // Update in database if user logged in
        if (!empty($_SESSION['user_id'])) {
            global $pdo;
            $pdo->prepare("UPDATE users SET language = ? WHERE id = ?")
                ->execute([$lang, $_SESSION['user_id']]);
        }
        
        return $lang;
    }
    
    // 2. Session
    if (!empty($_SESSION['language']) && array_key_exists($_SESSION['language'], AVAILABLE_LANGUAGES)) {
        return $_SESSION['language'];
    }
    
    // 3. Cookie
    if (!empty($_COOKIE['zakatpro_lang']) && array_key_exists($_COOKIE['zakatpro_lang'], AVAILABLE_LANGUAGES)) {
        $_SESSION['language'] = $_COOKIE['zakatpro_lang'];
        return $_COOKIE['zakatpro_lang'];
    }
    
    // 4. User preference from database
    if (!empty($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT language FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $dbLang = $stmt->fetchColumn();
        
        if ($dbLang && array_key_exists($dbLang, AVAILABLE_LANGUAGES)) {
            $_SESSION['language'] = $dbLang;
            return $dbLang;
        }
    }
    
    // 5. Default
    $_SESSION['language'] = DEFAULT_LANGUAGE;
    return DEFAULT_LANGUAGE;
}

/**
 * Load translations for current language
 */
function loadTranslations(string $lang): array {
    static $cache = [];
    
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }
    
    $file = BASE_PATH . "/lang/$lang.php";
    
    if (!file_exists($file)) {
        $file = BASE_PATH . '/lang/' . DEFAULT_LANGUAGE . '.php';
    }
    
    $translations = file_exists($file) ? require $file : [];
    $cache[$lang] = $translations;
    
    return $translations;
}

/**
 * Translate a key
 */
function __(string $key, array $replace = []): string {
    static $translations = null;
    
    if ($translations === null) {
        $lang = $_SESSION['language'] ?? DEFAULT_LANGUAGE;
        $translations = loadTranslations($lang);
    }
    
    $text = $translations[$key] ?? $key;
    
    foreach ($replace as $k => $v) {
        $text = str_replace('{' . $k . '}', (string)$v, $text);
    }
    
    return $text;
}

/**
 * Echo a translated string
 */
function _e(string $key, array $replace = []): void {
    echo __($key, $replace);
}

/**
 * Get current language code
 */
function currentLanguage(): string {
    return $_SESSION['language'] ?? DEFAULT_LANGUAGE;
}

/**
 * Get current language info
 */
function currentLanguageInfo(): array {
    $lang = currentLanguage();
    return AVAILABLE_LANGUAGES[$lang] ?? AVAILABLE_LANGUAGES[DEFAULT_LANGUAGE];
}

/**
 * Get text direction (ltr or rtl)
 */
function currentDirection(): string {
    return currentLanguageInfo()['dir'];
}

/**
 * Generate URL to switch language
 */
function languageSwitchUrl(string $lang): string {
    $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
    $url = strtok($currentUrl, '?');
    
    parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
    $params['lang'] = $lang;
    
    return $url . '?' . http_build_query($params);
}