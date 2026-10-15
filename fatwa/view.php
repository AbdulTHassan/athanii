<?php
/**
 * ZAKKATPRO - View Fatwa (Public)
 */

$pageTitle = 'View Fatwa';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fatwa_init.php';

// ============ GET FATWA ID ============
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(404);
    die('Fatwa not found.');
}

// ============ FETCH PUBLISHED FATWA ============
$fatwa = getFatwaById($pdo, $id);

if (!$fatwa) {
    http_response_code(404);
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <main class="min-h-[60vh] bg-slate-50 px-4 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fa-solid fa-file-circle-xmark text-2xl"></i>
            </div>
            <h1 class="mt-6 text-3xl font-bold text-slate-900">Fatwa Not Found</h1>
            <p class="mt-3 leading-7 text-slate-600">
                The requested fatwa could not be found or is no longer available.
            </p>
            <a href="index.php" 
               class="mt-8 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Fatwa Library
            </a>
        </div>
    </main>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// ============ HELPER VALUES ============
$languageLabels = [
    'en' => 'English',
    'ha' => 'Hausa',
    'ar' => 'Arabic',
];

$language = $languageLabels[$fatwa['language']] ?? strtoupper($fatwa['language']);
$category = $fatwa['category_name'] ?? 'General Zakat';
$scholar = $fatwa['scholar'] ?: 'Verified Islamic Source';
$source = $fatwa['source'] ?: 'Verified Islamic Reference';
$publishedDate = !empty($fatwa['published_at']) 
    ? date('d F Y', strtotime($fatwa['published_at'])) 
    : (!empty($fatwa['created_at']) ? date('d F Y', strtotime($fatwa['created_at'])) : '');

require_once __DIR__ . '/../includes/header.php';
?>

<main class="bg-slate-50">

    <!-- BREADCRUMB -->
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-5 sm:px-6 lg:px-8">
            <a href="index.php" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 transition hover:text-emerald-900">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Fatwa Library
            </a>
        </div>
    </section>

    <!-- FATWA CONTENT -->
    <section class="py-10 sm:py-14">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 px-6 py-8 text-white sm:px-10">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-emerald-100">
                            <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="rounded-full border border-white/20 px-3 py-1.5 text-xs font-semibold text-white/90">
                            <?= htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <h1 class="mt-6 text-2xl font-extrabold leading-tight sm:text-4xl">
                        Fatwa & Islamic Guidance
                    </h1>

                    <p class="mt-3 text-sm leading-7 text-emerald-100">
                        Verified Islamic guidance presented with its source and attribution.
                    </p>
                </div>

                <div class="px-6 py-8 sm:px-10 sm:py-10">

                    <section>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                                <i class="fa-solid fa-circle-question"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900">Question</h2>
                        </div>

                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                            <p class="text-base font-semibold leading-8 text-slate-800 sm:text-lg">
                                <?= nl2br(htmlspecialchars($fatwa['question'], ENT_QUOTES, 'UTF-8')) ?>
                            </p>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900">Answer</h2>
                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8">
                            <div class="prose prose-slate max-w-none leading-8">
                                <?= nl2br(htmlspecialchars($fatwa['answer'], ENT_QUOTES, 'UTF-8')) ?>
                            </div>
                        </div>
                    </section>

                    <section class="mt-10">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                        <i class="fa-solid fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Scholar</p>
                                        <p class="mt-1 font-bold text-slate-900">
                                            <?= htmlspecialchars($scholar, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                        <i class="fa-solid fa-book"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Source</p>
                                        <p class="mt-1 font-bold text-slate-900">
                                            <?= htmlspecialchars($source, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6">
                        <div class="text-sm text-slate-500">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            Published <?= htmlspecialchars($publishedDate, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <button type="button" onclick="shareFatwa()"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                            <i class="fa-solid fa-share-nodes"></i>
                            Share Fatwa
                        </button>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="index.php"
                           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Library
                        </a>
                        <a href="ask.php"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                            <i class="fa-solid fa-comments"></i>
                            Ask a Scholar
                        </a>
                    </div>

                </div>

            </article>

            <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="flex gap-4">
                    <i class="fa-solid fa-circle-info mt-1 text-amber-600"></i>
                    <div>
                        <h3 class="font-bold text-amber-900">Important Guidance</h3>
                        <p class="mt-2 text-sm leading-7 text-amber-800">
                            Fatwa rulings can depend on a person's circumstances,
                            madhhab, location and other relevant details.
                            For personal or complex matters, consult a qualified
                            Islamic scholar.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</main>

<script>
function shareFatwa() {
    const title = document.title;
    const url = window.location.href;

    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Fatwa from ZAKKATPRO',
            url: url
        });
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function () {
            alert('Fatwa link copied successfully.');
        });
    } else {
        alert(url);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>