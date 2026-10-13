/**
 * ============================================================
 * ZakatPro - Admin JavaScript
 * ============================================================
 */

(function() {
    'use strict';
    
    /**
     * Toggle sidebar (mobile)
     */
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('admin-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed');
        }
    };
    
    /**
     * Confirm delete
     */
    window.confirmDelete = function(itemName) {
        return confirm('Ka tabbata kana son share "' + (itemName || 'wannan') + '"?\n\nWannan aiki ba zai iya juyawa ba.');
    };
    
    /**
     * Confirm approve/confirm
     */
    window.confirmApprove = function(action, itemName) {
        return confirm('Ka tabbata kana son ' + action + ' "' + (itemName || 'wannan') + '"?');
    };
    
})();