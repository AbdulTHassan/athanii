<?php
/**
 * ============================================================
 * ZAKKATPRO — Public Home Page
 * ============================================================
 * Uses global header and footer.
 * ============================================================
 */

$pageTitle = 'ARE — Simplifying Zakat for the Modern World';
$pageDescription = 'ZAKKATPRO is a comprehensive digital platform designed to help individuals and organizations understand, calculate, manage, pay, and keep reliable records of their Zakat.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- =========================================================
     HERO SECTION
========================================================= -->
<section class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-emerald-50/50 py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            
            <!-- HERO CONTENT -->
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Zakat Management Platform
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    Simplifying Zakat
                    <span class="text-emerald-700">for the Modern World.</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                    ZAKKATPRO is a comprehensive digital platform designed to help individuals and organizations understand, calculate, manage, pay, and keep reliable records of their Zakat.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="<?= app_url('auth/login.php') ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                        Start Zakat Calculation
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                    <a href="<?= app_url('knowledge/index.php') ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                        Learn About Zakat
                        <i class="fa-solid fa-book-open text-xs"></i>
                    </a>
                </div>

                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500">
                    <span class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Multiple Zakat categories</span>
                    <span class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Organized records</span>
                    <span class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Built for individuals & organizations</span>
                </div>
            </div>

            <!-- HERO VISUAL -->
            <div class="relative mx-auto w-full max-w-lg lg:ml-auto">
                <div class="absolute -inset-4 rounded-full bg-emerald-200/30 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-2 shadow-2xl">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-slate-200">
                        <img src="<?= app_url('hero1.png') ?>" alt="ZAKKATPRO platform" class="absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-white/80">ZAKKATPRO</p>
                            <p class="mt-1 text-lg font-bold text-white">Understand • Calculate • Manage • Pay</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     VALUE STRIP
========================================================= -->
<section class="border-y border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 divide-x divide-slate-200 sm:grid-cols-4">
            <div class="px-4 py-6 text-center">
                <div class="text-emerald-700"><i class="fa-solid fa-calculator"></i></div>
                <p class="mt-2 text-sm font-bold text-slate-900">Calculate</p>
                <p class="mt-1 text-xs text-slate-500">Across multiple categories</p>
            </div>
            <div class="px-4 py-6 text-center">
                <div class="text-emerald-700"><i class="fa-solid fa-book-open"></i></div>
                <p class="mt-2 text-sm font-bold text-slate-900">Learn</p>
                <p class="mt-1 text-xs text-slate-500">Understand the principles</p>
            </div>
            <div class="px-4 py-6 text-center">
                <div class="text-emerald-700"><i class="fa-solid fa-file-lines"></i></div>
                <p class="mt-2 text-sm font-bold text-slate-900">Record</p>
                <p class="mt-1 text-xs text-slate-500">Keep organized reports</p>
            </div>
            <div class="px-4 py-6 text-center">
                <div class="text-emerald-700"><i class="fa-solid fa-circle-check"></i></div>
                <p class="mt-2 text-sm font-bold text-slate-900">Verify</p>
                <p class="mt-1 text-xs text-slate-500">Future payment records</p>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     ZAKAT CATEGORIES
========================================================= -->
<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-800">Zakat categories</span>
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">One platform for different types of Zakat.</h2>
            <p class="mt-4 text-base leading-7 text-slate-600">Select the category that matches the wealth or assets you want to assess.</p>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <!-- Cash -->
            <a href="<?= app_url('auth/login.php') ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fa-solid fa-money-bill-wave text-lg"></i></div>
                <h3 class="mt-5 font-bold text-slate-900">Cash & Savings</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Money, savings and other cash-based wealth.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700">Explore <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
            </a>

            <!-- Gold & Silver -->
            <a href="<?= app_url('auth/login.php') ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-coins text-lg"></i></div>
                <h3 class="mt-5 font-bold text-slate-900">Gold & Silver</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Precious metals and relevant holdings.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700">Explore <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
            </a>

            <!-- Business -->
            <a href="<?= app_url('auth/login.php') ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fa-solid fa-store text-lg"></i></div>
                <h3 class="mt-5 font-bold text-slate-900">Business</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Trade goods and business-related Zakat assets.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700">Explore <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
            </a>

            <!-- Agriculture -->
            <a href="<?= app_url('auth/login.php') ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-lime-50 text-lime-700"><i class="fa-solid fa-seedling text-lg"></i></div>
                <h3 class="mt-5 font-bold text-slate-900">Agriculture</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Agricultural produce and harvest assessment.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700">Explore <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
            </a>

            <!-- Livestock -->
            <a href="<?= app_url('auth/login.php') ?>" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600"><i class="fa-solid fa-cow text-lg"></i></div>
                <h3 class="mt-5 font-bold text-slate-900">Livestock</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">Zakat assessment for eligible livestock.</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700">Explore <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
            </a>
        </div>
    </div>
</section>

<!-- =========================================================
     HOW IT WORKS
========================================================= -->
<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-800">How ZAKKATPRO works</span>
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">From understanding to verified records.</h2>
        </div>

        <div class="mt-14 grid gap-10 md:grid-cols-5">
            <?php
            $steps = [
                ['number' => '01', 'icon' => 'fa-book-open', 'title' => 'Understand', 'text' => 'Learn about Zakat, Nisab, eligible assets and relevant guidance.'],
                ['number' => '02', 'icon' => 'fa-calculator', 'title' => 'Calculate', 'text' => 'Enter your information and calculate the amount due.'],
                ['number' => '03', 'icon' => 'fa-file-lines', 'title' => 'Review', 'text' => 'Review your result and generate a detailed Zakat report.'],
                ['number' => '04', 'icon' => 'fa-credit-card', 'title' => 'Pay', 'text' => 'Use an available online or approved offline payment workflow.'],
                ['number' => '05', 'icon' => 'fa-circle-check', 'title' => 'Verify', 'text' => 'Payment can be reviewed, approved and recorded for future reference.']
            ];
            ?>
            <?php foreach ($steps as $index => $step): ?>
                <div class="relative text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-lg"><i class="fa-solid <?= htmlspecialchars($step['icon']) ?>"></i></div>
                    <span class="mt-5 block text-xs font-extrabold tracking-widest text-emerald-700"><?= htmlspecialchars($step['number']) ?></span>
                    <h3 class="mt-2 font-bold text-slate-900"><?= htmlspecialchars($step['title']) ?></h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500"><?= htmlspecialchars($step['text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     KNOWLEDGE + FATWA
========================================================= -->
<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-800">Knowledge & Guidance</span>
            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Learn first. Ask when you need guidance.</h2>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fa-solid fa-book-open"></i></div>
                <h3 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Understand the principles behind Zakat.</h3>
                <p class="mt-4 leading-7 text-slate-600">Explore topics such as Zakat obligations, Nisab, Hawl, eligible assets, calculation principles, agricultural measurements, methodologies and sources.</p>
                <a href="<?= app_url('knowledge/index.php') ?>" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Explore Knowledge <i class="fa-solid fa-arrow-right text-xs"></i></a>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fa-solid fa-scale-balanced"></i></div>
                <h3 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900">Questions should follow a careful review process.</h3>
                <p class="mt-4 leading-7 text-slate-600">Browse published questions and answers or submit a question through the platform for the appropriate review and response workflow.</p>
                <a href="<?= app_url('fatwa/index.php') ?>" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Explore Fatwa <i class="fa-solid fa-arrow-right text-xs"></i></a>
            </article>
        </div>
    </div>
</section>

<!-- =========================================================
     FINAL CTA
========================================================= -->
<section class="bg-emerald-700 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="max-w-2xl text-white">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-200">Begin your Zakat journey</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Understand. Calculate. Manage. Pay. Verify.</h2>
                <p class="mt-4 leading-7 text-emerald-50">Start with the calculator, explore the Knowledge Centre, or learn more about the ZAKKATPRO platform.</p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                <a href="<?= app_url('auth/login.php') ?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50">Calculate Zakat <i class="fa-solid fa-arrow-right text-xs"></i></a>
                <a href="<?= app_url('auth/register.php') ?>" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">Create Account <i class="fa-solid fa-user-plus text-xs"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>