<?php
/**
 * ZAKKATPRO - Article View (Public)
 */

$pageTitle = 'Article';

require_once __DIR__ . '/../config/public_init.php';

// ============ SAMUN SLUG ============
$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// ============ ɗEBO ARTICLE ============
$article = getArticleBySlug($pdo, $slug);

if (!$article) {
    http_response_code(404);
    include __DIR__ . '/../includes/header.php';
    ?>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md text-center">
            <div class="text-6xl mb-4">📄</div>
            <h1 class="text-2xl font-bold text-slate-900 mb-2">Article Not Found</h1>
            <p class="text-slate-500 mb-6">
                Article ɗin da kake nema ba ya nan ko kuma ba a wallafa shi ba.
            </p>
            <a href="index.php" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                ← Back to Knowledge Center
            </a>
        </div>
    </div>
    <?php
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// ============ ƘARA VIEW COUNT ============
incrementArticleViews($pdo, $article['id']);

include __DIR__ . '/../includes/header.php';
?>

<main class="bg-white dark:bg-slate-950">

    <!-- =====================================================
         ARTICLE HERO
    ====================================================== -->
    <section class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-br from-emerald-50 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="relative mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

            <!-- Breadcrumb -->
            <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 flex-wrap">
                <a href="index.php" class="hover:text-emerald-700 dark:hover:text-emerald-400">Knowledge Center</a>
                <span>›</span>
                <?php if (!empty($article['category_slug'])): ?>
                    <a href="category.php?slug=<?= urlencode($article['category_slug']) ?>" 
                       class="hover:text-emerald-700 dark:hover:text-emerald-400">
                        <?= htmlspecialchars($article['category_name']) ?>
                    </a>
                    <span>›</span>
                <?php endif; ?>
                <span class="text-slate-700 dark:text-slate-300 font-medium line-clamp-1">
                    <?= htmlspecialchars($article['title']) ?>
                </span>
            </nav>

            <!-- Category badge -->
            <?php if (!empty($article['category_name'])): ?>
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-sm font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                    <?= htmlspecialchars($article['category_name']) ?>
                </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl dark:text-white">
                <?= htmlspecialchars($article['title']) ?>
            </h1>

            <!-- Meta -->
            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1">
                    <i class="far fa-clock"></i>
                    <?= (int)($article['reading_time_minutes'] ?? 5) ?> min read
                </span>
                <span>•</span>
                <span>
                    <i class="far fa-calendar"></i>
                    <?= date('F d, Y', strtotime($article['created_at'])) ?>
                </span>
                <?php if (!empty($article['author_name'])): ?>
                    <span>•</span>
                    <span>
                        <i class="far fa-user"></i>
                        <?= htmlspecialchars($article['author_name']) ?>
                    </span>
                <?php endif; ?>
                <span>•</span>
                <span>
                    <i class="far fa-eye"></i>
                    <?= number_format((int)($article['view_count'] ?? 0) + 1) ?> views
                </span>
            </div>

            <!-- Excerpt -->
            <?php if (!empty($article['excerpt'])): ?>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                    <?= htmlspecialchars($article['excerpt']) ?>
                </p>
            <?php endif; ?>

        </div>
    </section>

    <!-- =====================================================
         FEATURED IMAGE
    ====================================================== -->
    <?php if (!empty($article['featured_image'])): ?>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 -mt-8 mb-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-lg dark:border-slate-800">
                <img src="<?= SITE_URL . '/' . htmlspecialchars($article['featured_image']) ?>" 
                     alt="<?= htmlspecialchars($article['title']) ?>"
                     class="h-auto w-full object-cover">
            </div>
        </div>
    <?php endif; ?>

    <!-- =====================================================
         ARTICLE CONTENT
    ====================================================== -->
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <?= $article['content'] ?>
        </div>
    </article>

    <!-- =====================================================
         CTA
    ====================================================== -->
    <section class="mx-auto max-w-4xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-8 text-center dark:border-emerald-900 dark:bg-emerald-950/30">
            <h2 class="text-xl font-bold text-slate-950 dark:text-white">
                Ready to calculate your Zakat?
            </h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                Yi amfani da calculator ɗinmu don sanin adadin Zakat ɗinka.
            </p>
            <a href="../calculator/index.php" 
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                Calculate Zakat →
            </a>
        </div>
    </section>

    <!-- =====================================================
         BACK LINK
    ====================================================== -->
    <div class="mx-auto max-w-4xl px-4 pb-12 sm:px-6 lg:px-8">
        <a href="index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800 dark:text-emerald-400">
            <i class="fas fa-arrow-left"></i>
            Back to Knowledge Center
        </a>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>