<?php

session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';

/*
|--------------------------------------------------------------------------
| SIGNUP PROCESS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $language = $_POST['language'] ?? 'en';

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($full_name === '') {

        $error = 'Please enter your full name.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } elseif ($phone === '') {

        $error = 'Please enter your phone number.';

    } elseif ($password === '') {

        $error = 'Please enter a password.';

    } elseif (strlen($password) < 8) {

        $error = 'Password must be at least 8 characters long.';

    } elseif ($password !== $confirm_password) {

        $error = 'Passwords do not match.';

    } elseif (!in_array($language, ['en', 'ha', 'ar'], true)) {

        $error = 'Invalid language selected.';
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK IF EMAIL ALREADY EXISTS
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        try {

            $stmt = $pdo->prepare(
                "SELECT id
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );

            $stmt->execute([$email]);

            if ($stmt->fetch()) {

                $error = 'An account with this email address already exists.';
            }

        } catch (PDOException $e) {

            // Temporary detailed error for testing
            $error = 'Database error: ' . $e->getMessage();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE USER ACCOUNT
    |--------------------------------------------------------------------------
    */

    if ($error === '') {

        try {

            /*
            |--------------------------------------------------------------------------
            | GET USER ROLE ID
            |--------------------------------------------------------------------------
            */

            $roleStmt = $pdo->prepare(
                "SELECT id
                 FROM roles
                 WHERE name = 'User'
                 LIMIT 1"
            );

            $roleStmt->execute();

            $role = $roleStmt->fetch();


            if (!$role) {

                throw new Exception(
                    'User role was not found in the database.'
                );
            }


            $role_id = $role['id'];


            /*
            |--------------------------------------------------------------------------
            | HASH PASSWORD
            |--------------------------------------------------------------------------
            */

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /*
            |--------------------------------------------------------------------------
            | INSERT USER
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (
                    role_id,
                    full_name,
                    email,
                    password,
                    phone,
                    language,
                    status
                )
                VALUES
                (
                    :role_id,
                    :full_name,
                    :email,
                    :password,
                    :phone,
                    :language,
                    'active'
                )"
            );


            $stmt->execute([

                ':role_id' => $role_id,

                ':full_name' => $full_name,

                ':email' => $email,

                ':password' => $password_hash,

                ':phone' => $phone,

                ':language' => $language

            ]);


            /*
            |--------------------------------------------------------------------------
            | REDIRECT TO LOGIN
            |--------------------------------------------------------------------------
            */

            header(
                'Location: login.php?registered=1'
            );

            exit;


        } catch (Exception $e) {

            $error = 'Unable to create your account: ' . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Account | ZakatPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>

        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#14532D',
                        secondary: '#1F6B45',
                        lightGreen: '#F1F8F4',
                        cream: '#FAF9F4'
                    }
                }
            }
        };

    </script>

</head>


<body class="min-h-screen bg-cream">


<!-- ================= HEADER ================= -->

<header class="bg-white border-b border-gray-200">

    <div class="max-w-7xl mx-auto px-6 py-4">

        <div class="flex items-center justify-between">

            <!-- Logo -->

            <a
                href="../index.php"
                class="flex items-center gap-3"
            >

                <div
                    class="w-11 h-11 rounded-xl bg-primary text-white flex items-center justify-center text-xl"
                >
                    ☪
                </div>

                <div>

                    <h1 class="text-xl font-bold text-gray-900">
                        ZakatPro
                    </h1>

                    <p class="text-xs text-gray-500">
                        Purify Your Wealth
                    </p>

                </div>

            </a>


            <!-- Login -->

            <div class="flex items-center gap-4">

                <span class="hidden sm:block text-sm text-gray-500">
                    Already have an account?
                </span>

                <a
                    href="login.php"
                    class="px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-secondary transition"
                >
                    Login
                </a>

            </div>

        </div>

    </div>

</header>


<!-- ================= MAIN ================= -->

<main class="min-h-[calc(100vh-77px)] flex items-center py-10 px-4">


    <div class="max-w-6xl w-full mx-auto">


        <div
            class="grid lg:grid-cols-2 bg-white rounded-3xl overflow-hidden shadow-xl border border-gray-100"
        >


            <!-- ================= LEFT SIDE ================= -->

            <div
                class="hidden lg:flex relative bg-primary p-12 text-white flex-col justify-between overflow-hidden"
            >

                <!-- Background decoration -->

                <div
                    class="absolute -top-20 -right-20 w-72 h-72 rounded-full border border-white/10"
                ></div>

                <div
                    class="absolute top-32 -right-32 w-96 h-96 rounded-full border border-white/10"
                ></div>

                <div
                    class="absolute -bottom-32 -left-24 w-80 h-80 rounded-full border border-white/10"
                ></div>


                <!-- Top -->

                <div class="relative">

                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/10 text-sm"
                    >
                        🕌 Welcome to ZakatPro
                    </div>


                    <h2
                        class="text-4xl font-bold leading-tight mt-8"
                    >
                        Manage your Zakat with confidence.
                    </h2>


                    <p
                        class="text-green-100 text-lg leading-relaxed mt-5"
                    >
                        Calculate your Zakat, keep track of your records,
                        access your reports and manage your Zakat journey
                        from one secure place.
                    </p>

                </div>


                <!-- Features -->

                <div class="relative space-y-5 mt-10">


                    <div class="flex items-center gap-4">

                        <div
                            class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center"
                        >
                            🧮
                        </div>

                        <div>

                            <h3 class="font-semibold">
                                Calculate Zakat
                            </h3>

                            <p class="text-sm text-green-100">
                                Calculate your Zakat accurately.
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-4">

                        <div
                            class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center"
                        >
                            📊
                        </div>

                        <div>

                            <h3 class="font-semibold">
                                Track Your Activity
                            </h3>

                            <p class="text-sm text-green-100">
                                View your calculation history and progress.
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-4">

                        <div
                            class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center"
                        >
                            📄
                        </div>

                        <div>

                            <h3 class="font-semibold">
                                Access Your Reports
                            </h3>

                            <p class="text-sm text-green-100">
                                Keep your Zakat records organized.
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-4">

                        <div
                            class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center"
                        >
                            🔒
                        </div>

                        <div>

                            <h3 class="font-semibold">
                                Secure Account
                            </h3>

                            <p class="text-sm text-green-100">
                                Your information is protected and private.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- Bottom -->

                <div
                    class="relative mt-12 pt-6 border-t border-white/10 text-sm text-green-100"
                >
                    Your Zakat journey starts here.
                </div>

            </div>


            <!-- ================= RIGHT SIDE / FORM ================= -->

            <div class="p-7 sm:p-10 lg:p-12">


                <div class="mb-8">

                    <p class="text-sm font-semibold text-secondary mb-2">
                        CREATE ACCOUNT
                    </p>

                    <h2 class="text-3xl font-bold text-gray-900">
                        Join ZakatPro
                    </h2>

                    <p class="text-gray-500 mt-2">
                        Create your account and start managing your Zakat
                        in a simple and organized way.
                    </p>

                </div>


                <!-- ERROR MESSAGE -->

                <?php if ($error !== ''): ?>

                    <div
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>


                <!-- FORM -->

                <form method="POST" class="space-y-5">


                    <!-- Full Name -->

                    <div>

                        <label
                            for="full_name"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                            placeholder="Enter your full name"
                            required
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                        >

                    </div>


                    <!-- EMAIL -->

                    <div>

                        <label
                            for="email"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="you@example.com"
                            required
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                        >

                        <p class="mt-2 text-xs text-gray-400">
                            You will use this email address to login.
                        </p>

                    </div>


                    <!-- PHONE -->

                    <div>

                        <label
                            for="phone"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                            placeholder="e.g. 08012345678"
                            required
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                        >

                    </div>


                    <!-- LANGUAGE -->

                    <div>

                        <label
                            for="language"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Preferred Language
                        </label>

                        <select
                            id="language"
                            name="language"
                            class="w-full px-4 py-3.5 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                        >

                            <option value="en">
                                English
                            </option>

                            <option value="ha">
                                Hausa
                            </option>

                            <option value="ar">
                                Arabic
                            </option>

                        </select>

                    </div>


                    <!-- PASSWORD -->

                    <div>

                        <label
                            for="password"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Password
                        </label>

                        <div class="relative">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimum 8 characters"
                                required
                                minlength="8"
                                class="w-full px-4 py-3.5 pr-14 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                            >
                                Show
                            </button>

                        </div>

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div>

                        <label
                            for="confirm_password"
                            class="block text-sm font-semibold text-gray-700 mb-2"
                        >
                            Confirm Password
                        </label>

                        <div class="relative">

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Repeat your password"
                                required
                                minlength="8"
                                class="w-full px-4 py-3.5 pr-14 rounded-xl border border-gray-300 bg-white outline-none transition focus:border-primary focus:ring-4 focus:ring-green-100"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('confirm_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                            >
                                Show
                            </button>

                        </div>

                    </div>


                    <!-- SUBMIT -->

                    <button
                        type="submit"
                        class="w-full py-4 rounded-xl bg-primary text-white font-semibold hover:bg-secondary transition duration-200 shadow-lg shadow-green-900/10"
                    >
                        Create My Account →
                    </button>


                </form>


                <!-- LOGIN LINK -->

                <div class="mt-7 text-center">

                    <p class="text-sm text-gray-500">

                        Already have an account?

                        <a
                            href="login.php"
                            class="font-semibold text-primary hover:underline"
                        >
                            Login here
                        </a>

                    </p>

                </div>


                <!-- HOME LINK -->

                <div class="mt-5 text-center">

                    <a
                        href="../index.php"
                        class="text-sm text-gray-400 hover:text-primary transition"
                    >
                        ← Back to Home
                    </a>

                </div>


            </div>

        </div>

    </div>

</main>


<script>

/*
|--------------------------------------------------------------------------
| SHOW / HIDE PASSWORD
|--------------------------------------------------------------------------
*/

function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    if (input.type === 'password') {

        input.type = 'text';
        button.textContent = 'Hide';

    } else {

        input.type = 'password';
        button.textContent = 'Show';
    }
}

</script>


</body>

</html>