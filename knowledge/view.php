<?php
$page = $_GET['page'] ?? 'basics';
include '../includes/header.php';
?>

<div class="flex min-h-screen bg-gray-50">

    <!-- SIDEBAR -->
    <div class="w-64 bg-white border-r border-gray-200 p-6">

        <h2 class="text-lg font-semibold text-green-700 mb-6">
            Knowledge
        </h2>

        <nav class="space-y-3 text-sm">

            <a href="view.php?page=basics" class="block text-gray-600 hover:text-green-600">
                Basics of Zakat
            </a>

            <a href="view.php?page=nisab" class="block text-gray-600 hover:text-green-600">
                Nisab
            </a>

            <a href="view.php?page=cash" class="block text-gray-600 hover:text-green-600">
                Cash & Savings
            </a>

            <a href="view.php?page=livestock" class="block text-gray-600 hover:text-green-600">
                Livestock
            </a>

            <a href="view.php?page=agriculture" class="block text-gray-600 hover:text-green-600">
                Agriculture
            </a>
             <a href="view.php?page=business" class="block text-gray-600 hover:text-green-600">
                Business
            </a>
             <a href="view.php?page=fitr" class="block text-gray-600 hover:text-green-600">
                Fitr
            </a>
             <a href="view.php?page=gold" class="block text-gray-600 hover:text-green-600">
                Gold & Silver
            </a>
        </nav>

    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1">

        <!-- HERO HEADER (LIKE HOMEPAGE) -->
        <div class="bg-green-700 text-white py-12 px-10">

            <div class="max-w-4xl mx-auto text-center">

                <h1 class="text-3xl md:text-4xl font-bold mb-3">
                    <?php echo ucfirst(str_replace('_', ' ', $page)); ?>
                </h1>

                <p class="text-green-100">
                    Learn authentic rulings of Zakat based on Qur’an and Sunnah
                </p>

            </div>

        </div>

        <!-- CONTENT AREA -->
        <div class="max-w-4xl mx-auto p-8">

            <div class="bg-white rounded-xl shadow p-6">

                <?php
                $file = "topics/" . $page . ".php";

                if (file_exists($file)) {
                    include $file;
                } else {
                    echo "<p>Content not found</p>";
                }
                ?>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>