<?php
// user/_header.php
$unread = $unread ?? 0;
$user = $user ?? currentUser();
?>
<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="flex items-center justify-between px-6 py-3">
        
        <!-- Left: Search -->
        <div class="flex-1 max-w-lg">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       placeholder="Search anything..."
                       class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-full 
                              text-sm focus:outline-none focus:ring-2 focus:ring-green-500 
                              focus:border-transparent">
            </div>
        </div>
        
        <!-- Right: Notifications, Language, Profile -->
        <div class="flex items-center gap-3">
            
            <!-- Notifications -->
            <a href="notifications.php" class="relative p-2 rounded-full hover:bg-gray-100">
                <i class="fas fa-bell text-gray-600 text-lg"></i>
                <?php if ($unread > 0): ?>
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-xs 
                                 w-5 h-5 rounded-full flex items-center justify-center font-bold">
                        <?php echo $unread; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <!-- Language -->
            <?php include BASE_PATH . '/includes/components/language-switcher.php'; ?>
            
            <!-- Profile -->
            <div class="flex items-center gap-3 pl-3 border-l border-gray-200">
                <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <div class="hidden md:block">
                    <p class="text-sm font-semibold text-gray-800"><?php echo e($user['full_name']); ?></p>
                    <p class="text-xs text-gray-500">Individual</p>
                </div>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </div>
        </div>
    </div>
</header>