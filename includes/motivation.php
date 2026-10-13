<?php

if (!isset($motivation) || !is_array($motivation)) {
    return;
}

$type = strtolower(trim((string) ($motivation['type'] ?? 'quran')));

$title = trim((string) ($motivation['title'] ?? ''));
$arabic = trim((string) ($motivation['arabic_text'] ?? ''));
$english = trim((string) ($motivation['english_text'] ?? ''));
$hausa = trim((string) ($motivation['hausa_text'] ?? ''));
$reference = trim((string) ($motivation['reference'] ?? ''));
$sourceUrl = trim((string) ($motivation['source_url'] ?? ''));

$isQuran = $type === 'quran';

$label = $isQuran ? 'Qur’an' : 'Hadith';

$icon = $isQuran
    ? 'fa-book-quran'
    : 'fa-mosque';
?>

<section
    class="overflow-hidden rounded-2xl border border-green-100 bg-white shadow-sm
           dark:border-green-900/50 dark:bg-gray-900"
>

    <!-- Header -->
    <div class="bg-gradient-to-r from-green-700 to-green-600 px-5 py-4">

        <div class="flex items-center justify-between gap-3">

            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center
                           rounded-xl bg-white/15 text-white"
                >
                    <i class="fa-solid <?= htmlspecialchars($icon) ?>"></i>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-100">
                        Zakat Motivation
                    </p>

                    <p class="text-sm font-bold text-white">
                        <?= htmlspecialchars($label) ?>
                    </p>
                </div>

            </div>

            <span
                class="rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white"
            >
                <?= htmlspecialchars($reference) ?>
            </span>

        </div>

    </div>


    <!-- Content -->
    <div class="p-5">

        <?php if ($title !== ''): ?>

            <h3
                class="mb-4 text-lg font-bold leading-7 text-gray-900 dark:text-white"
            >
                <?= htmlspecialchars($title) ?>
            </h3>

        <?php endif; ?>


        <!-- Arabic -->
        <?php if ($arabic !== ''): ?>

            <div
                class="mb-5 rounded-xl border border-green-100 bg-green-50/70 p-4
                       dark:border-green-900/40 dark:bg-green-950/20"
                dir="rtl"
            >

                <p
                    class="font-arabic text-right text-xl leading-[2.2] text-gray-900
                           dark:text-gray-100"
                    lang="ar"
                >
                    <?= nl2br(htmlspecialchars($arabic)) ?>
                </p>

            </div>

        <?php endif; ?>


        <!-- English -->
        <?php if ($english !== ''): ?>

            <div class="mb-4">

                <div class="mb-2 flex items-center gap-2">

                    <span
                        class="h-2 w-2 rounded-full bg-green-600"
                    ></span>

                    <span
                        class="text-xs font-bold uppercase tracking-wide text-gray-500
                               dark:text-gray-400"
                    >
                        English
                    </span>

                </div>

                <p
                    class="text-sm leading-7 text-gray-700 dark:text-gray-300"
                >
                    <?= nl2br(htmlspecialchars($english)) ?>
                </p>

            </div>

        <?php endif; ?>


        <!-- Hausa -->
        <?php if ($hausa !== ''): ?>

            <div
                class="rounded-xl border border-gray-100 bg-gray-50 p-4
                       dark:border-gray-800 dark:bg-gray-800/60"
            >

                <div class="mb-2 flex items-center gap-2">

                    <span
                        class="h-2 w-2 rounded-full bg-green-600"
                    ></span>

                    <span
                        class="text-xs font-bold uppercase tracking-wide text-gray-500
                               dark:text-gray-400"
                    >
                        Hausa
                    </span>

                </div>

                <p
                    class="text-sm leading-7 text-gray-700 dark:text-gray-300"
                >
                    <?= nl2br(htmlspecialchars($hausa)) ?>
                </p>

            </div>

        <?php endif; ?>


        <!-- Footer -->
        <div
            class="mt-5 flex flex-wrap items-center justify-between gap-3
                   border-t border-gray-100 pt-4 dark:border-gray-800"
        >

            <div class="flex items-center gap-2">

                <i class="fa-solid fa-circle-check text-sm text-green-600"></i>

                <span class="text-xs text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($reference) ?>
                </span>

            </div>


            <?php if ($sourceUrl !== ''): ?>

                <a
                    href="<?= htmlspecialchars($sourceUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold
                           text-green-700 hover:text-green-800
                           dark:text-green-400 dark:hover:text-green-300"
                >
                    Verify source
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>