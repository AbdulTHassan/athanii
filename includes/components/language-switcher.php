<?php
// includes/components/language-switcher.php
$currentLang = currentLanguage();
$currentInfo = currentLanguageInfo();
?>
<div class="relative" id="lang-wrapper">
    <button onclick="toggleLangMenu()" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm">
        <span class="text-lg"><?php echo $currentInfo['flag']; ?></span>
        <span class="font-medium text-gray-700">
            <?php echo strtoupper($currentLang); ?>
        </span>
        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
    </button>
    
    <div id="lang-menu" 
         class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
        
        <div class="px-3 py-2 bg-gray-50 border-b">
            <p class="text-xs font-bold text-gray-500 uppercase">
                <i class="fas fa-globe"></i> <?php _e('language'); ?>
            </p>
        </div>
        
        <?php foreach (AVAILABLE_LANGUAGES as $code => $info): ?>
            <a href="<?php echo e(languageSwitchUrl($code)); ?>"
               class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 transition-colors
                      <?php echo $code === $currentLang ? 'bg-green-50 border-l-4 border-green-600' : ''; ?>">
                <span class="text-xl"><?php echo $info['flag']; ?></span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">
                        <?php echo $info['native']; ?>
                    </p>
                    <p class="text-xs text-gray-500"><?php echo $info['name']; ?></p>
                </div>
                <?php if ($code === $currentLang): ?>
                    <i class="fas fa-check text-green-600"></i>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
function toggleLangMenu() {
    const dropdown = document.getElementById('lang-menu');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const trigger = document.getElementById('lang-wrapper');
    const dropdown = document.getElementById('lang-menu');
    
    if (trigger && dropdown && !trigger.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>