/**
 * ============================================================
 * ZakatPro - Calculator JavaScript
 * ============================================================
 */

(function() {
    'use strict';
    
    /**
     * Format number
     */
    window.formatNumber = function(num) {
        return parseFloat(num || 0).toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };
    
    /**
     * Calculate zakat preview
     */
    window.previewZakat = function(amount) {
        return (parseFloat(amount) || 0) * 0.025;
    };
    
})();