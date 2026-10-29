<?php
/**
 * KAZWAB agriculture zakat estimate. PHP 8+.
 * Nisab: five awsuq. KG conversion is a configurable policy decision.
 * Never use this estimate as proof of donation or payment.
 */
if (!function_exists('calculateAgricultureZakat')) {
    function calculateAgricultureZakat($amount, $unit, $irrigation, $customWeight = 0): array
    {
        $amount = (float)$amount;
        $customWeight = (float)$customWeight;
        $factors = ['kg' => 1.0, 'bag50' => 50.0, 'bag100' => 100.0, 'ton' => 1000.0];
        if (!is_finite($amount) || $amount <= 0) {
            return ['success' => false, 'message' => 'Enter a valid harvest amount greater than zero.'];
        }
        if ($unit === 'custom') {
            if (!is_finite($customWeight) || $customWeight <= 0) {
                return ['success' => false, 'message' => 'Enter a valid weight in kg for the custom unit.'];
            }
            $factor = $customWeight;
        } elseif (isset($factors[$unit])) {
            $factor = $factors[$unit];
        } else {
            return ['success' => false, 'message' => 'Select a valid measurement unit.'];
        }
        if (!in_array($irrigation, ['rain', 'artificial', 'mixed'], true)) {
            return ['success' => false, 'message' => 'Select an irrigation method.'];
        }
        $totalKg = $amount * $factor;
        if (!is_finite($totalKg) || $totalKg <= 0) {
            return ['success' => false, 'message' => 'Harvest quantity is out of range.'];
        }
        // KAZWAB must approve the chosen conversion of five awsuq to kg.
        // This can be overridden in a central config: define('KAZWAB_AGRI_NISAB_KG', ...).
        $nisabKg = defined('KAZWAB_AGRI_NISAB_KG') ? (float)KAZWAB_AGRI_NISAB_KG : 653.0;
        if (!is_finite($nisabKg) || $nisabKg <= 0) {
            return ['success' => false, 'message' => 'Agriculture nisab is not configured correctly.'];
        }
        $eligible = $totalKg >= $nisabKg;
        if ($irrigation === 'mixed' && $eligible) {
            return [
                'success' => false,
                'message' => 'Mixed irrigation requires review of the irrigation proportions and the fiqh policy approved by KAZWAB. Do not apply an automatic rate.'
            ];
        }
        $rate = $irrigation === 'rain' ? 0.10 : 0.05;
        $zakatKg = $eligible ? $totalKg * $rate : 0.0;
        return [
            'success' => true,
            'eligible' => $eligible,
            'total_kg' => $totalKg,
            'nisab_kg' => $nisabKg,
            'rate_label' => $irrigation === 'rain' ? 'Rain / natural irrigation (10%)' : ($irrigation === 'artificial' ? 'Costly irrigation (5%)' : 'Mixed irrigation (review required)'),
            'zakat_kg' => $zakatKg,
            'zakat_display' => $zakatKg / $factor,
            'message' => $eligible ? 'Estimated zakat. Not proof of donation.' : 'Harvest is below the configured nisab.'
        ];
    }
}
