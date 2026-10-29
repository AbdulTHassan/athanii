
    
  
<?php
/**
 * ZakatPro free Nisab service.
 * Can be included by calculator.php or opened directly for JSON.
 */

if (!function_exists('zakatGetNisab')) {
    function zakatApiRequest(string $url): ?array
    {
        if (!function_exists('curl_init')) {
            return null;
        }
        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!is_string($body) || $status !== 200) {
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
    }

    function zakatValidRate($value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }
        $number = (float) $value;
        return is_finite($number) && $number > 0 ? $number : null;
    }

    function zakatGetNisab(): array
    {
        $cachePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'zakatpro_nisab_' . hash('sha256', __DIR__) . '.json';
        $cache = null;
        if (is_readable($cachePath)) {
            $raw = @file_get_contents($cachePath);
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($decoded) && isset($decoded['fetched_at'], $decoded['prices'])
                && is_array($decoded['prices'])) {
                $valid = true;
                foreach (['gold_usd_oz', 'silver_usd_oz', 'usd_ngn', 'usd_eur'] as $key) {
                    if (zakatValidRate($decoded['prices'][$key] ?? null) === null) {
                        $valid = false;
                        break;
                    }
                }
                if ($valid && is_numeric($decoded['fetched_at'])
                    && (int) $decoded['fetched_at'] <= time()) {
                    $cache = $decoded;
                }
            }
        }

        $now = time();
        $prices = null;
        $fetchedAt = null;
        $source = 'unavailable';
        $age = $cache === null ? PHP_INT_MAX : $now - (int) $cache['fetched_at'];

        if ($cache !== null && $age < 1800) {
            $prices = $cache['prices'];
            $fetchedAt = (int) $cache['fetched_at'];
            $source = 'cache';
        } else {
            $gold = zakatApiRequest('https://api.gold-api.com/price/XAU');
            $silver = zakatApiRequest('https://api.gold-api.com/price/XAG');
            $fx = zakatApiRequest('https://open.er-api.com/v6/latest/USD');
            $goldUsd = zakatValidRate($gold['price'] ?? null);
            $silverUsd = zakatValidRate($silver['price'] ?? null);
            $usdNgn = ($fx['result'] ?? '') === 'success'
                ? zakatValidRate($fx['rates']['NGN'] ?? null) : null;
            $usdEur = ($fx['result'] ?? '') === 'success'
                ? zakatValidRate($fx['rates']['EUR'] ?? null) : null;

            if ($goldUsd !== null && $silverUsd !== null
                && $usdNgn !== null && $usdEur !== null) {
                $prices = [
                    'gold_usd_oz' => $goldUsd,
                    'silver_usd_oz' => $silverUsd,
                    'usd_ngn' => $usdNgn,
                    'usd_eur' => $usdEur,

