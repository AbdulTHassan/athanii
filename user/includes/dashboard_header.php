<?php
// This file includes the header part for dashboard pages
// It should be included after the page-specific PHP code

// Get current page for active link
$current_page = $current_page ?? 'dashboard';

// Get user data
$user = getCurrentUser($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Zakat System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #065f46 0%, #047857 50%, #059669 100%);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-icon {
            margin-right: 0;
        }
        
        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }
        
        .sidebar.collapsed .logo-text {
            display: none;
        }
        
        .sidebar.collapsed .logo-icon {
            font-size: 2rem;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Nav Links */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 4px 12px;
            position: relative;
            text-decoration: none;
            gap: 12px;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 30px;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
        }
        
        .nav-link i {
            width: 24px;
            font-size: 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }
        
        .nav-link .sidebar-text {
            font-size: 0.9rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .nav-link .badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 12px 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: #f3f4f6;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        /* Top Bar */
        .topbar {
            background: white;
            padding: 16px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Toggle Button */
        .toggle-btn {
            background: none;
            border: none;
            color: #1f2937;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .toggle-btn:hover {
            background: #f3f4f6;
            transform: rotate(90deg);
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(4px);
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            .sidebar.collapsed {
                width: 280px !important;
            }
            
            .sidebar.collapsed .sidebar-text {
                display: inline !important;
            }
            
            .sidebar.collapsed .sidebar-icon {
                margin-right: 12px !important;
            }
            
            .sidebar.collapsed .nav-item {
                justify-content: flex-start !important;
                padding: 12px 20px !important;
            }
            
            .sidebar.collapsed .logo-text {
                display: inline !important;
            }
            
            .sidebar.collapsed .logo-icon {
                font-size: 1.5rem !important;
            }
        }
        
        @media (max-width: 640px) {
            .topbar {
                padding: 12px 16px;
            }
            
            .stat-card {
                padding: 16px;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .fade-in-up-delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }
        
        .fade-in-up-delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }
        
        .fade-in-up-delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }
        
        .fade-in-up-delay-4 {
            animation-delay: 0.4s;
            opacity: 0;
        }
    </style>
</head>
<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="flex items-center p-6 border-b border-white/10">
        <div class="logo-icon text-3xl text-white"><i class="fa-solid fa-mosque"></i></div>
        <div class="logo-text ml-3">
            <h1 class="text-white font-bold text-xl">ZakatPro</h1>
           
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-20 h-10 rounded-[5px] bg-white/20 flex items-center justify-center text-white text-xl font-semibold">
                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 2)); ?>
            </div>
            <div class="sidebar-text">
                <p class="text-white font-semibold text-sm"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></p>
                <p class="text-white/60 text-xs"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="p-4">
        <p class="text-white/40 text-xs uppercase tracking-wider font-semibold mb-4 sidebar-text">Main Menu</p>
        
        <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-th-large sidebar-icon"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        
        <a href="calculate.php" class="nav-link <?php echo $current_page == 'calculate' ? 'active' : ''; ?>">
            <i class="fas fa-calculator sidebar-icon"></i>
            <span class="sidebar-text">Calculate Zakat</span>
            <span class="badge sidebar-text">New</span>
        </a>
        
        <a href="history.php" class="nav-link <?php echo $current_page == 'history' ? 'active' : ''; ?>">
            <i class="fas fa-history sidebar-icon"></i>
            <span class="sidebar-text">History</span>
        </a>
        
        <a href="print.php" class="nav-link <?php echo $current_page == 'print' ? 'active' : ''; ?>">
            <i class="fas fa-print sidebar-icon"></i>
            <span class="sidebar-text">Print Report</span>
        </a>
        
        <div class="nav-divider sidebar-text"></div>
        
        <p class="text-white/40 text-xs uppercase tracking-wider font-semibold mb-4 sidebar-text">Support</p>
        
        <a href="contact.php" class="nav-link <?php echo $current_page == 'contact' ? 'active' : ''; ?>">
            <i class="fas fa-headset sidebar-icon"></i>
            <span class="sidebar-text">Contact Admin</span>
        </a>
        
        <a href="profile.php" class="nav-link <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
            <i class="fas fa-user-cog sidebar-icon"></i>
            <span class="sidebar-text">Profile Settings</span>
        </a>
        
        <div class="nav-divider sidebar-text"></div>
        
        <a href="logout.php" class="nav-link" style="color: rgba(255, 107, 107, 0.7);">
            <i class="fas fa-sign-out-alt sidebar-icon"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </nav>
    
    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10 sidebar-text">
        <p class="text-white/40 text-xs text-center">© 2024 Zakat System</p>
        <p class="text-white/30 text-xs text-center mt-1">v2.0.0</p>
    </div>
</aside>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Top Bar -->
    <header class="topbar">
        <div class="flex items-center gap-4">
            <button class="toggle-btn" id="toggleSidebar" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    <?php echo ucfirst($current_page); ?>
                </h2>
                <p class="text-sm text-gray-500">Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Notification Bell -->
            <button class="relative text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
            </button>
            
            <!-- User Menu (Desktop) -->
            <div class="hidden md:flex items-center gap-3">
                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></span>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 2)); ?>
                </div>
            </div>
        </div>
    </header>