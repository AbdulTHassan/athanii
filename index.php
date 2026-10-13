<?php
/**
 * ============================================================
 * ZAKKATPRO — Public Home Page
 * ============================================================
 *
 * Public landing page.
 * Database-free for now.
 * Global JavaScript:
 * assets/js/main.js
 * ============================================================
 */

$pageTitle = 'ZAKKATPRO — Simplifying Zakat for the Modern World';
$pageDescription = 'ZAKKATPRO is a comprehensive digital platform designed to help individuals and organizations understand, calculate, manage, pay, and keep reliable records of their Zakat.';
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    >

    <meta
        name="description"
        content="<?= htmlspecialchars($pageDescription) ?>"
    >

    <meta
        name="theme-color"
        content="#047857"
    >

    <title><?= htmlspecialchars($pageTitle) ?></title>


    <!-- =====================================================
         TAILWIND CSS
    ====================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >


    <!-- =====================================================
         FONTS
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         GLOBAL / PAGE UI STYLES
    ====================================================== -->

    <style>

        :root {
            --zakat-green: #047857;
            --zakat-green-dark: #065f46;
            --zakat-green-soft: #ecfdf5;
            --zakat-gold: #c89b3c;
        }


        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
        }


        img {
            max-width: 100%;
        }


        .container-main {
            width: min(100% - 32px, 1240px);
            margin-inline: auto;
        }


        @media (min-width: 640px) {

            .container-main {
                width: min(100% - 48px, 1240px);
            }

        }


        @media (min-width: 1024px) {

            .container-main {
                width: min(100% - 64px, 1240px);
            }

        }


        /* ==================================================
           HERO
        ================================================== */

        .hero-gradient {

            background:
                radial-gradient(
                    circle at 82% 18%,
                    rgba(16, 185, 129, .16),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 8% 90%,
                    rgba(200, 155, 60, .08),
                    transparent 25%
                ),
                linear-gradient(
                    135deg,
                    #ecfdf5 0%,
                    #ffffff 48%,
                    #f0fdf4 100%
                );

        }


        .hero-slider-image {

            transition:
                opacity .7s ease,
                transform 1s ease;

        }


        .hero-slider-image.active {

            opacity: 1;
            transform: scale(1);

        }


        .hero-slider-image.inactive {

            opacity: 0;
            transform: scale(1.05);

        }


        /* ==================================================
           MOBILE MENU
        ================================================== */

        .mobile-menu {

            transition:
                opacity .2s ease,
                visibility .2s ease,
                transform .2s ease;

            transform-origin: top;

        }


        .mobile-menu.hidden-menu {

            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-8px);

        }


        .mobile-menu.visible-menu {

            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);

        }


        /* ==================================================
           CARDS
        ================================================== */

        .soft-card {

            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, .92);

            transition:
                transform .25s ease,
                box-shadow .25s ease,
                border-color .25s ease;

        }


        .soft-card:hover {

            transform: translateY(-4px);

            border-color: rgba(16, 185, 129, .35);

            box-shadow:
                0 20px 45px rgba(15, 23, 42, .08);

        }


        .category-card {

            transition:
                transform .25s ease,
                box-shadow .25s ease;

        }


        .category-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 18px 40px rgba(15, 23, 42, .09);

        }


        /* ==================================================
           BUTTONS
        ================================================== */

        .btn-primary {

            background: var(--zakat-green);

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;

        }


        .btn-primary:hover {

            background: var(--zakat-green-dark);

            transform: translateY(-1px);

            box-shadow:
                0 12px 25px rgba(4, 120, 87, .20);

        }


        /* ==================================================
           SECTION LABEL
        ================================================== */

        .section-label {

            color: var(--zakat-green);

            background: var(--zakat-green-soft);

        }


        /* ==================================================
           PROCESS LINE
        ================================================== */

        .step-line {

            position: absolute;

            top: 28px;

            left: calc(50% + 32px);

            right: calc(-50% + 32px);

            height: 1px;

            background: #d1fae5;

        }


        @media (max-width: 767px) {

            .step-line {
                display: none;
            }

        }


        /* ==================================================
           REDUCED MOTION
        ================================================== */

        @media (prefers-reduced-motion: reduce) {

            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {

                animation-duration: .01ms !important;

                animation-iteration-count: 1 !important;

                transition-duration: .01ms !important;

            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVIGATION
========================================================= -->

<header
    class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl"
>

    <div class="container-main">

        <div class="flex h-[72px] items-center justify-between">


            <!-- =================================================
                 BRAND
            ================================================== -->

            <a
                href="index.php"
                class="flex items-center gap-3"
                aria-label="ZAKKATPRO Home"
            >

                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm"
                >

                    <i class="fa-solid fa-mosque"></i>

                </span>


                <div>

                    <div
                        class="text-lg font-extrabold tracking-tight text-slate-900"
                    >
                        ZAKKATPRO
                    </div>

                    <div
                        class="hidden text-[10px] font-semibold uppercase tracking-[.16em] text-slate-500 sm:block"
                    >
                        Zakat Management Platform
                    </div>

                </div>

            </a>


            <!-- =================================================
                 DESKTOP NAVIGATION
            ================================================== -->

            <nav class="hidden items-center gap-7 lg:flex">

                <a
                    href="index.php"
                    class="text-sm font-bold text-emerald-700"
                >
                    Home
                </a>

                <a
                    href="calculator/index.php"
                    class="text-sm font-medium text-slate-600 transition hover:text-emerald-700"
                >
                    Calculator
                </a>

                <a
                    href="knowledge/index.php"
                    class="text-sm font-medium text-slate-600 transition hover:text-emerald-700"
                >
                    Knowledge
                </a>

                <a
                    href="fatwa/index.php"
                    class="text-sm font-medium text-slate-600 transition hover:text-emerald-700"
                >
                    Fatwa
                </a>

                <a
                    href="about.php"
                    class="text-sm font-medium text-slate-600 transition hover:text-emerald-700"
                >
                    About
                </a>

            </nav>


            <!-- =================================================
                 DESKTOP ACTIONS
            ================================================== -->

            <div class="hidden items-center gap-2 lg:flex">

                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-emerald-700"
                    aria-label="Change language"
                    title="Language"
                >
                    <i class="fa-solid fa-globe"></i>
                </button>
<!-- 

                <button
                    type="button"
                    id="themeToggle"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-emerald-700"
                    aria-label="Toggle dark mode"
                    title="Theme"
                >
                    <i class="fa-solid fa-moon"></i>
                </button> -->


                <a
                    href="auth/login.php"
                    class="ml-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800"
                >
                    Sign in
                </a>

            </div>


            <!-- =================================================
                 MOBILE MENU BUTTON
            ================================================== -->

            <button
                type="button"
                id="mobileMenuButton"
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-50 lg:hidden"
                aria-label="Open menu"
                aria-expanded="false"
            >

                <i class="fa-solid fa-bars"></i>

            </button>

        </div>

    </div>


    <!-- =====================================================
         MOBILE MENU
    ====================================================== -->

    <div
        id="mobileMenu"
        class="mobile-menu hidden-menu absolute left-0 right-0 border-b border-slate-200 bg-white shadow-xl lg:hidden"
    >

        <div class="container-main py-5">

            <nav class="flex flex-col gap-1">


                <a
                    href="index.php"
                    class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                >

                    <i class="fa-solid fa-house mr-2"></i>

                    Home

                </a>


                <a
                    href="calculator/index.php"
                    class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >

                    <i class="fa-solid fa-calculator mr-2"></i>

                    Calculator

                </a>


                <a
                    href="knowledge/index.php"
                    class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >

                    <i class="fa-solid fa-book-open mr-2"></i>

                    Knowledge

                </a>


                <a
                    href="fatwa/index.php"
                    class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >

                    <i class="fa-solid fa-scale-balanced mr-2"></i>

                    Fatwa

                </a>


                <a
                    href="about.php"
                    class="rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >

                    <i class="fa-solid fa-circle-info mr-2"></i>

                    About

                </a>


                <div class="my-3 h-px bg-slate-100"></div>


                <a
                    href="auth/login.php"
                    class="rounded-xl bg-emerald-700 px-4 py-3 text-center text-sm font-semibold text-white"
                >

                    Sign in

                </a>

            </nav>

        </div>

    </div>

</header>



<!-- =========================================================
     MAIN
========================================================= -->

<main>


<!-- =========================================================
     HERO
========================================================= -->

<section class="hero-gradient overflow-hidden">

    <div class="container-main">

        <div
            class="grid min-h-[650px] items-center gap-12 py-14 lg:grid-cols-2 lg:py-20"
        >


            <!-- =================================================
                 HERO CONTENT
            ================================================== -->

            <div class="max-w-2xl">


                <span
                    class="section-label inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold uppercase tracking-[.12em]"
                >

                    <span
                        class="h-2 w-2 rounded-full bg-emerald-500"
                    ></span>

                    Zakat Management Platform

                </span>


                <h1
                    class="mt-6 text-4xl font-extrabold leading-[1.08] tracking-tight text-slate-950 sm:text-5xl lg:text-6xl"
                >

                    Simplifying Zakat

                    <span class="text-emerald-700">
                        for the Modern World.
                    </span>

                </h1>


                <p
                    class="mt-6 max-w-xl text-base leading-8 text-slate-600 sm:text-lg"
                >

                    ZAKKATPRO is a comprehensive digital platform designed
                    to help individuals and organizations understand,
                    calculate, manage, pay, and keep reliable records of
                    their Zakat.

                </p>


                <!-- =================================================
                     HERO ACTIONS
                ================================================== -->

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">


                    <a
                        href="calculator/index.php"
                        class="btn-primary inline-flex min-h-12 items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white"
                    >

                        Start Zakat Calculation

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>


                    <a
                        href="knowledge/index.php"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                    >

                        Learn About Zakat

                        <i class="fa-solid fa-book-open text-xs"></i>

                    </a>

                </div>


                <!-- =================================================
                     HERO TRUST POINTS
                ================================================== -->

                <div
                    class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500"
                >

                    <span class="flex items-center gap-2">

                        <i class="fa-solid fa-circle-check text-emerald-600"></i>

                        Multiple Zakat categories

                    </span>


                    <span class="flex items-center gap-2">

                        <i class="fa-solid fa-circle-check text-emerald-600"></i>

                        Organized records

                    </span>


                    <span class="flex items-center gap-2">

                        <i class="fa-solid fa-circle-check text-emerald-600"></i>

                        Built for individuals & organizations

                    </span>

                </div>

            </div>



            <!-- =================================================
                 HERO VISUAL
            ================================================== -->

            <div class="relative mx-auto w-full max-w-xl lg:ml-auto">


                <div
                    class="absolute -left-10 top-10 h-32 w-32 rounded-full bg-emerald-200/40 blur-3xl"
                ></div>


                <div
                    class="absolute -right-10 bottom-10 h-40 w-40 rounded-full bg-amber-200/30 blur-3xl"
                ></div>


                <div
                    class="relative overflow-hidden rounded-[28px] border border-white/80 bg-white/80 p-3 shadow-2xl shadow-emerald-900/10"
                >

                    <div
                        id="heroVisual"
                        class="relative aspect-[4/3] overflow-hidden rounded-[22px] bg-slate-200"
                    >


                        <!-- Slide 1 -->

                        <img
                            src="hero1.png"
                            alt="ZAKKATPRO platform"
                            class="hero-slider-image active absolute inset-0 h-full w-full object-cover"
                        >


                        <!-- Slide 2 -->

                        <img
                            src="hero2.png"
                            alt="Zakat calculation"
                            class="hero-slider-image inactive absolute inset-0 h-full w-full object-cover"
                        >


                        <!-- Slide 3 -->

                        <img
                            src="hero3.png"
                            alt="Islamic Zakat knowledge"
                            class="hero-slider-image inactive absolute inset-0 h-full w-full object-cover"
                        >


                        <!-- Overlay -->

                        <div
                            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-5 pt-24"
                        >

                            <div
                                class="flex items-end justify-between gap-4"
                            >

                                <div class="text-white">

                                    <p
                                        class="text-xs font-semibold uppercase tracking-[.12em] text-white/70"
                                    >
                                        ZAKKATPRO
                                    </p>

                                    <p class="mt-1 text-lg font-bold">
                                        Understand • Calculate • Manage • Pay
                                    </p>

                                </div>


                                <!-- Slider dots -->

                                <div
                                    id="heroDots"
                                    class="flex shrink-0 gap-1.5"
                                >

                                    <button
                                        type="button"
                                        class="hero-dot h-2 w-7 rounded-full bg-white"
                                        data-index="0"
                                        aria-label="Show slide 1"
                                    ></button>


                                    <button
                                        type="button"
                                        class="hero-dot h-2 w-2 rounded-full bg-white/50"
                                        data-index="1"
                                        aria-label="Show slide 2"
                                    ></button>


                                    <button
                                        type="button"
                                        class="hero-dot h-2 w-2 rounded-full bg-white/50"
                                        data-index="2"
                                        aria-label="Show slide 3"
                                    ></button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Floating information card -->

                <div
                    class="absolute -bottom-5 -left-3 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-xl sm:block lg:-left-8"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"
                        >

                            <i class="fa-solid fa-shield-halved"></i>

                        </div>


                        <div>

                            <p class="text-xs font-medium text-slate-500">
                                Designed for
                            </p>

                            <p class="text-sm font-bold text-slate-900">
                                Trust & Organization
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     PLATFORM VALUE STRIP
========================================================= -->

<section class="border-y border-slate-200 bg-white">

    <div class="container-main">

        <div
            class="grid grid-cols-2 divide-x divide-slate-200 sm:grid-cols-4"
        >

            <div class="px-4 py-6 text-center sm:px-6">

                <div
                    class="text-emerald-700"
                >
                    <i class="fa-solid fa-calculator"></i>
                </div>

                <p class="mt-2 text-sm font-bold text-slate-900">
                    Calculate
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Across multiple categories
                </p>

            </div>


            <div class="px-4 py-6 text-center sm:px-6">

                <div
                    class="text-emerald-700"
                >
                    <i class="fa-solid fa-book-open"></i>
                </div>

                <p class="mt-2 text-sm font-bold text-slate-900">
                    Learn
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Understand the principles
                </p>

            </div>


            <div class="px-4 py-6 text-center sm:px-6">

                <div
                    class="text-emerald-700"
                >
                    <i class="fa-solid fa-file-lines"></i>
                </div>

                <p class="mt-2 text-sm font-bold text-slate-900">
                    Record
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Keep organized reports
                </p>

            </div>


            <div class="px-4 py-6 text-center sm:px-6">

                <div
                    class="text-emerald-700"
                >
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <p class="mt-2 text-sm font-bold text-slate-900">
                    Verify
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Future payment records
                </p>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     ZAKAT CATEGORIES
========================================================= -->

<section class="bg-slate-50 py-20">

    <div class="container-main">


        <div class="max-w-2xl">

            <span
                class="section-label inline-flex rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em]"
            >
                Zakat categories
            </span>


            <h2
                class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl"
            >
                One platform for different types of Zakat.
            </h2>


            <p
                class="mt-4 text-base leading-7 text-slate-600"
            >
                Select the category that matches the wealth or assets
                you want to assess. Each calculator can be developed
                according to its own calculation rules and requirements.
            </p>

        </div>



        <div
            class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
        >


            <!-- Cash -->

            <a
                href="calculator/index.php?type=cash"
                class="category-card soft-card rounded-2xl p-5"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"
                >
                    <i class="fa-solid fa-money-bill-wave text-lg"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Cash & Savings
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Money, savings and other cash-based wealth.
                </p>


                <span
                    class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700"
                >
                    Explore

                    <i class="fa-solid fa-arrow-right"></i>

                </span>

            </a>



            <!-- Gold & Silver -->

            <a
                href="calculator/index.php?type=gold"
                class="category-card soft-card rounded-2xl p-5"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
                >
                    <i class="fa-solid fa-coins text-lg"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Gold & Silver
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Precious metals and relevant holdings.
                </p>


                <span
                    class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700"
                >
                    Explore

                    <i class="fa-solid fa-arrow-right"></i>

                </span>

            </a>



            <!-- Business -->

            <a
                href="calculator/index.php?type=business"
                class="category-card soft-card rounded-2xl p-5"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                >
                    <i class="fa-solid fa-store text-lg"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Business
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Trade goods and business-related Zakat assets.
                </p>


                <span
                    class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700"
                >
                    Explore

                    <i class="fa-solid fa-arrow-right"></i>

                </span>

            </a>



            <!-- Agriculture -->

            <a
                href="calculator/index.php?type=agriculture"
                class="category-card soft-card rounded-2xl p-5"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-lime-50 text-lime-700"
                >
                    <i class="fa-solid fa-seedling text-lg"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Agriculture
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Agricultural produce and harvest assessment.
                </p>


                <span
                    class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700"
                >
                    Explore

                    <i class="fa-solid fa-arrow-right"></i>

                </span>

            </a>



            <!-- Livestock -->

            <a
                href="calculator/index.php?type=livestock"
                class="category-card soft-card rounded-2xl p-5"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600"
                >
                    <i class="fa-solid fa-cow text-lg"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Livestock
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Zakat assessment for eligible livestock.
                </p>


                <span
                    class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-700"
                >
                    Explore

                    <i class="fa-solid fa-arrow-right"></i>

                </span>

            </a>

        </div>



        <div class="mt-8 text-center">

            <a
                href="calculator/index.php"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-800"
            >

                View all calculators

                <i class="fa-solid fa-arrow-right text-xs"></i>

            </a>

        </div>

    </div>

</section>



<!-- =========================================================
     HOW IT WORKS
========================================================= -->

<section class="bg-white py-20">

    <div class="container-main">


        <div class="mx-auto max-w-2xl text-center">

            <span
                class="section-label inline-flex rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em]"
            >
                How ZAKKATPRO works
            </span>


            <h2
                class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl"
            >
                From understanding to verified records.
            </h2>


            <p
                class="mt-4 text-base leading-7 text-slate-600"
            >
                ZAKKATPRO is designed to support the complete journey,
                not only the calculation itself.
            </p>

        </div>



        <div class="mt-14 grid gap-10 md:grid-cols-5">


            <?php

            $steps = [

                [
                    'number' => '01',
                    'icon' => 'fa-book-open',
                    'title' => 'Understand',
                    'text' => 'Learn about Zakat, Nisab, eligible assets and relevant guidance.'
                ],

                [
                    'number' => '02',
                    'icon' => 'fa-calculator',
                    'title' => 'Calculate',
                    'text' => 'Enter your information and calculate the amount due.'
                ],

                [
                    'number' => '03',
                    'icon' => 'fa-file-lines',
                    'title' => 'Review',
                    'text' => 'Review your result and generate a detailed Zakat report.'
                ],

                [
                    'number' => '04',
                    'icon' => 'fa-credit-card',
                    'title' => 'Pay',
                    'text' => 'Use an available online or approved offline payment workflow.'
                ],

                [
                    'number' => '05',
                    'icon' => 'fa-circle-check',
                    'title' => 'Verify',
                    'text' => 'Payment can be reviewed, approved and recorded for future reference.'
                ]

            ];

            ?>


            <?php foreach ($steps as $index => $step): ?>

                <div class="relative text-center">


                    <?php if ($index < count($steps) - 1): ?>

                        <div class="step-line"></div>

                    <?php endif; ?>


                    <div
                        class="relative mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-lg shadow-emerald-700/20"
                    >

                        <i
                            class="fa-solid <?= htmlspecialchars($step['icon']) ?>"
                        ></i>

                    </div>


                    <span
                        class="mt-5 block text-xs font-extrabold tracking-widest text-emerald-700"
                    >
                        <?= htmlspecialchars($step['number']) ?>
                    </span>


                    <h3 class="mt-2 font-bold text-slate-900">
                        <?= htmlspecialchars($step['title']) ?>
                    </h3>


                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        <?= htmlspecialchars($step['text']) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>



<!-- =========================================================
     MORE THAN A CALCULATOR
========================================================= -->

<section class="bg-slate-950 py-20 text-white">

    <div class="container-main">


        <div
            class="grid gap-12 lg:grid-cols-[.85fr_1.15fr] lg:items-center"
        >


            <!-- Left -->

            <div>

                <span
                    class="inline-flex rounded-full bg-emerald-400/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em] text-emerald-300"
                >
                    More than a calculator
                </span>


                <h2
                    class="mt-5 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    A complete digital Zakat ecosystem.
                </h2>


                <p
                    class="mt-5 max-w-xl leading-8 text-slate-300"
                >
                    ZAKKATPRO brings calculation, Islamic knowledge,
                    Fatwa questions, reports, payment workflows and
                    verification into one organized platform.
                </p>


                <a
                    href="about.php"
                    class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-emerald-300 transition hover:text-emerald-200"
                >

                    Discover ZAKKATPRO

                    <i class="fa-solid fa-arrow-right text-xs"></i>

                </a>

            </div>



            <!-- Feature grid -->

            <div class="grid gap-4 sm:grid-cols-2">


                <?php

                $features = [

                    [
                        'icon' => 'fa-calculator',
                        'title' => 'Smart Calculation',
                        'text' => 'Structured calculators for different Zakat categories.'
                    ],

                    [
                        'icon' => 'fa-book-open',
                        'title' => 'Knowledge Centre',
                        'text' => 'Clear educational content and methodology references.'
                    ],

                    [
                        'icon' => 'fa-scale-balanced',
                        'title' => 'Fatwa Questions',
                        'text' => 'A structured workflow for questions and reviewed answers.'
                    ],

                    [
                        'icon' => 'fa-file-lines',
                        'title' => 'Detailed Reports',
                        'text' => 'Keep a complete record of calculations and payment information.'
                    ],

                    [
                        'icon' => 'fa-credit-card',
                        'title' => 'Payment Workflow',
                        'text' => 'Support for online and approved offline payment processes.'
                    ],

                    [
                        'icon' => 'fa-qrcode',
                        'title' => 'Verification',
                        'text' => 'Future certificates can be verified through a public reference system.'
                    ]

                ];

                ?>


                <?php foreach ($features as $feature): ?>

                    <div
                        class="rounded-2xl border border-white/10 bg-white/5 p-6"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300"
                        >

                            <i
                                class="fa-solid <?= htmlspecialchars($feature['icon']) ?>"
                            ></i>

                        </div>


                        <h3 class="mt-5 font-bold">
                            <?= htmlspecialchars($feature['title']) ?>
                        </h3>


                        <p
                            class="mt-2 text-sm leading-6 text-slate-400"
                        >
                            <?= htmlspecialchars($feature['text']) ?>
                        </p>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     KNOWLEDGE + FATWA
========================================================= -->

<section class="bg-slate-50 py-20">

    <div class="container-main">


        <div class="mx-auto max-w-2xl text-center">

            <span
                class="section-label inline-flex rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em]"
            >
                Knowledge & Guidance
            </span>


            <h2
                class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl"
            >
                Learn first. Ask when you need guidance.
            </h2>


            <p
                class="mt-4 leading-7 text-slate-600"
            >
                Educational resources and a structured Fatwa question
                workflow help users find information before and after
                using the calculators.
            </p>

        </div>



        <div class="mt-12 grid gap-6 lg:grid-cols-2">


            <!-- Knowledge -->

            <article
                class="soft-card rounded-3xl p-7 sm:p-9"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"
                >

                    <i class="fa-solid fa-book-open"></i>

                </div>


                <span
                    class="mt-6 block text-xs font-bold uppercase tracking-[.12em] text-emerald-700"
                >
                    Knowledge Centre
                </span>


                <h3
                    class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950"
                >
                    Understand the principles behind Zakat.
                </h3>


                <p
                    class="mt-4 leading-7 text-slate-600"
                >
                    Explore topics such as Zakat obligations, Nisab,
                    Hawl, eligible assets, calculation principles,
                    agricultural measurements, methodologies and sources.
                </p>


                <a
                    href="knowledge/index.php"
                    class="mt-7 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                >

                    Explore Knowledge

                    <i class="fa-solid fa-arrow-right text-xs"></i>

                </a>

            </article>



            <!-- Fatwa -->

            <article
                class="soft-card rounded-3xl p-7 sm:p-9"
            >

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                >

                    <i class="fa-solid fa-scale-balanced"></i>

                </div>


                <span
                    class="mt-6 block text-xs font-bold uppercase tracking-[.12em] text-blue-700"
                >
                    Fatwa & Questions
                </span>


                <h3
                    class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950"
                >
                    Questions should follow a careful review process.
                </h3>


                <p
                    class="mt-4 leading-7 text-slate-600"
                >
                    Browse published questions and answers or submit
                    a question through the platform for the appropriate
                    review and response workflow.
                </p>


                <a
                    href="fatwa/index.php"
                    class="mt-7 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                >

                    Explore Fatwa

                    <i class="fa-solid fa-arrow-right text-xs"></i>

                </a>

            </article>

        </div>

    </div>

</section>



<!-- =========================================================
     REPORT + CERTIFICATE
========================================================= -->

<section class="bg-white py-20">

    <div class="container-main">


        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">


            <!-- Text -->

            <div>

                <span
                    class="section-label inline-flex rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em]"
                >
                    Records & verification
                </span>


                <h2
                    class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl"
                >
                    Reports and certificates have different purposes.
                </h2>


                <p
                    class="mt-5 leading-8 text-slate-600"
                >
                    A detailed Zakat Report can preserve the calculation,
                    assets, deductions, Nisab and payment information.
                    A Zakat Payment Certificate can serve as a concise
                    record of a confirmed and approved payment.
                </p>


                <div class="mt-7 space-y-4">


                    <div class="flex gap-3">

                        <div
                            class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"
                        >
                            <i class="fa-solid fa-file-lines text-xs"></i>
                        </div>


                        <div>

                            <h3 class="font-bold text-slate-900">
                                Detailed Zakat Report
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Complete calculation and transaction record.
                            </p>

                        </div>

                    </div>



                    <div class="flex gap-3">

                        <div
                            class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600"
                        >
                            <i class="fa-solid fa-certificate text-xs"></i>
                        </div>


                        <div>

                            <h3 class="font-bold text-slate-900">
                                Zakat Payment Certificate
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Short formal evidence of a confirmed payment,
                                subject to the platform's approval workflow.
                            </p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Visual card -->

            <div
                class="rounded-3xl border border-slate-200 bg-slate-50 p-5 sm:p-7"
            >

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                >


                    <div
                        class="flex items-start justify-between gap-4"
                    >

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-[.14em] text-emerald-700"
                            >
                                ZAKKATPRO
                            </p>

                            <h3
                                class="mt-2 text-xl font-extrabold text-slate-900"
                            >
                                Payment Record
                            </h3>

                        </div>


                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"
                        >
                            <i class="fa-solid fa-qrcode"></i>
                        </div>

                    </div>


                    <div
                        class="mt-7 grid gap-4 sm:grid-cols-2"
                    >

                        <div>

                            <p class="text-xs text-slate-400">
                                Status
                            </p>

                            <p class="mt-1 font-bold text-emerald-700">
                                Approved
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-400">
                                Record
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                ZKT-2026-000184
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-400">
                                Category
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                Business Zakat
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-400">
                                Verification
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                Available
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-7 border-t border-slate-100 pt-5"
                    >

                        <p class="text-xs leading-6 text-slate-500">
                            This interface represents the planned
                            institutional record and verification workflow.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     METHODOLOGY / TRUST
========================================================= -->

<section class="bg-slate-50 py-20">

    <div class="container-main">


        <div class="mx-auto max-w-3xl text-center">

            <span
                class="section-label inline-flex rounded-full px-3 py-1.5 text-xs font-bold uppercase tracking-[.12em]"
            >
                Designed for trust
            </span>


            <h2
                class="mt-4 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl"
            >
                Clear separation between knowledge, calculation and records.
            </h2>


            <p
                class="mt-4 leading-7 text-slate-600"
            >
                Each part of the platform has a defined purpose so users
                can understand where information comes from and how
                their records are handled.
            </p>

        </div>



        <div
            class="mx-auto mt-12 grid max-w-5xl gap-5 sm:grid-cols-3"
        >


            <!-- Sources -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 text-center"
            >

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"
                >
                    <i class="fa-solid fa-book"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Sources & Knowledge
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Educational content and methodology references
                    remain accessible to users.
                </p>

            </div>



            <!-- Calculation -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 text-center"
            >

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"
                >
                    <i class="fa-solid fa-calculator"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Calculation
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Calculators stay focused on collecting inputs and
                    presenting understandable results.
                </p>

            </div>



            <!-- Records -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 text-center"
            >

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700"
                >
                    <i class="fa-solid fa-shield-halved"></i>
                </div>


                <h3 class="mt-5 font-bold text-slate-900">
                    Records & Verification
                </h3>


                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Reports, payments, approvals and future certificates
                    can remain connected to reliable records.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     FINAL CTA
========================================================= -->

<section class="bg-emerald-700 py-16">

    <div class="container-main">

        <div
            class="flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center"
        >


            <div class="max-w-2xl text-white">

                <p
                    class="text-xs font-bold uppercase tracking-[.14em] text-emerald-200"
                >
                    Begin your Zakat journey
                </p>


                <h2
                    class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl"
                >
                    Understand. Calculate. Manage. Pay. Verify.
                </h2>


                <p
                    class="mt-4 leading-7 text-emerald-50"
                >
                    Start with the calculator, explore the Knowledge
                    Centre, or learn more about the ZAKKATPRO platform.
                </p>

            </div>


            <div
                class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row"
            >

                <a
                    href="calculator/index.php"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50"
                >

                    Calculate Zakat

                    <i class="fa-solid fa-arrow-right text-xs"></i>

                </a>


                <a
                    href="auth/register.php"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15"
                >

                    Create Account

                    <i class="fa-solid fa-user-plus text-xs"></i>

                </a>

            </div>

        </div>

    </div>

</section>

</main>



<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="bg-slate-950 text-slate-300">

    <div class="container-main py-14">


        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">


            <!-- Brand -->

            <div class="lg:col-span-2">

                <div class="flex items-center gap-3">

                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white"
                    >
                        <i class="fa-solid fa-mosque"></i>
                    </span>


                    <span class="text-lg font-extrabold text-white">
                        ZAKKATPRO
                    </span>

                </div>


                <p
                    class="mt-5 max-w-md text-sm leading-7 text-slate-400"
                >
                    A comprehensive digital platform designed to help
                    individuals and organizations understand, calculate,
                    manage, pay and keep reliable records of their Zakat.
                </p>


                <div
                    class="mt-5 flex flex-wrap gap-2 text-xs"
                >

                    <span
                        class="rounded-full border border-white/10 px-3 py-1.5"
                    >
                        Understand
                    </span>

                    <span
                        class="rounded-full border border-white/10 px-3 py-1.5"
                    >
                        Calculate
                    </span>

                    <span
                        class="rounded-full border border-white/10 px-3 py-1.5"
                    >
                        Report
                    </span>

                    <span
                        class="rounded-full border border-white/10 px-3 py-1.5"
                    >
                        Pay
                    </span>

                    <span
                        class="rounded-full border border-white/10 px-3 py-1.5"
                    >
                        Verify
                    </span>

                </div>

            </div>



            <!-- Platform -->

            <div>

                <h3 class="text-sm font-bold text-white">
                    Platform
                </h3>


                <ul class="mt-4 space-y-3 text-sm">

                    <li>
                        <a
                            href="calculator/index.php"
                            class="transition hover:text-emerald-300"
                        >
                            Calculator
                        </a>
                    </li>


                    <li>
                        <a
                            href="knowledge/index.php"
                            class="transition hover:text-emerald-300"
                        >
                            Knowledge
                        </a>
                    </li>


                    <li>
                        <a
                            href="fatwa/index.php"
                            class="transition hover:text-emerald-300"
                        >
                            Fatwa
                        </a>
                    </li>


                    <li>
                        <a
                            href="about.php"
                            class="transition hover:text-emerald-300"
                        >
                            About ZAKKATPRO
                        </a>
                    </li>

                </ul>

            </div>



            <!-- Account -->

            <div>

                <h3 class="text-sm font-bold text-white">
                    Account
                </h3>


                <ul class="mt-4 space-y-3 text-sm">

                    <li>
                        <a
                            href="auth/login.php"
                            class="transition hover:text-emerald-300"
                        >
                            Sign in
                        </a>
                    </li>


                    <li>
                        <a
                            href="auth/register.php"
                            class="transition hover:text-emerald-300"
                        >
                            Create account
                        </a>
                    </li>


                    <li>
                        <a
                            href="#"
                            class="transition hover:text-emerald-300"
                        >
                            Privacy
                        </a>
                    </li>


                    <li>
                        <a
                            href="#"
                            class="transition hover:text-emerald-300"
                        >
                            Terms
                        </a>
                    </li>

                </ul>

            </div>

        </div>



        <!-- Bottom -->

        <div
            class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between"
        >

            <p>
                © <?= date('Y') ?> ZAKKATPRO. All rights reserved.
            </p>


            <p>
                Designed for clarity, accessibility and future growth.
            </p>

        </div>

    </div>

</footer>



<!-- =========================================================
     GLOBAL JAVASCRIPT
========================================================= -->

<script src="assets/js/main.js"></script>


</body>

</html>