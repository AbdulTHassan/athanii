<?php
/**
 * ZAKKATPRO - Fatwa Library (Public)
 */

$pageTitle = 'Fatwa Library';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fatwa_init.php';

// ============ FILTERS ============
$search    = trim($_GET['search'] ?? '');
$category  = (int)($_GET['category'] ?? 0);
$language  = trim($_GET['language'] ?? '');

if (!in_array($language, ['en', 'ha', 'ar'], true)) {
    $language = '';
}

// ============ DATA ============
$categories = getFatwaCategories($pdo);
$fatwas = getPublishedFatwas($pdo, 50, 0, $search, $category, $language);
$totalPublished = countPublishedFatwas($pdo);

require_once __DIR__ . '/../includes/header.php';
?>

<main class="min-h-screen bg-[#f4f6f5]">

    <!-- PAGE INTRO -->
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-emerald-700">
                        <i class="fa-solid fa-scale-balanced"></i>
                        Islamic Guidance
                    </div>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        Islamic Fatwas Library
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                        Explore verified Zakat-related rulings and Islamic guidance
                        organized by topic and language.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-medium text-slate-500">Published</p>
                        <p class="mt-1 text-xl font-extrabold text-slate-900"><?= $totalPublished ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN -->
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-12">

            <!-- LEFT SIDEBAR -->
            <aside class="lg:col-span-3">
                <div class="space-y-5">

                    <!-- Categories -->
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h2 class="font-bold text-slate-900">Fatwa Categories</h2>
                        </div>
                        <div class="p-3">
                            <a href="index.php" 
                               class="flex items-center justify-between rounded-xl px-3 py-3 text-sm font-semibold
                               <?= $category === 0 ? 'bg-[#e6f0eb] text-[#006838]' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid fa-layer-group w-4"></i>
                                    All Fatwas
                                </span>
                                <span class="text-xs font-bold text-slate-400"><?= $totalPublished ?></span>
                            </a>

                            <?php foreach ($categories as $item): ?>
                                <a href="?category=<?= (int)$item['id'] ?>" 
                                   class="mt-1 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition
                                   <?= $category === (int)$item['id'] 
                                       ? 'bg-[#e6f0eb] font-semibold text-[#006838]' 
                                       : 'text-slate-600 hover:bg-slate-50 hover:text-[#006838]' ?>">
                                    <i class="fa-solid fa-chevron-right w-4 text-[10px]"></i>
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ask Scholar -->
                    <div class="overflow-hidden rounded-2xl bg-[#006838] text-white shadow-sm">
                        <div class="p-6">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <h2 class="mt-5 text-xl font-extrabold">Ask a Scholar</h2>
                            <p class="mt-3 text-sm leading-6 text-emerald-50">
                                Have a specific Zakat question? Submit it for review.
                            </p>
                            <a href="ask.php" 
                               class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-bold text-[#006838] transition hover:bg-emerald-50">
                                Ask Your Question
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- CENTER -->
            <div class="lg:col-span-9">

                <!-- Search -->
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <form method="GET" action="index.php">
                        <?php if ($category > 0): ?>
                            <input type="hidden" name="category" value="<?= $category ?>">
                        <?php endif; ?>
                        <?php if ($language !== ''): ?>
                            <input type="hidden" name="language" value="<?= htmlspecialchars($language) ?>">
                        <?php endif; ?>
                        
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="search" name="search" 
                                   value="<?= htmlspecialchars($search) ?>"
                                   placeholder="Search Fatwas, questions, scholars..."
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-28 text-sm outline-none transition focus:border-emerald-500 focus:bg-white">
                            <button type="submit" 
                                    class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-lg bg-[#006838] px-4 py-2 text-sm font-bold text-white hover:bg-[#00522c]">
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Results -->
                <div class="mt-6">
                    <h2 class="text-xl font-extrabold text-slate-900">
                        <?= count($fatwas) ?> Published Ruling<?= count($fatwas) === 1 ? '' : 's' ?>
                    </h2>
                </div>

                <!-- Fatwa Cards -->
                <div class="mt-4 space-y-4">
                    <?php if (!empty($fatwas)): ?>
                        <?php foreach ($fatwas as $fatwa): ?>
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-200 hover:shadow-md sm:p-6">
                                
                                <div class="flex flex-wrap items-center gap-2">
                                    <?php if (!empty($fatwa['category_name'])): ?>
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                            <?= htmlspecialchars($fatwa['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-500">
                                        <?= htmlspecialchars($fatwa['language']) ?>
                                    </span>
                                    <?php if (!empty($fatwa['created_at'])): ?>
                                        <span class="text-xs text-slate-400">
                                            <?= date('d M Y', strtotime($fatwa['created_at'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="mt-4 text-lg font-extrabold leading-7 text-slate-900">
                                    <?= htmlspecialchars($fatwa['question']) ?>
                                </h3>

                                <?php if (!empty($fatwa['scholar'])): ?>
                                    <div class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                            <i class="fa-solid fa-user-graduate text-xs"></i>
                                        </div>
                                        <span><?= htmlspecialchars($fatwa['scholar']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($fatwa['answer'])): ?>
                                    <div class="mt-4 border-l-2 border-emerald-200 pl-4">
                                        <p class="text-sm leading-7 text-slate-600">
                                            <?= htmlspecialchars(mb_substr(strip_tags($fatwa['answer']), 0, 260)) ?>...
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                                    <a href="view.php?id=<?= (int)$fatwa['id'] ?>" 
                                       class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900">
                                        Read Full Fatwa
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </div>

                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xl"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-slate-900">No published Fatwa found</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                                We could not find a published ruling matching your search.
                            </p>
                            <a href="index.php" 
                               class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#006838] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#00522c]">
                                View All Fatwas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>