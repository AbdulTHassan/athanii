<?php

/**
 * Agriculture Zakat Calculation Logic
 *
 * This file contains the calculation functions only.
 * No HTML.
 * No JavaScript.
 */


/*
|--------------------------------------------------------------------------
| Agriculture Nisab
|--------------------------------------------------------------------------
|
| The fiqh basis is 5 Wasqs.
|
| The modern kg equivalent is kept here in one place
| so it can easily be updated later.
|
*/

const AGRICULTURE_NISAB_KG = 609.84;


/*
|--------------------------------------------------------------------------
| Convert User Measurement To Kilograms
|--------------------------------------------------------------------------
*/

function agricultureToKg(float $amount, string $unit, float $customWeight = 0): float
{
    switch ($unit) {

        case 'kg':
            return $amount;

        case 'bag50':
            return $amount * 50;

        case 'bag100':
            return $amount * 100;

        case 'ton':
            return $amount * 1000;

        case 'custom':

            if ($customWeight <= 0) {
                return 0;
            }

            return $amount * $customWeight;

        default:
            return 0;
    }
}


/*
|--------------------------------------------------------------------------
| Irrigation Rate
|--------------------------------------------------------------------------
*/

function getAgricultureRate(string $irrigation): float
{
    switch ($irrigation) {

        case 'rain':
            return 0.10;

        case 'artificial':
            return 0.05;

        case 'mixed':
            return 0.075;

        default:
            return 0;
    }
}


/*
|--------------------------------------------------------------------------
| Irrigation Name
|--------------------------------------------------------------------------
*/

function getAgricultureRateLabel(string $irrigation): string
{
    switch ($irrigation) {

        case 'rain':
            return '10% — Natural / Rain-fed';

        case 'artificial':
            return '5% — Artificial Irrigation';

        case 'mixed':
            return '7.5% — Mixed Irrigation';

        default:
            return 'Not selected';
    }
}


/*
|--------------------------------------------------------------------------
| Convert Zakat KG Back To User Unit
|--------------------------------------------------------------------------
*/

function agricultureFromKg(float $kg, string $unit, float $customWeight = 0): float
{
    switch ($unit) {

        case 'kg':
            return $kg;

        case 'bag50':
            return $kg / 50;

        case 'bag100':
            return $kg / 100;

        case 'ton':
            return $kg / 1000;

        case 'custom':

            if ($customWeight <= 0) {
                return 0;
            }

            return $kg / $customWeight;

        default:
            return $kg;
    }
}


/*
|--------------------------------------------------------------------------
| Main Agriculture Zakat Calculation
|--------------------------------------------------------------------------
*/

function calculateAgricultureZakat(
    float $amount,
    string $unit,
    string $irrigation,
    float $customWeight = 0
): array {

    /*
     * Validate amount
     */

    if ($amount <= 0) {

        return [
            'success' => false,
            'message' => 'Please enter a valid harvest amount.'
        ];
    }


    /*
     * Convert harvest to KG
     */

    $totalKg = agricultureToKg(
        $amount,
        $unit,
        $customWeight
    );


    /*
     * Invalid measurement
     */

    if ($totalKg <= 0) {

        return [
            'success' => false,
            'message' => 'Please provide a valid measurement.'
        ];
    }


    /*
     * Check Nisab
     */

    if ($totalKg < AGRICULTURE_NISAB_KG) {

        return [

            'success' => true,

            'eligible' => false,

            'message' => 'The harvest has not reached the agricultural Nisab.',

            'total_kg' => $totalKg,

            'nisab_kg' => AGRICULTURE_NISAB_KG,

            'zakat_kg' => 0,

            'zakat_display' => 0,

            'rate' => 0,

            'rate_label' => 'No Zakat due'
        ];
    }


    /*
     * Get irrigation rate
     */

    $rate = getAgricultureRate($irrigation);


    /*
     * Validate irrigation
     */

    if ($rate <= 0) {

        return [

            'success' => false,

            'message' => 'Please select an irrigation method.'
        ];
    }


    /*
     * Calculate Zakat
     */

    $zakatKg = $totalKg * $rate;


    /*
     * Convert result back to user's unit
     */

    $zakatDisplay = agricultureFromKg(
        $zakatKg,
        $unit,
        $customWeight
    );


    /*
     * Return complete result
     */

    return [

        'success' => true,

        'eligible' => true,

        'message' => 'Zakat is due on this harvest.',

        'total_kg' => $totalKg,

        'nisab_kg' => AGRICULTURE_NISAB_KG,

        'zakat_kg' => $zakatKg,

        'zakat_display' => $zakatDisplay,

        'rate' => $rate,

        'rate_label' => getAgricultureRateLabel($irrigation)
    ];
}