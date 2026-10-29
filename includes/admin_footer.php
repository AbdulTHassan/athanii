<?php
/**
 * ZakatPro Admin Footer
 * 
 * Closes all HTML tags opened in admin_header.php
 * Includes Chart.js and common JavaScript
 */
?>

        </main>
        <!-- End Page Content -->

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 px-6 py-4 mt-auto">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
                <p>
                    © <?php echo date('Y'); ?> 
                    <strong class="text-gray-700"><?= e($softwareName ?? appName($pdo)) ?></strong>. 
                    All rights reserved.
                </p>
                <p class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        System Online
                    </span>
                    <span class="text-gray-300">|</span>
                    <span>Version v2.0.0</span>
                </p>
            </div>
        </footer>

    </div>
    <!-- End Main Area -->

</div>
<!-- End min-h-screen flex -->


<!-- ============================================================
     SCRIPTS
     ============================================================ -->

<!-- Chart.js (for dashboard charts) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Common Admin JS -->
<script>
/**
 * ============================================================
 * MOBILE SIDEBAR TOGGLE
 * ============================================================
 */
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    if (!sidebar) return;
    
    sidebar.classList.toggle('-translate-x-full');
    sidebar.classList.toggle('translate-x-0');
}

/**
 * ============================================================
 * CLOSE SIDEBAR ON LINK CLICK (mobile only)
 * ============================================================
 */
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('adminSidebar');
    if (!sidebar) return;
    
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                if (!sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }
        });
    });
});

/**
 * ============================================================
 * ADMIN DROPDOWN
 * ============================================================
 */
function toggleAdminDropdown() {
    const menu = document.getElementById('adminDropdown');
    if (menu) menu.classList.toggle('hidden');
}

/**
 * ============================================================
 * NOTIFICATIONS DROPDOWN
 * ============================================================
 */
function toggleNotifications() {
    const menu = document.getElementById('notificationsDropdown');
    if (menu) menu.classList.toggle('hidden');
}

/**
 * ============================================================
 * CLOSE DROPDOWNS ON CLICK OUTSIDE
 * ============================================================
 */
document.addEventListener('click', function(e) {
    // Admin dropdown
    const adminMenu = document.getElementById('adminDropdown');
    const adminTrigger = document.getElementById('adminTrigger');
    if (adminMenu && adminTrigger && !adminTrigger.contains(e.target) && !adminMenu.contains(e.target)) {
        adminMenu.classList.add('hidden');
    }
    
    // Notifications dropdown
    const notifMenu = document.getElementById('notificationsDropdown');
    const notifTrigger = document.getElementById('notificationsTrigger');
    if (notifMenu && notifTrigger && !notifTrigger.contains(e.target) && !notifMenu.contains(e.target)) {
        notifMenu.classList.add('hidden');
    }
});

/**
 * ============================================================
 * AUTO-DISMISS FLASH MESSAGES
 * ============================================================
 */
document.addEventListener('DOMContentLoaded', function() {
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s, transform 0.5s';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    });
});

/**
 * ============================================================
 * FORMAT CURRENCY (helper for charts)
 * ============================================================
 */
function formatNaira(value) {
    return '₦' + Number(value).toLocaleString('en-NG', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

/**
 * ============================================================
 * CONSOLE WELCOME
 * ============================================================
 */
console.log(
    '%c🕌 ZakatPro Admin',
    'font-size: 16px; color: #059669; font-weight: bold;'
);
console.log(
    '%cVersion 2.0.0 — Dashboard',
    'font-size: 11px; color: #6b7280;'
);
</script>

</body>
</html>