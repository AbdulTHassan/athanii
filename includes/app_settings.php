
<?php
/**
 * Application Settings
 *
 * File: includes/app_settings.php
 *
 * Reads application settings from the
 * existing system_settings database table.
 *
 * Required:
 * - PDO database connection ($pdo)
 * - system_settings table
 */

if (!function_exists('appSetting')) {

    /**
     * Get one setting from the database.
     */
    function appSetting(
        PDO $pdo,
        string $key,
        string $default = ''
    ): string {

        $stmt = $pdo->prepare(
            "SELECT setting_value
             FROM system_settings
             WHERE setting_key = :setting_key
             LIMIT 1"
        );

        $stmt->execute([
            ':setting_key' => $key
        ]);

        $value = $stmt->fetchColumn();

        if ($value === false || $value === null) {
            return $default;
        }

        return (string) $value;
    }
}

if (!function_exists('appName')) {

    /**
     * Get the current software name.
     *
     * Example:
     * app_name = AREF
     */
    function appName(PDO $pdo): string {

        $name = trim(
            appSetting($pdo, 'app_name', 'ZakatPro')
        );

        return $name !== ''
            ? $name
            : 'ZakatPro';
    }
}

if (!function_exists('appSettingNumber')) {

    /**
     * Read a numeric application setting.
     */
    function appSettingNumber(
        PDO $pdo,
        string $key,
        float $default = 0.0
    ): float {

        $value = appSetting(
            $pdo,
            $key,
            (string) $default
        );

        return is_numeric($value)
            ? (float) $value
            : $default;
    }
}

if (!function_exists('appSettingBool')) {

    /**
     * Read a boolean application setting.
     */
    function appSettingBool(
        PDO $pdo,
        string $key,
        bool $default = false
    ): bool {

        $value = strtolower(
            trim(appSetting($pdo, $key, $default ? '1' : '0'))
        );

        return in_array(
            $value,
            ['1', 'true', 'yes', 'on'],
            true
        );
    }
}
