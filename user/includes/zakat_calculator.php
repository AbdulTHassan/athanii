<?php
/**
 * Zakat Calculator Library
 * Complete Zakat calculation system for different types of wealth
 */

class ZakatCalculator {
    private $nisab_gold = 85; // 85 grams of gold
    private $nisab_silver = 595; // 595 grams of silver
    private $gold_price_per_gram = 60; // USD
    private $silver_price_per_gram = 0.80; // USD
    private $zakat_rate = 0.025; // 2.5%
    
    /**
     * Set Nisab values from database
     */
    public function setNisab($gold_nisab, $silver_nisab, $gold_price, $silver_price) {
        $this->nisab_gold = $gold_nisab;
        $this->nisab_silver = $silver_nisab;
        $this->gold_price_per_gram = $gold_price;
        $this->silver_price_per_gram = $silver_price;
    }
    
    /**
     * Get current Nisab value in monetary terms (using silver as default)
     */
    public function getNisabValue() {
        return $this->nisab_silver * $this->silver_price_per_gram;
    }
    
    /**
     * Check if wealth meets Nisab threshold
     */
    public function isEligible($wealth) {
        return $wealth >= $this->getNisabValue();
    }
    
    /**
     * Calculate Zakat on Cash/Money
     */
    public function calculateCashZakat($cash_in_hand, $bank_savings, $other_income) {
        $result = [
            'type' => 'Cash/Money Zakat',
            'inputs' => [
                'cash_in_hand' => $cash_in_hand,
                'bank_savings' => $bank_savings,
                'other_income' => $other_income
            ],
            'calculation_steps' => [],
            'total_wealth' => 0,
            'nisab_status' => false,
            'zakat_due' => 0,
            'details' => []
        ];
        
        // Calculate total wealth
        $total_wealth = $cash_in_hand + $bank_savings + $other_income;
        $result['total_wealth'] = $total_wealth;
        $result['calculation_steps'][] = "Total Wealth = Cash in Hand ($" . number_format($cash_in_hand, 2) . ") + Bank Savings ($" . number_format($bank_savings, 2) . ") + Other Income ($" . number_format($other_income, 2) . ") = $" . number_format($total_wealth, 2);
        
        // Check Nisab
        $nisab_value = $this->getNisabValue();
        $result['details']['nisab_value'] = $nisab_value;
        $result['calculation_steps'][] = "Nisab Threshold = " . $this->nisab_silver . "g Silver × $" . $this->silver_price_per_gram . " = $" . number_format($nisab_value, 2);
        
        if ($total_wealth >= $nisab_value) {
            $result['nisab_status'] = true;
            $result['zakat_due'] = $total_wealth * $this->zakat_rate;
            $result['calculation_steps'][] = "✅ Wealth meets Nisab threshold";
            $result['calculation_steps'][] = "Zakat = $" . number_format($total_wealth, 2) . " × 2.5% = $" . number_format($result['zakat_due'], 2);
        } else {
            $result['nisab_status'] = false;
            $result['zakat_due'] = 0;
            $result['calculation_steps'][] = "❌ Wealth does NOT meet Nisab threshold (Need: $" . number_format($nisab_value, 2) . ", Have: $" . number_format($total_wealth, 2) . ")";
            $result['calculation_steps'][] = "No Zakat is due on this wealth.";
        }
        
        return $result;
    }
    
    /**
     * Calculate Zakat on Property/Business
     */
    public function calculatePropertyZakat($rental_income, $business_income, $expenses) {
        $result = [
            'type' => 'Property/Business Zakat',
            'inputs' => [
                'rental_income' => $rental_income,
                'business_income' => $business_income,
                'expenses' => $expenses
            ],
            'calculation_steps' => [],
            'total_wealth' => 0,
            'nisab_status' => false,
            'zakat_due' => 0,
            'details' => []
        ];
        
        // Calculate net income
        $total_income = $rental_income + $business_income;
        $net_income = $total_income - $expenses;
        $result['total_wealth'] = $net_income;
        
        $result['calculation_steps'][] = "Total Income = Rental ($" . number_format($rental_income, 2) . ") + Business ($" . number_format($business_income, 2) . ") = $" . number_format($total_income, 2);
        $result['calculation_steps'][] = "Net Income = Total Income ($" . number_format($total_income, 2) . ") - Expenses ($" . number_format($expenses, 2) . ") = $" . number_format($net_income, 2);
        
        // Check Nisab
        $nisab_value = $this->getNisabValue();
        $result['details']['nisab_value'] = $nisab_value;
        $result['calculation_steps'][] = "Nisab Threshold = $" . number_format($nisab_value, 2);
        
        if ($net_income >= $nisab_value) {
            $result['nisab_status'] = true;
            $result['zakat_due'] = $net_income * $this->zakat_rate;
            $result['calculation_steps'][] = "✅ Net income meets Nisab threshold";
            $result['calculation_steps'][] = "Zakat = $" . number_format($net_income, 2) . " × 2.5% = $" . number_format($result['zakat_due'], 2);
        } else {
            $result['nisab_status'] = false;
            $result['zakat_due'] = 0;
            $result['calculation_steps'][] = "❌ Net income does NOT meet Nisab threshold";
            $result['calculation_steps'][] = "No Zakat is due on this income.";
        }
        
        return $result;
    }
    
    /**
     * Calculate Zakat on Agriculture
     */
    public function calculateAgricultureZakat($crop_value, $irrigation_type) {
        $result = [
            'type' => 'Agriculture Zakat',
            'inputs' => [
                'crop_value' => $crop_value,
                'irrigation_type' => $irrigation_type
            ],
            'calculation_steps' => [],
            'total_wealth' => $crop_value,
            'nisab_status' => true,
            'zakat_due' => 0,
            'details' => []
        ];
        
        // Determine zakat rate based on irrigation type
        $rate = 0;
        if ($irrigation_type === 'rain-fed') {
            $rate = 0.10; // 10%
            $rate_text = '10%';
            $result['details']['irrigation_type'] = 'Rain-fed (No cost irrigation)';
        } elseif ($irrigation_type === 'artificial') {
            $rate = 0.05; // 5%
            $rate_text = '5%';
            $result['details']['irrigation_type'] = 'Artificial (Costly irrigation)';
        } else {
            $rate = 0.075; // 7.5% (default middle ground)
            $rate_text = '7.5%';
            $result['details']['irrigation_type'] = 'Mixed irrigation';
        }
        
        $result['details']['zakat_rate'] = $rate_text;
        $result['calculation_steps'][] = "Crop Value: $" . number_format($crop_value, 2);
        $result['calculation_steps'][] = "Irrigation Type: " . $result['details']['irrigation_type'];
        $result['calculation_steps'][] = "Zakat Rate: " . $rate_text;
        
        // Calculate zakat
        $result['zakat_due'] = $crop_value * $rate;
        $result['calculation_steps'][] = "Zakat = $" . number_format($crop_value, 2) . " × " . $rate_text . " = $" . number_format($result['zakat_due'], 2);
        
        // Check if crop meets nisab (5 wasq or 612.36 kg - simplified)
        $min_crop_value = 500; // Minimum value threshold for crops
        if ($crop_value < $min_crop_value) {
            $result['calculation_steps'][] = "⚠️ Note: Crop value is below minimum threshold, but zakat may still be recommended.";
        }
        
        return $result;
    }
    
    /**
     * Calculate Zakat on Livestock
     */
    public function calculateLivestockZakat($animal_type, $quantity) {
        $result = [
            'type' => 'Livestock Zakat',
            'inputs' => [
                'animal_type' => ucfirst($animal_type),
                'quantity' => $quantity
            ],
            'calculation_steps' => [],
            'total_wealth' => 0,
            'nisab_status' => false,
            'zakat_due' => 0,
            'zakat_animals' => 0,
            'details' => []
        ];
        
        $animal_type_lower = strtolower($animal_type);
        
        // Zakat rules for different animals
        switch ($animal_type_lower) {
            case 'goat':
            case 'sheep':
                $result = $this->calculateGoatZakat($quantity, $result);
                break;
            case 'cow':
            case 'ox':
            case 'buffalo':
                $result = $this->calculateCowZakat($quantity, $result);
                break;
            case 'camel':
                $result = $this->calculateCamelZakat($quantity, $result);
                break;
            default:
                $result['calculation_steps'][] = "❌ Unsupported animal type: " . $animal_type;
                $result['zakat_due'] = 0;
                $result['zakat_animals'] = 0;
                return $result;
        }
        
        // Calculate monetary value (approximate)
        $animal_prices = [
            'goat' => 100,
            'sheep' => 120,
            'cow' => 1500,
            'buffalo' => 1800,
            'camel' => 3000
        ];
        
        $price = $animal_prices[$animal_type_lower] ?? 0;
        $result['total_wealth'] = $result['zakat_animals'] * $price;
        $result['zakat_due'] = $result['zakat_animals'] * $price;
        
        if ($result['zakat_animals'] > 0) {
            $result['nisab_status'] = true;
            $result['calculation_steps'][] = "📊 Zakat due: " . $result['zakat_animals'] . " " . ucfirst($animal_type_lower) . "(s)";
            $result['calculation_steps'][] = "💰 Approximate value: $" . number_format($result['total_wealth'], 2);
        } else {
            $result['nisab_status'] = false;
            $result['calculation_steps'][] = "✅ No Zakat is due on this livestock.";
        }
        
        return $result;
    }
    
    /**
     * Goat/Sheep Zakat calculation
     */
    private function calculateGoatZakat($quantity, $result) {
        $zakat_animals = 0;
        $details = [];
        
        if ($quantity >= 1 && $quantity <= 39) {
            $zakat_animals = 0;
            $details['rule'] = "1-39 animals: No zakat";
            $result['calculation_steps'][] = "❌ " . $quantity . " goats: No zakat required (Below nisab)";
        } elseif ($quantity >= 40 && $quantity <= 120) {
            $zakat_animals = 1;
            $details['rule'] = "40-120 animals: 1 goat/sheep";
            $result['calculation_steps'][] = "✅ " . $quantity . " goats: 1 goat/sheep due";
        } elseif ($quantity >= 121 && $quantity <= 200) {
            $zakat_animals = 2;
            $details['rule'] = "121-200 animals: 2 goats/sheep";
            $result['calculation_steps'][] = "✅ " . $quantity . " goats: 2 goats/sheep due";
        } elseif ($quantity >= 201 && $quantity <= 399) {
            $zakat_animals = 3;
            $details['rule'] = "201-399 animals: 3 goats/sheep";
            $result['calculation_steps'][] = "✅ " . $quantity . " goats: 3 goats/sheep due";
        } elseif ($quantity >= 400) {
            $zakat_animals = ceil($quantity / 100);
            $details['rule'] = "400+ animals: 1 goat/sheep per 100 animals";
            $result['calculation_steps'][] = "✅ " . $quantity . " goats: " . $zakat_animals . " goats/sheep due";
        }
        
        $result['zakat_animals'] = $zakat_animals;
        $result['details'] = $details;
        return $result;
    }
    
    /**
     * Cow/Buffalo Zakat calculation
     */
    private function calculateCowZakat($quantity, $result) {
        $zakat_animals = 0;
        $details = [];
        
        if ($quantity >= 1 && $quantity <= 29) {
            $zakat_animals = 0;
            $details['rule'] = "1-29 animals: No zakat";
            $result['calculation_steps'][] = "❌ " . $quantity . " cows: No zakat required (Below nisab)";
        } elseif ($quantity >= 30 && $quantity <= 39) {
            $zakat_animals = 1;
            $details['rule'] = "30-39 animals: 1 yearling calf (1-2 years)";
            $result['calculation_steps'][] = "✅ " . $quantity . " cows: 1 yearling calf due";
        } elseif ($quantity >= 40 && $quantity <= 59) {
            $zakat_animals = 1;
            $details['rule'] = "40-59 animals: 1 two-year old calf";
            $result['calculation_steps'][] = "✅ " . $quantity . " cows: 1 two-year old calf due";
        } elseif ($quantity >= 60 && $quantity <= 69) {
            $zakat_animals = 2;
            $details['rule'] = "60-69 animals: 2 yearling calves";
            $result['calculation_steps'][] = "✅ " . $quantity . " cows: 2 yearling calves due";
        } elseif ($quantity >= 70 && $quantity <= 79) {
            $zakat_animals = 2;
            $details['rule'] = "70-79 animals: 1 yearling + 1 two-year old calf";
            $result['calculation_steps'][] = "✅ " . $quantity . " cows: 1 yearling + 1 two-year old calf due";
        } elseif ($quantity >= 80) {
            $zakat_animals = floor($quantity / 30);
            $details['rule'] = "80+ animals: 1 yearling calf per 30 animals";
            $result['calculation_steps'][] = "✅ " . $quantity . " cows: " . $zakat_animals . " calves due";
        }
        
        $result['zakat_animals'] = $zakat_animals;
        $result['details'] = $details;
        return $result;
    }
    
    /**
     * Camel Zakat calculation
     */
    private function calculateCamelZakat($quantity, $result) {
        $zakat_animals = 0;
        $details = [];
        
        if ($quantity >= 1 && $quantity <= 4) {
            $zakat_animals = 0;
            $details['rule'] = "1-4 camels: No zakat";
            $result['calculation_steps'][] = "❌ " . $quantity . " camels: No zakat required (Below nisab)";
        } elseif ($quantity >= 5 && $quantity <= 9) {
            $zakat_animals = 1;
            $details['rule'] = "5-9 camels: 1 sheep/goat";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 1 sheep/goat due";
        } elseif ($quantity >= 10 && $quantity <= 14) {
            $zakat_animals = 2;
            $details['rule'] = "10-14 camels: 2 sheep/goats";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 2 sheep/goats due";
        } elseif ($quantity >= 15 && $quantity <= 19) {
            $zakat_animals = 3;
            $details['rule'] = "15-19 camels: 3 sheep/goats";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 3 sheep/goats due";
        } elseif ($quantity >= 20 && $quantity <= 24) {
            $zakat_animals = 4;
            $details['rule'] = "20-24 camels: 4 sheep/goats";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 4 sheep/goats due";
        } elseif ($quantity >= 25 && $quantity <= 35) {
            $zakat_animals = 1;
            $details['rule'] = "25-35 camels: 1 one-year old she-camel";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 1 one-year old she-camel due";
        } elseif ($quantity >= 36 && $quantity <= 45) {
            $zakat_animals = 1;
            $details['rule'] = "36-45 camels: 1 two-year old she-camel";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 1 two-year old she-camel due";
        } elseif ($quantity >= 46 && $quantity <= 60) {
            $zakat_animals = 1;
            $details['rule'] = "46-60 camels: 1 three-year old she-camel";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 1 three-year old she-camel due";
        } elseif ($quantity >= 61 && $quantity <= 75) {
            $zakat_animals = 1;
            $details['rule'] = "61-75 camels: 1 four-year old she-camel";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 1 four-year old she-camel due";
        } elseif ($quantity >= 76 && $quantity <= 90) {
            $zakat_animals = 2;
            $details['rule'] = "76-90 camels: 2 two-year old she-camels";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 2 two-year old she-camels due";
        } elseif ($quantity >= 91 && $quantity <= 120) {
            $zakat_animals = 2;
            $details['rule'] = "91-120 camels: 2 three-year old she-camels";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: 2 three-year old she-camels due";
        } elseif ($quantity > 120) {
            $zakat_animals = ceil($quantity / 50);
            $details['rule'] = "120+ camels: 1 four-year old she-camel per 50 animals";
            $result['calculation_steps'][] = "✅ " . $quantity . " camels: " . $zakat_animals . " four-year old she-camels due";
        }
        
        $result['zakat_animals'] = $zakat_animals;
        $result['details'] = $details;
        return $result;
    }
    
    /**
     * Combined Zakat calculation for all types
     */
    public function calculateAllZakat($data) {
        $results = [];
        $total_zakat = 0;
        
        // Cash/Money Zakat
        if (isset($data['cash_in_hand']) || isset($data['bank_savings']) || isset($data['other_income'])) {
            $cash_result = $this->calculateCashZakat(
                $data['cash_in_hand'] ?? 0,
                $data['bank_savings'] ?? 0,
                $data['other_income'] ?? 0
            );
            $results['cash'] = $cash_result;
            $total_zakat += $cash_result['zakat_due'];
        }
        
        // Property/Business Zakat
        if (isset($data['rental_income']) || isset($data['business_income'])) {
            $property_result = $this->calculatePropertyZakat(
                $data['rental_income'] ?? 0,
                $data['business_income'] ?? 0,
                $data['expenses'] ?? 0
            );
            $results['property'] = $property_result;
            $total_zakat += $property_result['zakat_due'];
        }
        
        // Agriculture Zakat
        if (isset($data['crop_value']) && isset($data['irrigation_type'])) {
            $agri_result = $this->calculateAgricultureZakat(
                $data['crop_value'],
                $data['irrigation_type']
            );
            $results['agriculture'] = $agri_result;
            $total_zakat += $agri_result['zakat_due'];
        }
        
        // Livestock Zakat
        if (isset($data['animal_type']) && isset($data['quantity'])) {
            $livestock_result = $this->calculateLivestockZakat(
                $data['animal_type'],
                $data['quantity']
            );
            $results['livestock'] = $livestock_result;
            $total_zakat += $livestock_result['zakat_due'];
        }
        
        // Summary
        $summary = [
            'total_zakat_due' => $total_zakat,
            'total_categories' => count($results),
            'eligible_categories' => 0,
            'ineligible_categories' => 0
        ];
        
        foreach ($results as $result) {
            if ($result['nisab_status']) {
                $summary['eligible_categories']++;
            } else {
                $summary['ineligible_categories']++;
            }
        }
        
        return [
            'results' => $results,
            'summary' => $summary
        ];
    }
}

// Helper function to format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Helper function to get irrigation types
function getIrrigationTypes() {
    return [
        'rain-fed' => 'Rain-fed (10% zakat)',
        'artificial' => 'Artificial Irrigation (5% zakat)',
        'mixed' => 'Mixed (7.5% zakat)'
    ];
}

// Helper function to get animal types
function getAnimalTypes() {
    return [
        'goat' => 'Goat/Sheep',
        'cow' => 'Cow/Buffalo',
        'camel' => 'Camel'
    ];
}
?>