<?php
/*
|--------------------------------------------------------------------------
| ZAKKATPRO Global Footer
|--------------------------------------------------------------------------
| Shared footer for all pages.
| Uses app_url() from includes/header.php to prevent broken paths
| when pages are inside nested folders.
|--------------------------------------------------------------------------
*/
?>

</main>


<!-- ============================================================
     FOOTER
============================================================ -->

<footer
    class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950"
>

    <div
        class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
    >

        <div
            class="grid gap-8 md:grid-cols-2 lg:grid-cols-4"
        >

            <!-- BRAND -->

            <div>

                <a
                    href="<?= app_url('index.php') ?>"
                    class="inline-flex items-center gap-2"
                >

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-700 text-white"
                    >
                        <i class="fa-solid fa-mosque text-sm"></i>
                    </div>

                    <span
                        class="text-lg font-extrabold tracking-tight text-emerald-700"
                    >
                        ZAKKATPRO
                    </span>

                </a>

                <p
                    class="mt-4 max-w-sm text-sm leading-6 text-slate-500 dark:text-slate-400"
                >
                    A comprehensive digital platform designed to help
                    individuals and organizations understand, calculate,
                    manage, and keep reliable records of Zakat.
                </p>

            </div>


            <!-- PLATFORM -->

            <div>

                <h3
                    class="text-sm font-bold text-slate-900 dark:text-white"
                >
                    Platform
                </h3>

                <ul class="mt-4 space-y-3">

                    <li>
                        <a
                            href="<?= app_url('calculator/index.php') ?>"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Calculator
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= app_url('knowledge/index.php') ?>"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Knowledge
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= app_url('fatwa/index.php') ?>"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Fatwa
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= app_url('about.php') ?>"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            About
                        </a>
                    </li>

                </ul>

            </div>


            <!-- SUPPORT -->

            <div>

                <h3
                    class="text-sm font-bold text-slate-900 dark:text-white"
                >
                    Support
                </h3>

                <ul class="mt-4 space-y-3">

                    <li>
                        <a
                            href="#"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Help Center
                        </a>
                    </li>

                    <li>
                        <a
                            href="#"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Frequently Asked Questions
                        </a>
                    </li>

                    <li>
                        <a
                            href="#"
                            class="text-sm text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                        >
                            Contact Us
                        </a>
                    </li>

                </ul>

            </div>


            <!-- PRINCIPLES -->

            <div>

                <h3
                    class="text-sm font-bold text-slate-900 dark:text-white"
                >
                    ZAKKATPRO
                </h3>

                <p
                    class="mt-4 text-sm leading-6 text-slate-500 dark:text-slate-400"
                >
                    Understand
                    <span class="text-emerald-600">•</span>
                    Calculate
                    <span class="text-emerald-600">•</span>
                    Report
                    <span class="text-emerald-600">•</span>
                    Pay
                    <span class="text-emerald-600">•</span>
                    Verify
                </p>

            </div>

        </div>


        <!-- BOTTOM -->

        <div
            class="mt-10 flex flex-col gap-4 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
        >

            <p
                class="text-xs text-slate-500 dark:text-slate-400"
            >
                © <?= date('Y') ?> ZAKKATPRO. All rights reserved.
            </p>

            <div
                class="flex items-center gap-5"
            >

                <a
                    href="#"
                    class="text-xs text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                >
                    Privacy
                </a>

                <a
                    href="#"
                    class="text-xs text-slate-500 transition hover:text-emerald-700 dark:text-slate-400 dark:hover:text-emerald-400"
                >
                    Terms
                </a>

            </div>

        </div>

    </div>

</footer>


<!-- ============================================================
     GLOBAL JAVASCRIPT
============================================================ -->

<script src="<?= app_url('assets/js/main.js') ?>"></script>

</body>
</html>