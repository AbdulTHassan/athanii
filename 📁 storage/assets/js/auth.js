/**
 * ============================================================
 * ZakatPro - Auth JavaScript
 * Functions don Login, Register, Forgot Password
 * ============================================================
 */

(function() {
    'use strict';
    
    /**
     * Toggle password (Login/Register/Reset)
     */
    window.togglePassword = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (!input || !icon) return;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };
    
    /**
     * Password strength indicator
     */
    window.checkPasswordStrength = function(inputId, barId, textId) {
        const input = document.getElementById(inputId);
        const bar = document.getElementById(barId);
        const text = document.getElementById(textId);
        
        if (!input || !bar || !text) return;
        
        input.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let label = '';
            let color = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (password.length === 0) {
                bar.style.width = '0%';
                text.textContent = '';
                return;
            }
            
            switch(strength) {
                case 0:
                case 1:
                    label = 'Weak';
                    color = 'bg-red-500';
                    bar.style.width = '20%';
                    break;
                case 2:
                    label = 'Fair';
                    color = 'bg-orange-500';
                    bar.style.width = '40%';
                    break;
                case 3:
                    label = 'Good';
                    color = 'bg-yellow-500';
                    bar.style.width = '60%';
                    break;
                case 4:
                    label = 'Strong';
                    color = 'bg-lime-500';
                    bar.style.width = '80%';
                    break;
                case 5:
                    label = 'Very Strong';
                    color = 'bg-green-600';
                    bar.style.width = '100%';
                    break;
            }
            
            bar.className = 'h-1 rounded-full transition-all duration-300 ' + color;
            text.textContent = label;
            text.className = 'text-xs mt-1 ' + (strength >= 4 ? 'text-green-600' : strength >= 3 ? 'text-yellow-600' : 'text-red-600');
        });
    };
    
    /**
     * Confirm password match
     */
    window.checkPasswordMatch = function(passwordId, confirmId, indicatorId) {
        const password = document.getElementById(passwordId);
        const confirm = document.getElementById(confirmId);
        const indicator = document.getElementById(indicatorId);
        
        if (!password || !confirm || !indicator) return;
        
        confirm.addEventListener('input', function() {
            if (this.value === '') {
                indicator.textContent = '';
                return;
            }
            
            if (this.value === password.value) {
                indicator.innerHTML = '<i class="fas fa-check-circle text-green-600"></i> Passwords sun dace';
                indicator.className = 'text-xs mt-1 text-green-600';
            } else {
                indicator.innerHTML = '<i class="fas fa-times-circle text-red-600"></i> Passwords ba su dace ba';
                indicator.className = 'text-xs mt-1 text-red-600';
            }
        });
    };
    
    /**
     * Auto-focus first input
     */
    document.addEventListener('DOMContentLoaded', function() {
        const firstInput = document.querySelector('input[autofocus]');
        if (firstInput) {
            firstInput.focus();
        }
    });
    
})();