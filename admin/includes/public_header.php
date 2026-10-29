<?php
// includes/public_header.php
$pageTitle = $pageTitle ?? 'ZAKKATPRO — Simplifying Zakat for the Modern World';
$pageDescription = $pageDescription ?? 'ZAKKATPRO is a comprehensive digital platform designed to help individuals and organizations understand, calculate, manage, pay, and keep reliable records of their Zakat.';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="theme-color" content="#047857">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- GLOBAL STYLES (Style ɗinka na asali daga index.php) -->
    <style>
        :root { --zakat-green: #047857; --zakat-green-dark: #065f46; --zakat-green-soft: #ecfdf5; --zakat-gold: #c89b3c; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; overflow-x: hidden; }
        img { max-width: 100%; }
        .container-main { width: min(100% - 32px, 1240px); margin-inline: auto; }
        @media (min-width: 640px) { .container-main { width: min(100% - 48px, 1240px); } }
        @media (min-width: 1024px) { .container-main { width: min(100% - 64px, 1240px); } }
        .hero-gradient { background: radial-gradient(circle at 82% 18%, rgba(16, 185, 129, .16), transparent 30%), radial-gradient(circle at 8% 90%, rgba(200, 155, 60, .08), transparent 25%), linear-gradient(135deg, #ecfdf5 0%, #ffffff 48%, #f0fdf4 100%); }
        .hero-slider-image { transition: opacity .7s ease, transform 1s ease; }
        .hero-slider-image.active { opacity: 1; transform: scale(1); }
        .hero-slider-image.inactive { opacity: 0; transform: scale(1.05); }
        .mobile-menu { transition: opacity .2s ease, visibility .2s ease, transform .2s ease; transform-origin: top; }
        .mobile-menu.hidden-menu { opacity: 0; visibility: hidden; pointer-events: none; transform: translateY(-8px); }
        .mobile-menu.visible-menu { opacity: 1; visibility: visible; pointer-events: auto; transform: translateY(0); }
        .soft-card { border: 1px solid #e5e7eb; background: rgba(255, 255, 255, .92); transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .soft-card:hover { transform: translateY(-4px); border-color: rgba(16, 185, 129, .35); box-shadow: 0 20px 45px rgba(15, 23, 42, .08); }
        .category-card { transition: transform .25s ease, box-shadow .25s ease; }
        .category-card:hover { transform: translateY(-5px); box-shadow: 0 18px 40px rgba(15, 23, 42, .09); }
        .btn-primary { background: var(--zakat-green); transition: background .2s ease, transform .2s ease, box-shadow .2s ease; }
        .btn-primary:hover { background: var(--zakat-green-dark); transform: translateY(-1px); box-shadow: 0 12px 25px rgba(4, 120, 87, .20); }
        .section-label { color: var(--zakat-green); background: var(--zakat-green-soft); }
        .step-line { position: absolute; top: 28px; left: calc(50% + 32px); right: calc(-50% + 32px); height: 1px; background: #d1fae5; }
        @media (max-width: 767px) { .step-line { display: none; } }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

<!-- NAVIGATION (Daga index.php) -->
<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl">
    <div class="container-main">
        <div class="flex h-[72px] items-center justify-between">
            <a href="index.php" class="flex items-center gap-3" aria-label="ZAKKATPRO Home">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm">
                    <i class="fa-solid fa-mosque"></i>
                </span>
                <div>
                    <div class="text-lg font-extrabold tracking-tight text-slate-900">ZAKKATPRO</div>
                    <div class="hidden text-[10px] font-semibold uppercase tracking-[.16em] text-slate-500 sm:block">Zakat Management Platform</div>
                </div>
            </a>

            <nav class="hidden items-center gap-7 lg:flex">
                <a href="index.php" class="text-sm font-bold text-emerald-700">Home</a>
                <!-- Muna canza 'calculator/index.php' zuwa 'auth/login.php' don tilasta login -->
                <a href="auth/login.php" class="text-sm font-medium text-slate-600 transition hover:text-emerald-700">Calculator</a>
                <a href="knowledge/index.php" class="text-sm font-medium text-slate-600 transition hover:text-emerald-700">Knowledge</a>
                <a href="fatwa/index.php" class="text-sm font-medium text-slate-600 transition hover:text-emerald-700">Fatwa</a>
                <a href="about.php" class="text-sm font-medium text-slate-600 transition hover:text-emerald-700">About</a>
            </nav>

            <div class="hidden items-center gap-2 lg:flex">
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-emerald-700" aria-label="Change language" title="Language">
                    <i class="fa-solid fa-globe"></i>
                </button>
                <a href="auth/login.php" class="ml-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Sign in</a>
            </div>

            <button type="button" id="mobileMenuButton" class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-50 lg:hidden" aria-label="Open menu" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu" class="mobile-menu hidden-menu absolute left-0 right-0 border-b border-slate-200 bg-white shadow-xl lg:hidden">
        <div class="container-main py-5">
            <nav class="flex flex-col gap-1">
                <a href="index.php" class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"><i class="fa-solid fa-house mr-2"></i> Home</a>
                <!-- Muna canza 'calculator/index.php' zuwa 'auth/login.php' -->
                <a href="auth/login.php" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><i class="fa-solid fa-calculator mr-2"></i> Calculator</a>
                <a href="knowledge/index.php" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><i class="fa-solid fa-book-open mr-2"></i> Knowledge</a>
                <a href="fatwa/index.php" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><i class="fa-solid fa-scale-balanced mr-2"></i> Fatwa</a>
                <a href="about.php" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><i class="fa-solid fa-circle-info mr-2"></i> About</a>
                <div class="my-3 h-px bg-slate-100"></div>
                <a href="auth/login.php" class="rounded-xl bg-emerald-700 px-4 py-3 text-center text-sm font-semibold text-white">Sign in</a>
            </nav>
        </div>
    </div>
</header>

<main>