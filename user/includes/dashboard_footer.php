<?php
// Footer for dashboard pages
?>
    </div>
</div>

<!-- Sidebar Toggle JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    let isCollapsed = false;
    let isMobile = window.innerWidth <= 1024;
    
    // Toggle sidebar
    toggleBtn.addEventListener('click', function() {
        if (isMobile) {
            // Mobile: toggle open/close
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        } else {
            // Desktop: toggle collapse
            isCollapsed = !isCollapsed;
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });
    
    // Close sidebar on overlay click (mobile)
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth <= 1024;
        
        if (wasMobile !== isMobile) {
            if (isMobile) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }
        }
    });
    
    // Keyboard shortcut: Alt + S to toggle sidebar
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            toggleBtn.click();
        }
    });
    
    // Auto-hide sidebar on mobile when clicking a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (isMobile) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
});
</script>

</body>
</html>