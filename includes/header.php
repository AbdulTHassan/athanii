<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Project Root
|--------------------------------------------------------------------------
| Automatically detects /Athanii/ even when this header is included
| from a nested page.
|--------------------------------------------------------------------------
*/

$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';

$knownFolders = [
    '/calculator/',
    '/knowledge/',
    '/fatwa/',
    '/auth/',
    '/admin/',
    '/user/',
    '/reports/',
    '/payments/',
    '/certificates/',
    '/language/',
];

$projectRoot = '/';

foreach ($knownFolders as $folder) {
    $position = strpos($scriptPath, $folder);

    if ($position !== false) {
        $projectRoot = substr($scriptPath, 0, $position + 1);
        break;
    }
}

if ($projectRoot === '/') {
    $directory = dirname($scriptPath);

    if ($directory !== '/' && $directory !== '\\') {
        $projectRoot = rtrim($directory, '/') . '/';
    }
}

/*
|--------------------------------------------------------------------------
| URL Helper
|--------------------------------------------------------------------------
*/

if (!function_exists('app_url')) {
    function app_url($path = '')
    {
        global $projectRoot;

        return rtrim($projectRoot, '/') . '/' . ltrim($path, '/');
    }
}

require_once __DIR__ . '/functions.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="ZAKKATPRO - A comprehensive digital platform for understanding, calculating, managing and keeping reliable records of Zakat."
    >

    <title>ZAKKATPRO</title>

    <!-- Google Fonts -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',

            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b'
                        },

                        gold: {
                            400: '#d4af37',
                            500: '#c9a227',
                            600: '#b08d1e'
                        }
                    }
                }
            }
        };
    </script>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <!-- Global CSS -->
    <link
        rel="stylesheet"
        href="<?= app_url('assets/css/app.css') ?>"
    >

</head>


<body class="bg-slate-50 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">


<!-- ============================================================
     NAVIGATION
============================================================ -->

<header
    class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95"
>

    <nav
        class="mx-auto flex min-h-[72px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
    >

        <!-- BRAND -->
        <a
            href="<?= app_url('index.php') ?>"
            class="flex items-center gap-2"
        >

            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm"
            >
                <i class="fa-solid fa-mosque"></i>
            </div>

            <div class="leading-tight">

                <span
                    class="block text-lg font-extrabold tracking-tight text-emerald-700"
                >
                    ZAKKATPRO
                </span>

                <span
                    class="hidden text-[10px] font-medium tracking-wide text-slate-500 sm:block dark:text-slate-400"
                >
                    Understand • Calculate • Manage
                </span>

            </div>

        </a>


        <!-- DESKTOP NAVIGATION -->
        <nav class="hidden items-center gap-7 lg:flex">

            <a
                href="<?= app_url('index.php') ?>"
                class="text-sm font-medium text-slate-600 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400"
            >
                Home
            </a>

            <a
                href="<?= app_url('calculator/index.php') ?>"
                class="text-sm font-medium text-slate-600 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400"
            >
                Calculator
            </a>

            <a
                href="<?= app_url('knowledge/index.php') ?>"
                class="text-sm font-medium text-slate-600 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400"
            >
                Knowledge
            </a>

            <a
                href="<?= app_url('fatwa/index.php') ?>"
                class="text-sm font-medium text-slate-600 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400"
            >
                Fatwa
            </a>

            <a
                href="<?= app_url('about.php') ?>"
                class="text-sm font-medium text-slate-600 transition hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400"
            >
                About
            </a>

        </nav>


        <!-- RIGHT SIDE -->
        <div class="hidden items-center gap-3 lg:flex">

            <!-- LANGUAGE -->
            <div class="relative">

                <button
                    id="languageButton"
                    type="button"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-emerald-400"
                    aria-expanded="false"
                >

                    <i class="fa-solid fa-globe text-sm"></i>

                    <span id="currentLanguage">
                        English
                    </span>

                    <i class="fa-solid fa-chevron-down text-[10px]"></i>

                </button>


                <!-- LANGUAGE DROPDOWN -->

                <div
                    id="languageMenu"
                    class="absolute right-0 mt-2 hidden w-40 overflow-hidden rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                >

                    <button
                        type="button"
                        class="language-option flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-200 dark:hover:bg-slate-800"
                        data-language="en"
                    >
                        English
                    </button>

                    <button
                        type="button"
                        class="language-option flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-200 dark:hover:bg-slate-800"
                        data-language="ha"
                    >
                        Hausa
                    </button>

                    <button
                        type="button"
                        class="language-option flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-200 dark:hover:bg-slate-800"
                        data-language="ar"
                    >
                        العربية
                    </button>

                </div>

            </div>


            <!-- DARK MODE -->

            <button
                id="themeToggle"
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-emerald-400"
                aria-label="Toggle dark mode"
            >

                <i
                    id="themeIcon"
                    class="fa-solid fa-moon"
                ></i>

            </button>


            <!-- SIGN IN -->

            <a
                href="<?= app_url('auth/login.php') ?>"
                class="rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800"
            >
                Sign in
            </a>

        </div>


        <!-- MOBILE MENU BUTTON -->

        <button
            id="mobileMenuButton"
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 lg:hidden dark:text-slate-200 dark:hover:bg-slate-800"
            aria-label="Open menu"
            aria-expanded="false"
        >

            <i
                id="mobileMenuIcon"
                class="fa-solid fa-bars"
            ></i>

        </button>

    </nav>


    <!-- MOBILE MENU -->

    <div
        id="mobileMenu"
        class="hidden border-t border-slate-200 bg-white px-4 py-4 lg:hidden dark:border-slate-800 dark:bg-slate-950"
    >

        <div class="flex flex-col gap-1">

            <a
                href="<?= app_url('index.php') ?>"
                class="mobile-nav-link rounded-lg px-3 py-3 text-sm font-semibold text-emerald-700"
            >
                Home
            </a>

            <a
                href="<?= app_url('calculator/index.php') ?>"
                class="mobile-nav-link rounded-lg px-3 py-3 text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-900"
            >
                Calculator
            </a>

            <a
                href="<?= app_url('knowledge/index.php') ?>"
                class="mobile-nav-link rounded-lg px-3 py-3 text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-900"
            >
                Knowledge
            </a>

            <a
                href="<?= app_url('fatwa/index.php') ?>"
                class="mobile-nav-link rounded-lg px-3 py-3 text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-900"
            >
                Fatwa
            </a>

            <a
                href="<?= app_url('about.php') ?>"
                class="mobile-nav-link rounded-lg px-3 py-3 text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-slate-300 dark:hover:bg-slate-900"
            >
                About
            </a>


            <div class="my-2 border-t border-slate-200 dark:border-slate-800"></div>


            <!-- MOBILE LANGUAGE -->

            <div class="px-3 py-2">

                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Language
                </p>

                <div class="flex flex-wrap gap-2">

                    <button
                        type="button"
                        class="language-option rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"
                        data-language="en"
                    >
                        English
                    </button>

                    <button
                        type="button"
                        class="language-option rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"
                        data-language="ha"
                    >
                        Hausa
                    </button>

                    <button
                        type="button"
                        class="language-option rounded-lg border border-slate-200 px-3 py-2 text-sm dark:border-slate-700"
                        data-language="ar"
                    >
                        العربية
                    </button>

                </div>

            </div>


            <!-- MOBILE THEME -->

            <button
                id="mobileThemeToggle"
                type="button"
                class="mt-2 flex items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-900"
            >

                <i class="fa-solid fa-moon"></i>

                <span>
                    Dark Mode
                </span>

            </button>


            <!-- MOBILE SIGN IN -->

            <a
                href="<?= app_url('auth/login.php') ?>"
                class="mt-2 rounded-xl bg-emerald-700 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-emerald-800"
            >
                Sign in
            </a>

        </div>

    </div>

</header>


<!-- MAIN CONTENT -->

<main id="main-content"> 