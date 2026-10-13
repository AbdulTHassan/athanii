<?php include '../includes/header.php'; ?>

<div class="flex min-h-screen bg-gray-50">

    <!-- SIDEBAR -->
    <div class="w-64 bg-white border-r border-gray-200 p-6">

        <h2 class="text-lg font-semibold text-green-700 mb-6">
            Knowledge
        </h2>

        <nav class="space-y-3 text-sm">

            <a href="index.php"
               class="block text-green-700 font-medium">
                Overview
            </a>

            <a href="view.php?page=basics"
               class="block text-gray-600 hover:text-green-600">
                Basics of Zakat
            </a>

            <a href="view.php?page=nisab"
               class="block text-gray-600 hover:text-green-600">
                Nisab & Threshold
            </a>

            <a href="view.php?page=cash"
               class="block text-gray-600 hover:text-green-600">
                Cash & Savings
            </a>

            <a href="view.php?page=livestock"
               class="block text-gray-600 hover:text-green-600">
                Livestock
            </a>

            <a href="view.php?page=agriculture"
               class="block text-gray-600 hover:text-green-600">
                Agriculture
            </a>

        </nav>

    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 p-10">

        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800">
                Zakat Knowledge Base
            </h1>

            <p class="text-gray-500 mt-2 max-w-2xl">
                Learn Zakat in a structured and authentic way based on
                Qur’an, Hadith, and classical Islamic scholarship.
            </p>
        </div>

        <!-- FEATURED ARTICLES -->
        <div class="mb-12">

            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Featured Topics
            </h2>

            <div class="grid md:grid-cols-3 gap-6">

                <a href="view.php?page=basics"
                   class="bg-white border border-gray-200 p-5 rounded-xl hover:shadow-md transition">

                    <h3 class="font-semibold text-green-700 mb-2">
                        What is Zakat?
                    </h3>

                    <p class="text-sm text-gray-500">
                        Understand the meaning, purpose, and obligation of Zakat.
                    </p>

                </a>

                <a href="view.php?page=nisab"
                   class="bg-white border border-gray-200 p-5 rounded-xl hover:shadow-md transition">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Nisab Explained
                    </h3>

                    <p class="text-sm text-gray-500">
                        Learn the minimum threshold for Zakat obligation.
                    </p>

                </a>

                <a href="view.php?page=conditions"
                   class="bg-white border border-gray-200 p-5 rounded-xl hover:shadow-md transition">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Conditions of Zakat
                    </h3>

                    <p class="text-sm text-gray-500">
                        Who must pay Zakat and when it becomes due.
                    </p>

                </a>

            </div>

        </div>

        <!-- CATEGORIES -->
        <div>

            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Zakat Categories
            </h2>

            <div class="grid md:grid-cols-2 gap-6">

                <!-- CASH -->
                <a href="view.php?page=cash"
                   class="bg-white border border-gray-200 p-6 rounded-xl hover:shadow-md">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Cash & Savings
                    </h3>

                    <p class="text-sm text-gray-500">
                        Zakat on money, bank savings, and financial assets.
                    </p>

                </a>

                <!-- LIVESTOCK -->
                <a href="view.php?page=livestock"
                   class="bg-white border border-gray-200 p-6 rounded-xl hover:shadow-md">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Livestock
                    </h3>

                    <p class="text-sm text-gray-500">
                        Rules for goats, cows, and camels.
                    </p>

                </a>

                <!-- AGRICULTURE -->
                <a href="view.php?page=agriculture"
                   class="bg-white border border-gray-200 p-6 rounded-xl hover:shadow-md">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Agriculture
                    </h3>

                    <p class="text-sm text-gray-500">
                        Zakat on crops and farm produce.
                    </p>

                </a>

                <!-- GOLD -->
                <a href="view.php?page=gold"
                   class="bg-white border border-gray-200 p-6 rounded-xl hover:shadow-md">

                    <h3 class="font-semibold text-green-700 mb-2">
                        Gold & Silver
                    </h3>

                    <p class="text-sm text-gray-500">
                        Zakat rules for gold, silver, and jewelry.
                    </p>

                </a>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>