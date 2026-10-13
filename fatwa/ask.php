<?php

declare(strict_types=1);

$pageTitle = 'Ask a Scholar | ZAKKATPRO';

require_once __DIR__ . '/../config/database.php';


// ============================================================
// DEFAULT VALUES
// ============================================================

$fullName = '';
$email = '';
$phone = '';
$country = '';
$language = 'en';
$category = '';
$question = '';
$privacyMode = 'private';

$errors = [];
$success = false;
$submittedId = null;


// ============================================================
// CATEGORIES
// ============================================================

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


// ============================================================
// HANDLE FORM
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? ''));
    $language = trim((string) ($_POST['language'] ?? 'en'));
    $category = trim((string) ($_POST['category'] ?? ''));
    $question = trim((string) ($_POST['question'] ?? ''));
    $privacyMode = trim((string) ($_POST['privacy_mode'] ?? 'private'));


    // ========================================================
    // VALIDATION
    // ========================================================

    if ($fullName === '') {
        $errors[] = 'Please enter your full name.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($question === '') {
        $errors[] = 'Please enter your question.';
    }

    if (!in_array($language, ['en', 'ha', 'ar'], true)) {
        $errors[] = 'Invalid language selected.';
    }

    if ($category !== '' && !in_array($category, $categories, true)) {
        $errors[] = 'Invalid category selected.';
    }

    if (!in_array($privacyMode, ['public', 'anonymous', 'private'], true)) {
        $errors[] = 'Invalid privacy option selected.';
    }


    // ========================================================
    // SAVE QUESTION
    // ========================================================

    if (!$errors) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO questions (
                    full_name,
                    email,
                    phone,
                    country,
                    language,
                    category,
                    question,
                    privacy_mode,
                    status
                )
                VALUES (
                    :full_name,
                    :email,
                    :phone,
                    :country,
                    :language,
                    :category,
                    :question,
                    :privacy_mode,
                    'pending'
                )
            ");

            $stmt->execute([
                ':full_name' => $fullName,
                ':email' => $email,
                ':phone' => $phone !== '' ? $phone : null,
                ':country' => $country !== '' ? $country : null,
                ':language' => $language,
                ':category' => $category !== '' ? $category : null,
                ':question' => $question,
                ':privacy_mode' => $privacyMode,
            ]);

            $submittedId = (int) $pdo->lastInsertId();

            $success = true;


            // Clear form

            $fullName = '';
            $email = '';
            $phone = '';
            $country = '';
            $language = 'en';
            $category = '';
            $question = '';
            $privacyMode = 'private';

        } catch (PDOException $e) {

            $errors[] = 'Your question could not be submitted at this time. Please try again.';

        }

    }

}


require_once __DIR__ . '/../includes/header.php';

?>

<main class="bg-slate-50">

    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="bg-emerald-950 text-white">

        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">

            <div class="mx-auto max-w-3xl text-center">
                <a
                    href="ask.php"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-user-graduate"></i>
                    Ask a Scholar
                </a>


                <!-- <h1 class="mt-6 text-3xl font-extrabold tracking-tight sm:text-5xl">
                    Ask a Scholar
                </h1> -->


                <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-emerald-100 sm:text-lg">

                    Submit your Zakat question for review through the
                    platform's scholarly consultation workflow.

                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         FORM
    ====================================================== -->

    <section class="py-12 sm:py-16">

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">


            <!-- SUCCESS -->

            <?php if ($success): ?>

                <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">

                    <div class="flex gap-4">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">

                            <i class="fa-solid fa-check"></i>

                        </div>


                        <div>

                            <h2 class="font-bold text-emerald-900">
                                Question Submitted Successfully
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-emerald-800">

                                Your question has been received and is now
                                waiting for review.

                                <?php if ($submittedId): ?>

                                    Your reference number is:

                                    <strong>
                                        #<?= $submittedId ?>
                                    </strong>

                                <?php endif; ?>

                            </p>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <!-- ERRORS -->

            <?php if ($errors): ?>

                <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5">

                    <div class="flex gap-3">

                        <i class="fa-solid fa-circle-exclamation mt-1 text-red-600"></i>

                        <div>

                            <h2 class="font-bold text-red-900">
                                Please correct the following:
                            </h2>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-800">

                                <?php foreach ($errors as $error): ?>

                                    <li>
                                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <div class="grid gap-8 lg:grid-cols-[1fr_320px]">


                <!-- =================================================
                     FORM CARD
                ================================================== -->

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                    <div class="mb-8">

                        <p class="text-sm font-bold uppercase tracking-wider text-emerald-700">
                            Submit Question
                        </p>

                        <h2 class="mt-2 text-2xl font-extrabold text-slate-900">
                            Tell us what you need guidance about
                        </h2>

                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            Provide enough information to help the reviewing
                            scholar understand your question and its context.
                        </p>

                    </div>


                    <form method="POST" action="" class="space-y-6">


                        <!-- NAME -->

                        <div>

                            <label
                                for="full_name"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Full Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>"
                                required
                                maxlength="150"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                placeholder="Enter your full name"
                            >

                        </div>


                        <!-- EMAIL / PHONE -->

                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>

                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Email Address
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    maxlength="150"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                    placeholder="you@example.com"
                                >

                            </div>


                            <div>

                                <label
                                    for="phone"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Phone Number
                                </label>

                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="50"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                    placeholder="+234..."
                                >

                            </div>

                        </div>


                        <!-- COUNTRY / LANGUAGE -->

                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>

                                <label
                                    for="country"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Country
                                </label>

                                <input
                                    type="text"
                                    id="country"
                                    name="country"
                                    value="<?= htmlspecialchars($country, ENT_QUOTES, 'UTF-8') ?>"
                                    maxlength="100"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                    placeholder="Nigeria"
                                >

                            </div>


                            <div>

                                <label
                                    for="language"
                                    class="mb-2 block text-sm font-bold text-slate-700"
                                >
                                    Preferred Language
                                </label>

                                <select
                                    id="language"
                                    name="language"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                >

                                    <option value="en" <?= $language === 'en' ? 'selected' : '' ?>>
                                        English
                                    </option>

                                    <option value="ha" <?= $language === 'ha' ? 'selected' : '' ?>>
                                        Hausa
                                    </option>

                                    <option value="ar" <?= $language === 'ar' ? 'selected' : '' ?>>
                                        Arabic
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- CATEGORY -->

                        <div>

                            <label
                                for="category"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Question Category
                            </label>

                            <select
                                id="category"
                                name="category"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                            >

                                <option value="">
                                    Select a category
                                </option>

                                <?php foreach ($categories as $item): ?>

                                    <option
                                        value="<?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $category === $item ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- QUESTION -->

                        <div>

                            <label
                                for="question"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Your Question
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                id="question"
                                name="question"
                                rows="8"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-7 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"
                                placeholder="Write your Zakat question in detail..."
                            ><?= htmlspecialchars($question, ENT_QUOTES, 'UTF-8') ?></textarea>

                        </div>


                        <!-- PRIVACY -->

                        <div>

                            <label class="mb-3 block text-sm font-bold text-slate-700">
                                Privacy Preference
                            </label>


                            <div class="grid gap-3 sm:grid-cols-3">


                                <label class="cursor-pointer">

                                    <input
                                        type="radio"
                                        name="privacy_mode"
                                        value="private"
                                        class="peer sr-only"
                                        <?= $privacyMode === 'private' ? 'checked' : '' ?>
                                    >

                                    <div class="rounded-xl border border-slate-300 p-4 transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50">

                                        <p class="font-bold text-slate-900">
                                            Private
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Keep your question private.
                                        </p>

                                    </div>

                                </label>


                                <label class="cursor-pointer">

                                    <input
                                        type="radio"
                                        name="privacy_mode"
                                        value="anonymous"
                                        class="peer sr-only"
                                        <?= $privacyMode === 'anonymous' ? 'checked' : '' ?>
                                    >

                                    <div class="rounded-xl border border-slate-300 p-4 transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50">

                                        <p class="font-bold text-slate-900">
                                            Anonymous
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Hide your identity if published.
                                        </p>

                                    </div>

                                </label>


                                <label class="cursor-pointer">

                                    <input
                                        type="radio"
                                        name="privacy_mode"
                                        value="public"
                                        class="peer sr-only"
                                        <?= $privacyMode === 'public' ? 'checked' : '' ?>
                                    >

                                    <div class="rounded-xl border border-slate-300 p-4 transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50">

                                        <p class="font-bold text-slate-900">
                                            Public
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            May be published after review.
                                        </p>

                                    </div>

                                </label>


                            </div>

                        </div>


                        <!-- SUBMIT -->

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200"
                        >

                            <i class="fa-solid fa-paper-plane"></i>

                            Submit Question

                        </button>


                    </form>

                </div>


                <!-- =================================================
                     SIDEBAR
                ================================================== -->

                <aside class="space-y-5">


                    <!-- PROCESS -->

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <h2 class="font-bold text-slate-900">
                            What happens next?
                        </h2>


                        <div class="mt-5 space-y-5">


                            <div class="flex gap-3">

                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                                    1
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Submit
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Your question is securely recorded.
                                    </p>
                                </div>

                            </div>


                            <div class="flex gap-3">

                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                                    2
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Review
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        An administrator reviews the submission.
                                    </p>
                                </div>

                            </div>


                            <div class="flex gap-3">

                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                                    3
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Scholarly Answer
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        The question can be assigned for scholarly review.
                                    </p>
                                </div>

                            </div>


                            <div class="flex gap-3">

                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700">
                                    4
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        Publication
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Only verified answers can be published.
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- NOTICE -->

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                        <div class="flex gap-3">

                            <i class="fa-solid fa-shield-halved mt-1 text-amber-600"></i>

                            <div>

                                <h3 class="font-bold text-amber-900">
                                    Important
                                </h3>

                                <p class="mt-2 text-xs leading-6 text-amber-800">
                                    Complex personal matters should be discussed
                                    with a qualified scholar. ZAKKATPRO does not
                                    automatically generate fatwas.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- BACK -->

                    <a
                        href="index.php"
                        class="flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Fatwa Library
                    </a>

                </aside>

            </div>

        </div>

    </section>

</main>


<?php
require_once __DIR__ . '/../includes/footer.php';
?>