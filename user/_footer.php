
<?php
// user/_footer.php
?>
<footer class="bg-white border-t border-gray-200 mt-8">
    <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <div class="w-8 h-8 bg-green-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-mosque text-white text-sm"></i>
            </div>
            <span class="font-bold text-green-800"><?php echo SITE_NAME; ?></span>
            <span class="text-gray-400">|</span>
            <span class="text-xs">Purify Your Wealth, Help the Ummah</span>
        </div>
        
        <div class="flex items-center gap-4 text-xs text-gray-500">
            <a href="<?php echo SITE_URL; ?>" class="hover:text-green-700">Home</a>
            <span>|</span>
            <a href="<?php echo SITE_URL; ?>/user/calculator.php" class="hover:text-green-700">Calculator</a>
            <span>|</span>
            <a href="<?php echo SITE_URL; ?>/public/knowledge.php" class="hover:text-green-700">Knowledge</a>
            <span>|</span>
            <a href="<?php echo SITE_URL; ?>/public/fatwas.php" class="hover:text-green-700">Fatwas</a>
            <span>|</span>
            <a href="<?php echo SITE_URL; ?>/public/about.php" class="hover:text-green-700">About Us</a>
        </div>
        
        <p class="text-xs text-gray-400">
            © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
        </p>
    </div>
</footer>