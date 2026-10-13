<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Islamic Zakat Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .slide {
            transition: opacity 0.5s ease-in-out;
        }
        
        .dot.active {
            opacity: 1 !important;
            background-color: #16a34a !important;
        }
        
        .hover-scale {
            transition: transform 0.3s ease-in-out;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left section -->
            <div class="flex items-center">
                <a href="index.php" class="flex items-center">
                    <span class="text-2xl font-bold text-green-600">Zakat</span>
                    <span class="text-2xl font-bold text-gray-700">System</span>
                </a>
            </div>

            <!-- Right section -->
            <div class="flex items-center space-x-4">
                <?php if (isLoggedIn()): ?>
                    <!-- User is logged in -->
                    <span class="text-sm text-gray-700 hidden md:inline-block">
                        Welcome, <span class="font-semibold text-green-600"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
                    </span>
                    <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">
                        Dashboard
                    </a>
                    <a href="calculate.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        Calculate Zakat
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-red-600 transition">
                        Logout
                    </a>
                <?php else: ?>
                    <!-- User is not logged in -->
                    <a href="login.php" class="text-gray-700 hover:text-green-600 transition">
                        Login
                    </a>
                    <a href="register.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main>