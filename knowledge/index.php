<?php
/**
 * ZAKKATPRO - Public Knowledge Center
 * Dynamic version - yana ɗebo articles daga database
 */

$pageTitle = 'Knowledge Center';

// Load public init (yana ɗauke da database connection)
require_once __DIR__ . '/../config/public_init.php';

// ============ ɗEBO DATA ============
$categories = getPublicCategories($pdo);
$featuredArticles = getFeaturedArticles($pdo, 3);
$latestArticles = getLatestArticles($pdo, 6);

// Idan babu featured, yi amfani da latest 3
if (empty($featuredArticles) && !empty($latestArticles)) {
    $featuredArticles = array_slice($latestArticles, 0, 3);
}

// ============ SEARCH ============
$searchQuery = trim($_GET['q'] ?? '');
$searchResults = [];

if (!empty($searchQuery)) {
    $searchResults = searchArticles($pdo, $searchQuery, 20);
}

include __DIR__ . '/../includes/header.php';
?>

<main class="bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">

    <!-- =====================================================
         HERO
    ====================================================== -->
    <section class="relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-emerald-100/60 blur-3xl dark:bg-emerald-900/20"></div>
            <div class="absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-amber-100/50 blur-3xl dark:bg-amber-900/10"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-3xl text-center">

                <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    ZAKKATPRO Knowledge Center
                </div>

                <h1 class="text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl dark:text-white">
                    Learn About
                    <span class="text-emerald-600 dark:text-emerald-400">Zakat</span>
                    with Clarity
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg dark:text-slate-300">
                    Explore clear and structured resources about Zakat,
                    including its principles, eligibility, Nisab, different
                    categories of wealth, and practical guidance.
                </p>

                <!-- SEARCH -->
                <form method="GET" action="" class="relative mx-auto mt-9 max-w-2xl">
                    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-2 shadow-lg shadow-slate-200/50 sm:flex-row dark:border-slate-700 dark:bg-slate-900">
                        <div class="flex flex-1 items-center">
                            <svg class="ml-3 h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                            <input type="search" name="q" 
                                   value="<?= htmlspecialchars($searchQuery) ?>"
                                   placeholder="Search Zakat knowledge..."
                                   class="w-full border-0 bg-transparent px-3 py-3 text-sm text-slate-900 outline-none placeholder:text-slate-400 dark:text-white">
                        </div>
                        <button type="submit" 
                                class="rounded-xl bg-emerald-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">
                            Search
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        Search by topic, article title, category or keyword.
                    </p>
                </form>

            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">

        <?php if (!empty($searchQuery)): ?>
            <!-- ================================
                 SEARCH RESULTS
            ================================= -->
            <section>
                <div class="mb-8">
                    <p class="mb-2 text-sm font-bold uppercase tracking-wider text-emerald-600">
                        Search Results
                    </p>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white">
                        "<?= htmlspecialchars($searchQuery) ?>"
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        <?= count($searchResults) ?> article(s) found
                    </p>
                </div>

                <?php if (empty($searchResults)): ?>
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                            <svg class="h-7 w-7 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">No articles found</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">
                            Ba a sami article ɗin da ya dace da bincikenka ba. Gwada wata kalma.
                        </p>
                        <a href="index.php" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                            <i class="fas fa-arrow-left"></i> Back to Knowledge Center
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($searchResults as $article): ?>
                            <article class="group rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                                <div class="mb-5 flex items-center justify-between">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                        <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <?= (int)($article['reading_time_minutes'] ?? 5) ?> min read
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-950 dark:text-white">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600 line-clamp-3 dark:text-slate-400">
                                    <?= htmlspecialchars($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 150)) ?>
                                </p>
                                <a href="article.php?slug=<?= urlencode($article['slug']) ?>" 
                                   class="mt-5 inline-flex items-center text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                    Read Article →
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php else: ?>

            <!-- ================================
                 FEATURED ARTICLES
            ================================= -->
            <section>
                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="mb-2 text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            Featured
                        </p>
                        <h2 class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white">
                            Featured Articles
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">
                            Start with some of the most useful topics in the ZAKKATPRO Knowledge Center.
                        </p>
                    </div>
                </div>

                <?php if (empty($featuredArticles)): ?>
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                        <div class="text-4xl mb-3">📚</div>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No featured articles yet</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Admin zai iya wallafa articles a cikin Admin → Knowledge Base.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 lg:grid-cols-3">
                        <?php 
                        $gradients = [
                            'from-emerald-900 to-emerald-700',
                            'from-amber-700 to-yellow-600',
                            'from-slate-800 to-slate-600'
                        ];
                        foreach ($featuredArticles as $index => $article): 
                            $gradient = $gradients[$index % count($gradients)];
                        ?>
                            <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900">
                                <div class="relative h-52 overflow-hidden bg-gradient-to-br <?= $gradient ?>">
                                    <?php if (!empty($article['featured_image'])): ?>
                                       

                                        <img src="<?= SITE_URL . '/' . htmlspecialchars($article['featured_image']) ?>" ...
                                             alt="<?= htmlspecialchars($article['title']) ?>"
                                             class="absolute inset-0 h-full w-full object-cover opacity-70">
                                    <?php endif; ?>
                                    <div class="relative flex h-full items-end p-6">
                                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                                            <?= htmlspecialchars($article['category_name'] ?? 'Article') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="mb-3 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                        <span><?= htmlspecialchars($article['category_name'] ?? 'General') ?></span>
                                        <span>•</span>
                                        <span><?= (int)($article['reading_time_minutes'] ?? 5) ?> min read</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-950 dark:text-white">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </h3>
                                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                        <?= htmlspecialchars($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 120)) ?>
                                    </p>
                                    <a href="article.php?slug=<?= urlencode($article['slug']) ?>" 
                                       class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                        Read Article →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>


            <!-- ================================
                 CATEGORIES
            ================================= -->
            <section class="mt-20">
                <div class="mb-8">
                    <p class="mb-2 text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        Explore
                    </p>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white">
                        Browse by Category
                    </h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">
                        Find resources based on the Zakat topic you want to understand.
                    </p>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                        <p class="text-sm text-slate-500 dark:text-slate-400">Babu categories a database tukuna.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <?php 
                        $categoryColors = [
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
                            'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
                            'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-400',
                            'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400',
                            'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                            'bg-lime-100 text-lime-700 dark:bg-lime-950 dark:text-lime-400',
                            'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400',
                            'bg-cyan-100 text-cyan-700 dark:bg-cyan-950 dark:text-cyan-400',
                        ];
                        foreach ($categories as $index => $cat): 
                            $colorClass = $categoryColors[$index % count($categoryColors)];
                        ?>
                            <a href="category.php?slug=<?= urlencode($cat['slug']) ?>" 
                               class="group rounded-2xl border border-slate-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700">
                                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl <?= $colorClass ?>">
                                    <span class="text-xl font-bold"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                <h3 class="font-bold text-slate-950 dark:text-white">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 line-clamp-2 dark:text-slate-400">
                                    <?= htmlspecialchars($cat['description'] ?? 'Explore this category') ?>
                                </p>
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                    Explore <span class="transition-transform group-hover:translate-x-1">→</span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>


            <!-- ================================
                 LATEST ARTICLES
            ================================= -->
            <section class="mt-20">
                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="mb-2 text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            Knowledge
                        </p>
                        <h2 class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white">
                            Latest Articles
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">
                            Continue learning with more topics from the Knowledge Center.
                        </p>
                    </div>
                </div>

                <?php if (empty($latestArticles)): ?>
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                        <p class="text-sm text-slate-500 dark:text-slate-400">Babu articles a database tukuna.</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($latestArticles as $article): ?>
                            <article class="group rounded-2xl border border-slate-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                                <div class="mb-5 flex items-center justify-between">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                        <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        <?= (int)($article['reading_time_minutes'] ?? 5) ?> min read
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-950 dark:text-white">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600 line-clamp-3 dark:text-slate-400">
                                    <?= htmlspecialchars($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 150)) ?>
                                </p>
                                <a href="article.php?slug=<?= urlencode($article['slug']) ?>" 
                                   class="mt-5 inline-flex items-center text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                    Read Article →
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>