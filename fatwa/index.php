<?php

declare(strict_types=1);

$pageTitle = 'Fatwa Library | ZAKKATPRO';

/*
 Database

*/
require_once __DIR__ . '/../config/database.php';


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/
$search    = trim((string) ($_GET['search'] ?? ''));
$category  = trim((string) ($_GET['category'] ?? ''));
$language  = trim((string) ($_GET['language'] ?? ''));
$scholar   = trim((string) ($_GET['scholar'] ?? ''));

$allowedLanguages = ['en', 'ha', 'ar'];

if (!in_array($language, $allowedLanguages, true)) {
    $language = '';
}


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
| These categories correspond to the categories we designed for
| the ZAKKATPRO Fatwa Library.
|--------------------------------------------------------------------------
*/
$categories = [
    'Cash & Savings',
    'Gold & Silver',
    'Business & Trade',
    'Agriculture',
    'Property & Assets',
    'Zakat Recipients',
    'Debts & Liabilities',
    'Zakat Eligibility',
    'General Zakat',
];


/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
| The project already has e() in includes/functions.php.
| Therefore, we DO NOT redefine e() here.
|--------------------------------------------------------------------------
*/
function fatwaPreview(?string $text, int $length = 230): string
{
    $text = trim(strip_tags((string) $text));

    if ($text === '') {
        return '';
    }

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . '...';
}


/*
|--------------------------------------------------------------------------
| Base Query
|--------------------------------------------------------------------------
*/
$where  = ["status = 'published'"];
$params = [];


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
if ($search !== '') {

    $where[] = "(
        question LIKE :search_question
        OR answer LIKE :search_answer
        OR source LIKE :search_source
        OR scholar LIKE :search_scholar
    )";

    $searchValue = '%' . $search . '%';

    $params[':search_question'] = $searchValue;
    $params[':search_answer']   = $searchValue;
    $params[':search_source']   = $searchValue;
    $params[':search_scholar']  = $searchValue;
}


/*
|--------------------------------------------------------------------------
| Category Filter
|--------------------------------------------------------------------------
*/
if ($category !== '' && in_array($category, $categories, true)) {

    $where[] = "category = :category";
    $params[':category'] = $category;

} else {

    $category = '';
}


/*
|--------------------------------------------------------------------------
| Language Filter
|--------------------------------------------------------------------------
*/
if ($language !== '') {

    $where[] = "language = :language";
    $params[':language'] = $language;
}


/*
|--------------------------------------------------------------------------
| Scholar Filter
|--------------------------------------------------------------------------
*/
if ($scholar !== '') {

    $where[] = "scholar = :scholar";
    $params[':scholar'] = $scholar;
}


/*
|--------------------------------------------------------------------------
| Build WHERE
|--------------------------------------------------------------------------
*/
$whereSql = implode(' AND ', $where);


/*
|--------------------------------------------------------------------------
| Published Fatwas
|--------------------------------------------------------------------------
*/
$fatwas = [];

try {

    $sql = "
        SELECT
            id,
            language,
            category,
            question,
            answer,
            source,
            scholar,
            status,
            created_at,
            updated_at
        FROM fatwas
        WHERE {$whereSql}
        ORDER BY created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $fatwas = $stmt->fetchAll();

} catch (PDOException $e) {

    $fatwas = [];

}


/*
|--------------------------------------------------------------------------
| Total Published Fatwas
|--------------------------------------------------------------------------
*/
$totalPublished = 0;

try {

    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM fatwas
        WHERE status = 'published'
    ");

    $totalPublished = (int) $stmt->fetchColumn();

} catch (PDOException $e) {

    $totalPublished = 0;

}


/*
|--------------------------------------------------------------------------
| Total Scholars
|--------------------------------------------------------------------------
*/
$totalScholars = 0;

try {

    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT scholar)
        FROM fatwas
        WHERE status = 'published'
        AND scholar IS NOT NULL
        AND TRIM(scholar) <> ''
    ");

    $totalScholars = (int) $stmt->fetchColumn();

} catch (PDOException $e) {

    $totalScholars = 0;

}


/*
|--------------------------------------------------------------------------
| Scholars List
|--------------------------------------------------------------------------
*/
$scholars = [];

try {

    $stmt = $pdo->query("
        SELECT DISTINCT scholar
        FROM fatwas
        WHERE status = 'published'
        AND scholar IS NOT NULL
        AND TRIM(scholar) <> ''
        ORDER BY scholar ASC
    ");

    $scholars = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {

    $scholars = [];

}


/*
|--------------------------------------------------------------------------
| Language Counts
|--------------------------------------------------------------------------
*/
$languageCounts = [
    'en' => 0,
    'ha' => 0,
    'ar' => 0,
];

try {

    $stmt = $pdo->query("
        SELECT language, COUNT(*) AS total
        FROM fatwas
        WHERE status = 'published'
        GROUP BY language
    ");

    foreach ($stmt->fetchAll() as $row) {

        $lang = (string) ($row['language'] ?? '');

        if (isset($languageCounts[$lang])) {
            $languageCounts[$lang] = (int) $row['total'];
        }
    }

} catch (PDOException $e) {

    // Keep default values.
}


/*
|--------------------------------------------------------------------------
| Public Header
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../includes/header.php';

?>

<!-- =========================================================
     FATWA LIBRARY
========================================================= -->

<main class="min-h-screen bg-[#f4f6f5]">

    <!-- =====================================================
         PAGE INTRO
    ====================================================== -->

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
                        organized by topic, language and scholar.

                    </p>

                </div>


                <!-- Statistics -->

                <div class="flex flex-wrap gap-3">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                        <p class="text-xs font-medium text-slate-500">
                            Published Fatwas
                        </p>

                        <p class="mt-1 text-xl font-extrabold text-slate-900">
                            <?= $totalPublished ?>
                        </p>

                    </div>


                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                        <p class="text-xs font-medium text-slate-500">
                            Scholars
                        </p>

                        <p class="mt-1 text-xl font-extrabold text-slate-900">
                            <?= $totalScholars ?>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         MAIN APPLICATION LAYOUT
    ====================================================== -->

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-12">


            <!-- =================================================
                 LEFT SIDEBAR
            ================================================== -->

            <aside class="lg:col-span-3">

                <div class="space-y-5">


                    <!-- Categories -->

                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-5 py-4">

                            <h2 class="font-bold text-slate-900">
                                Fatwa Categories
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                Browse rulings by topic
                            </p>

                        </div>


                        <div class="p-3">

                            <!-- All -->

                            <a
                                href="index.php"
                                class="flex items-center justify-between rounded-xl px-3 py-3 text-sm font-semibold transition
                                <?= $category === '' && $language === '' && $scholar === ''
                                    ? 'bg-[#e6f0eb] text-[#006838]'
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-[#006838]' ?>"
                            >

                                <span class="flex items-center gap-3">

                                    <i class="fa-solid fa-layer-group w-4"></i>

                                    All Fatwas

                                </span>

                                <span class="text-xs font-bold text-slate-400">
                                    <?= $totalPublished ?>
                                </span>

                            </a>


                            <?php foreach ($categories as $item): ?>

                                <a
                                    href="?category=<?= urlencode($item) ?>"
                                    class="mt-1 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition
                                    <?= $category === $item
                                        ? 'bg-[#e6f0eb] font-semibold text-[#006838]'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-[#006838]' ?>"
                                >

                                    <i class="fa-solid fa-chevron-right w-4 text-[10px]"></i>

                                    <?= e($item) ?>

                                </a>

                            <?php endforeach; ?>

                        </div>

                    </div>


                    <!-- Languages -->

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="font-bold text-slate-900">
                            Language
                        </h2>

                        <div class="mt-4 space-y-2">


                            <a
                                href="?language=en"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-sm
                                <?= $language === 'en'
                                    ? 'bg-emerald-50 font-semibold text-emerald-700'
                                    : 'text-slate-600 hover:bg-slate-50' ?>"
                            >

                                <span>English</span>

                                <span class="text-xs text-slate-400">
                                    <?= $languageCounts['en'] ?>
                                </span>

                            </a>


                            <a
                                href="?language=ha"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-sm
                                <?= $language === 'ha'
                                    ? 'bg-emerald-50 font-semibold text-emerald-700'
                                    : 'text-slate-600 hover:bg-slate-50' ?>"
                            >

                                <span>Hausa</span>

                                <span class="text-xs text-slate-400">
                                    <?= $languageCounts['ha'] ?>
                                </span>

                            </a>


                            <a
                                href="?language=ar"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-sm
                                <?= $language === 'ar'
                                    ? 'bg-emerald-50 font-semibold text-emerald-700'
                                    : 'text-slate-600 hover:bg-slate-50' ?>"
                            >

                                <span>Arabic</span>

                                <span class="text-xs text-slate-400">
                                    <?= $languageCounts['ar'] ?>
                                </span>

                            </a>

                        </div>

                    </div>


                    <!-- Scholars -->

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <div class="flex items-center justify-between">

                            <h2 class="font-bold text-slate-900">
                                Scholars
                            </h2>

                            <i class="fa-solid fa-user-graduate text-sm text-emerald-600"></i>

                        </div>


                        <?php if (!empty($scholars)): ?>

                            <div class="mt-4 space-y-2">

                                <?php foreach ($scholars as $scholarName): ?>

                                    <a
                                        href="?scholar=<?= urlencode((string) $scholarName) ?>"
                                        class="block rounded-lg px-3 py-2 text-sm transition
                                        <?= $scholar === $scholarName
                                            ? 'bg-emerald-50 font-semibold text-emerald-700'
                                            : 'text-slate-600 hover:bg-slate-50' ?>"
                                    >

                                        <?= e((string) $scholarName) ?>

                                    </a>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <p class="mt-3 text-sm leading-6 text-slate-500">

                                Scholar information will appear here when
                                published Fatwas have assigned scholars.

                            </p>

                        <?php endif; ?>

                    </div>


                    <!-- Guidance -->

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                        <div class="flex items-start gap-3">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">

                                <i class="fa-solid fa-circle-info"></i>

                            </div>

                            <div>

                                <h3 class="font-bold text-amber-900">
                                    Important Guidance
                                </h3>

                                <p class="mt-2 text-xs leading-5 text-amber-800">

                                    General Fatwa information is provided for
                                    educational purposes. Personal or complex
                                    matters may require consultation with a
                                    qualified scholar.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </aside>


            <!-- =================================================
                 CENTER CONTENT
            ================================================== -->

            <div class="lg:col-span-6">


                <!-- Search -->

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <form method="GET" action="index.php">

                        <?php if ($category !== ''): ?>

                            <input
                                type="hidden"
                                name="category"
                                value="<?= e($category) ?>"
                            >

                        <?php endif; ?>


                        <?php if ($language !== ''): ?>

                            <input
                                type="hidden"
                                name="language"
                                value="<?= e($language) ?>"
                            >

                        <?php endif; ?>


                        <?php if ($scholar !== ''): ?>

                            <input
                                type="hidden"
                                name="scholar"
                                value="<?= e($scholar) ?>"
                            >

                        <?php endif; ?>


                        <div class="relative">

                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input
                                type="search"
                                name="search"
                                value="<?= e($search) ?>"
                                placeholder="Search Fatwas, questions, scholars or sources..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-28 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                            >

                            <button
                                type="submit"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-lg bg-[#006838] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#00522c]"
                            >
                                Search
                            </button>

                        </div>

                    </form>


                    <!-- Active filters -->

                    <?php if ($search !== '' || $category !== '' || $language !== '' || $scholar !== ''): ?>

                        <div class="mt-3 flex flex-wrap items-center gap-2">

                            <span class="text-xs font-semibold text-slate-500">
                                Active filters:
                            </span>


                            <?php if ($search !== ''): ?>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">

                                    Search:
                                    <?= e($search) ?>

                                </span>

                            <?php endif; ?>


                            <?php if ($category !== ''): ?>

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">

                                    <?= e($category) ?>

                                </span>

                            <?php endif; ?>


                            <?php if ($language !== ''): ?>

                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">

                                    <?= strtoupper(e($language)) ?>

                                </span>

                            <?php endif; ?>


                            <?php if ($scholar !== ''): ?>

                                <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-700">

                                    <?= e($scholar) ?>

                                </span>

                            <?php endif; ?>


                            <a
                                href="index.php"
                                class="ml-auto text-xs font-bold text-red-600 hover:text-red-700"
                            >
                                Clear filters
                            </a>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- Results heading -->

                <div class="mt-6 flex items-end justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">
                            Fatwa Library
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-slate-900">
                            <?= count($fatwas) ?> Published Ruling<?= count($fatwas) === 1 ? '' : 's' ?>
                        </h2>

                    </div>

                </div>


                <!-- Fatwa Cards -->

                <div class="mt-4 space-y-4">


                    <?php if (!empty($fatwas)): ?>


                        <?php foreach ($fatwas as $fatwa): ?>

                            <?php

                            $fatwaId = (int) ($fatwa['id'] ?? 0);

                            $fatwaCategory = trim((string) ($fatwa['category'] ?? ''));

                            $fatwaScholar = trim((string) ($fatwa['scholar'] ?? ''));

                            $fatwaSource = trim((string) ($fatwa['source'] ?? ''));

                            $fatwaLanguage = (string) ($fatwa['language'] ?? 'en');

                            $question = trim((string) ($fatwa['question'] ?? ''));

                            $answerPreview = fatwaPreview(
                                (string) ($fatwa['answer'] ?? ''),
                                260
                            );

                            $createdAt = $fatwa['created_at'] ?? null;

                            ?>


                            <article
                                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md sm:p-6"
                            >


                                <!-- Meta -->

                                <div class="flex flex-wrap items-center gap-2">

                                    <?php if ($fatwaCategory !== ''): ?>

                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">

                                            <?= e($fatwaCategory) ?>

                                        </span>

                                    <?php endif; ?>


                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-500">

                                        <?= e($fatwaLanguage) ?>

                                    </span>


                                    <?php if ($createdAt): ?>

                                        <span class="text-xs text-slate-400">

                                            <?= date('d M Y', strtotime((string) $createdAt)) ?>

                                        </span>

                                    <?php endif; ?>

                                </div>


                                <!-- Question -->

                                <h3 class="mt-4 text-lg font-extrabold leading-7 text-slate-900">

                                    <?= e($question) ?>

                                </h3>


                                <!-- Scholar -->

                                <?php if ($fatwaScholar !== ''): ?>

                                    <div class="mt-3 flex items-center gap-2 text-sm text-slate-600">

                                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">

                                            <i class="fa-solid fa-user-graduate text-xs"></i>

                                        </div>

                                        <span>

                                            <?= e($fatwaScholar) ?>

                                        </span>

                                    </div>

                                <?php endif; ?>


                                <!-- Answer Preview -->

                                <?php if ($answerPreview !== ''): ?>

                                    <div class="mt-4 border-l-2 border-emerald-200 pl-4">

                                        <p class="text-sm leading-7 text-slate-600">

                                            <?= e($answerPreview) ?>

                                        </p>

                                    </div>

                                <?php endif; ?>


                                <!-- Source -->

                                <?php if ($fatwaSource !== ''): ?>

                                    <p class="mt-4 text-xs text-slate-400">

                                        <span class="font-semibold text-slate-500">
                                            Source:
                                        </span>

                                        <?= e($fatwaSource) ?>

                                    </p>

                                <?php endif; ?>


                                <!-- Actions -->

                                <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">

                                   
                                    
                                    <a
                                    href="view.php?id=<?= (int) $fatwa['id'] ?>"
                                    class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900"
                                >
                                    Read Full Fatwa
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>



                                    


                                    <button
                                        type="button"
                                        onclick="shareFatwa(
                                            <?= $fatwaId ?>,
                                            <?= htmlspecialchars(
                                                json_encode($question, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        )"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                                    >

                                        <i class="fa-solid fa-share-nodes text-xs"></i>

                                        Share

                                    </button>

                                </div>

                            </article>

                        <?php endforeach; ?>


                    <?php else: ?>


                        <!-- Empty State -->

                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                <i class="fa-solid fa-magnifying-glass text-xl"></i>

                            </div>

                            <h3 class="mt-5 text-lg font-bold text-slate-900">

                                No published Fatwa found

                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">

                                We could not find a published ruling matching
                                your current search or filters.

                            </p>

                            <a
                                href="index.php"
                                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-[#006838] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#00522c]"
                            >

                                View All Fatwas

                            </a>

                        </div>


                    <?php endif; ?>

                </div>

            </div>


            <!-- =================================================
                 RIGHT SIDEBAR
            ================================================== -->

            <aside class="lg:col-span-3">

                <div class="space-y-5">


                    <!-- Ask Scholar -->

                    <div class="overflow-hidden rounded-2xl bg-[#006838] text-white shadow-sm">

                        <div class="p-6">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10">

                                <i class="fa-solid fa-user-graduate"></i>

                            </div>

                            <h2 class="mt-5 text-xl font-extrabold">

                                Ask a Scholar

                            </h2>

                            <p class="mt-3 text-sm leading-6 text-emerald-50">

                                Have a specific Zakat question that you
                                cannot find in the library?

                            </p>


                            <a
                                href="ask.php"
                                class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-bold text-[#006838] transition hover:bg-emerald-50"
                            >

                                Ask Your Question

                                <i class="fa-solid fa-arrow-right text-xs"></i>

                            </a>


                            <div class="mt-4 flex items-start gap-2 border-t border-white/10 pt-4">

                                <i class="fa-solid fa-lock mt-0.5 text-xs text-emerald-200"></i>

                                <p class="text-xs leading-5 text-emerald-100">

                                    Personal details submitted with a question
                                    are treated as private and are not displayed
                                    in the public Fatwa Library.

                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- Quick Links -->

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="font-bold text-slate-900">
                            Quick Knowledge Links
                        </h2>


                        <div class="mt-4 space-y-2">


                            <a
                                href="../knowledge/index.php"
                                class="flex items-center gap-3 rounded-xl p-3 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-emerald-700"
                            >

                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">

                                    <i class="fa-solid fa-book-open"></i>

                                </span>

                                <span class="font-semibold">
                                    Knowledge Centre
                                </span>

                            </a>


                            <a
                                href="index.php"
                                class="flex items-center gap-3 rounded-xl p-3 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-emerald-700"
                            >

                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700">

                                    <i class="fa-solid fa-scale-balanced"></i>

                                </span>

                                <span class="font-semibold">
                                    Fatwa Library
                                </span>

                            </a>


                            <a
                                href="../calculator/index.php"
                                class="flex items-center gap-3 rounded-xl p-3 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-emerald-700"
                            >

                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700">

                                    <i class="fa-solid fa-calculator"></i>

                                </span>

                                <span class="font-semibold">
                                    Zakat Calculator
                                </span>

                            </a>

                        </div>

                    </div>


                    <!-- Languages Summary -->

  

                  </div>
<?php require __DIR__ . '/../includes/motivation.php'; ?>

                </div>

            </aside>


        </div>

    </section>

</main>


<!-- =========================================================
     SHARE SCRIPT
========================================================= -->

<script>

function shareFatwa(id, title) {

    const shareUrl =
        window.location.origin +
        window.location.pathname.replace('index.php', 'view.php?id=' + id);


    if (navigator.share) {

        navigator.share({
            title: 'ZAKKATPRO Fatwa',
            text: title,
            url: shareUrl
        }).catch(function () {
            // User cancelled share dialog.
        });

        return;
    }


    if (navigator.clipboard) {

        navigator.clipboard.writeText(shareUrl)
            .then(function () {

                alert('Fatwa link copied to clipboard.');

            })
            .catch(function () {

                alert(shareUrl);

            });

    } else {

        alert(shareUrl);

    }

}

</script>