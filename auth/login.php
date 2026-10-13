<?php
/**
 * ZakatPro - Secure Unified Login
 *
 * File:
 * C:\xampp\htdocs\athanii\auth\login.php
 *
 * Login is shared by:
 * - User
 * - Admin
 * - Super Admin
 *
 * Role is determined from the database.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_login'])) {
    $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$error = '';
$success = '';

$email = '';

/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function loginEscape($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN
|--------------------------------------------------------------------------
|
| If a user is already authenticated, don't show the login page again.
|
*/

if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {

    $roleId = (int) $_SESSION['role'];

    if ($roleId === 1 || $roleId === 3) {
        header('Location: ../admin/dashboard.php');
        exit;
    }

    if ($roleId === 2) {
        header('Location: ../user/dashboard.php');
        exit;
    }

    // Unknown role
    $_SESSION = [];
}

/*
|--------------------------------------------------------------------------
| LOGIN PROCESS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CSRF VALIDATION
    |--------------------------------------------------------------------------
    */

    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        empty($csrfToken) ||
        empty($_SESSION['csrf_login']) ||
        !hash_equals($_SESSION['csrf_login'], $csrfToken)
    ) {
        $error = 'Your session has expired. Please refresh the page and try again.';
    } else {

        /*
        |--------------------------------------------------------------------------
        | GET FORM DATA
        |--------------------------------------------------------------------------
        */

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | BASIC VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($email === '') {

            $error = 'Please enter your email address.';

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error = 'Please enter a valid email address.';

        } elseif ($password === '') {

            $error = 'Please enter your password.';

        } else {

            try {

                /*
                |--------------------------------------------------------------------------
                | FIND USER
                |--------------------------------------------------------------------------
                */

                $stmt = $pdo->prepare("
                    SELECT
                        id,
                        role_id,
                        full_name,
                        email,
                        password,
                        language,
                        status
                    FROM users
                    WHERE email = ?
                    LIMIT 1
                ");

                $stmt->execute([$email]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                /*
                |--------------------------------------------------------------------------
                | CHECK ACCOUNT
                |--------------------------------------------------------------------------
                */

                if (!$user) {

                    // Same generic message for security.
                    $error = 'Invalid email or password.';

                } elseif (
                    isset($user['status']) &&
                    strtolower($user['status']) !== 'active'
                ) {

                    $error = 'Your account is currently inactive. Please contact support.';

                } elseif (
                    empty($user['password']) ||
                    !password_verify($password, $user['password'])
                ) {

                    $error = 'Invalid email or password.';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESSFUL LOGIN
                    |--------------------------------------------------------------------------
                    */

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['role'] = (int) $user['role_id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_language'] = $user['language'] ?? 'en';

                    /*
                    |--------------------------------------------------------------------------
                    | REDIRECT BASED ON ROLE
                    |--------------------------------------------------------------------------
                    |
                    | Role 1 = Super Admin
                    | Role 2 = User
                    | Role 3 = Admin
                    |
                    | We don't expose these roles on the login page.
                    |
                    */

                    $roleId = (int) $user['role_id'];

                    if ($roleId === 1 || $roleId === 3) {

                        header('Location: ../admin/dashboard.php');
                        exit;

                    } elseif ($roleId === 2) {

                        header('Location: ../user/dashboard.php');
                        exit;

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | UNKNOWN ROLE
                        |--------------------------------------------------------------------------
                        |
                        | If an unexpected role exists, don't allow access.
                        |
                        */

                        $_SESSION = [];

                        if (ini_get('session.use_cookies')) {

                            $params = session_get_cookie_params();

                            setcookie(
                                session_name(),
                                '',
                                time() - 42000,
                                $params['path'],
                                $params['domain'],
                                $params['secure'],
                                $params['httponly']
                            );
                        }

                        session_destroy();

                        $error = 'Your account role is not configured correctly. Please contact support.';
                    }
                }

            } catch (PDOException $e) {

                /*
                |--------------------------------------------------------------------------
                | DATABASE ERROR
                |--------------------------------------------------------------------------
                |
                | Don't expose database details to the user.
                |
                */

                error_log(
                    'ZakatPro Login Database Error: ' .
                    $e->getMessage()
                );

                $error = 'A system error occurred. Please try again later.';
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| PAGE TITLE
|--------------------------------------------------------------------------
*/

$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Sign in to your ZakatPro account."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <title>
        <?= loginEscape($pageTitle) ?> | ZakatPro
    </title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zakat: {
                            50: '#f1f8f5',
                            100: '#dcefe7',
                            200: '#b9dfcf',
                            300: '#8fc9b2',
                            400: '#5faf92',
                            500: '#318267',
                            600: '#246b55',
                            700: '#1d5746',
                            800: '#194638',
                            900: '#143a30'
                        },

                        ivory: '#FAF9F4'
                    },

                    boxShadow: {
                        'zakat': '0 20px 60px rgba(20, 58, 48, 0.10)',
                        'input': '0 0 0 3px rgba(49, 130, 103, 0.10)'
                    }
                }
            }
        };
    </script>

    <style>

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #FAF9F4;
        }

        .auth-pattern {
            background-image:
                radial-gradient(
                    rgba(49, 130, 103, 0.08) 1px,
                    transparent 1px
                );
            background-size: 28px 28px;
        }

        .glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #1f2937;
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.55s ease-out;
        }

    </style>

</head>

<body class="min-h-screen text-slate-800">

    <!-- =========================================================
         MAIN AUTH LAYOUT
    ========================================================== -->

    <main class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6">

        <div class="w-full max-w-6xl">

            <div class="grid lg:grid-cols-2 overflow-hidden rounded-3xl shadow-zakat border border-slate-200 bg-white">

                <!-- =================================================
                     LEFT BRANDING PANEL
                ================================================== -->

                <section
                    class="hidden lg:flex relative overflow-hidden bg-zakat-900 p-12 xl:p-16 text-white"
                >

                    <!-- Decorative pattern -->
                    <div class="absolute inset-0 auth-pattern opacity-30"></div>

                    <!-- Decorative circles -->
                    <div
                        class="absolute -top-24 -right-24 h-72 w-72 rounded-full border border-white/10"
                    ></div>

                    <div
                        class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full border border-white/10"
                    ></div>

                    <div class="relative z-10 flex flex-col justify-between w-full">

                        <!-- Brand -->

                        <div>

                            <div class="flex items-center gap-4">

                                <div
                                    class="h-14 w-14 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center"
                                >
                                    <i class="fa-solid fa-mosque text-2xl"></i>
                                </div>

                                <div>

                                    <h1 class="text-2xl font-bold tracking-tight">
                                        ZakatPro
                                    </h1>

                                    <p class="text-sm text-white/60">
                                        Zakat Management System
                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- Main message -->

                        <div class="my-16">

                            <span
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/10 text-xs font-medium text-emerald-100 mb-6"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                Secure &amp; trusted platform
                            </span>

                            <h2
                                class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight"
                            >
                                Manage your Zakat
                                <span class="text-emerald-300">
                                    with confidence.
                                </span>
                            </h2>

                            <p
                                class="mt-6 max-w-lg text-base xl:text-lg leading-8 text-white/65"
                            >
                                Calculate, track, review and manage your Zakat
                                activities through one secure and organized
                                platform.
                            </p>

                            <!-- Feature list -->

                            <div class="mt-8 space-y-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center"
                                    >
                                        <i class="fa-solid fa-calculator text-sm text-emerald-300"></i>
                                    </div>

                                    <span class="text-sm text-white/80">
                                        Accurate Zakat calculations
                                    </span>

                                </div>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center"
                                    >
                                        <i class="fa-solid fa-chart-line text-sm text-emerald-300"></i>
                                    </div>

                                    <span class="text-sm text-white/80">
                                        Track your Zakat activities
                                    </span>

                                </div>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center"
                                    >
                                        <i class="fa-solid fa-shield-halved text-sm text-emerald-300"></i>
                                    </div>

                                    <span class="text-sm text-white/80">
                                        Secure account management
                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- Bottom -->

                        <div class="pt-8 border-t border-white/10">

                            <p class="text-xs text-white/45">
                                © <?= date('Y') ?> ZakatPro.
                                All rights reserved.
                            </p>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                     RIGHT LOGIN PANEL
                ================================================== -->

                <section class="glass px-6 py-8 sm:px-10 lg:px-12 xl:px-16 flex items-center">

                    <div class="w-full max-w-md mx-auto fade-up">

                        <!-- Mobile Brand -->

                        <div class="lg:hidden text-center mb-8">

                            <div
                                class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-zakat-600 text-white shadow-lg"
                            >
                                <i class="fa-solid fa-mosque text-xl"></i>
                            </div>

                            <h1 class="mt-3 text-2xl font-bold text-zakat-900">
                                ZakatPro
                            </h1>

                            <p class="text-sm text-slate-500 mt-1">
                                Zakat Management System
                            </p>

                        </div>


                        <!-- Header -->

                        <div class="mb-8">

                            <p
                                class="text-sm font-semibold text-zakat-600 mb-2"
                            >
                                Welcome back
                            </p>

                            <h2
                                class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900"
                            >
                                Sign in to your account
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Enter your account details to continue to ZakatPro.
                            </p>

                        </div>


                        <!-- =================================================
                             ERROR MESSAGE
                        ================================================== -->

                        <?php if ($error !== ''): ?>

                            <div
                                class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4"
                                role="alert"
                            >

                                <div class="flex items-start gap-3">

                                    <div
                                        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                                    >
                                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-red-800">
                                            Sign in failed
                                        </p>

                                        <p class="mt-1 text-sm leading-5 text-red-700">
                                            <?= loginEscape($error) ?>
                                        </p>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                             SUCCESS MESSAGE
                        ================================================== -->

                        <?php if ($success !== ''): ?>

                            <div
                                class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4"
                                role="status"
                            >

                                <div class="flex items-start gap-3">

                                    <div
                                        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"
                                    >
                                        <i class="fa-solid fa-check text-sm"></i>
                                    </div>

                                    <p class="text-sm text-emerald-800">
                                        <?= loginEscape($success) ?>
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                             LOGIN FORM
                        ================================================== -->

                        <form
                            method="POST"
                            action=""
                            id="loginForm"
                            novalidate
                            class="space-y-5"
                        >

                            <!-- CSRF -->

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= loginEscape($_SESSION['csrf_login']) ?>"
                            >


                            <!-- Email -->

                            <div>

                                <label
                                    for="email"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Email address
                                </label>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                                    >
                                        <i class="fa-regular fa-envelope"></i>
                                    </div>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="<?= loginEscape($email) ?>"
                                        autocomplete="email"
                                        inputmode="email"
                                        spellcheck="false"
                                        placeholder="you@example.com"
                                        class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Password -->

                            <div>

                                <div class="flex items-center justify-between mb-2">

                                    <label
                                        for="password"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Password
                                    </label>

                                    <a
                                        href="forgot-password.php"
                                        class="text-xs font-semibold text-zakat-600 hover:text-zakat-700 transition"
                                    >
                                        Forgot password?
                                    </a>

                                </div>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                                    >
                                        <i class="fa-solid fa-lock"></i>
                                    </div>

                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        autocomplete="current-password"
                                        placeholder="Enter your password"
                                        class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                                        required
                                    >

                                    <button
                                        type="button"
                                        id="togglePassword"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-zakat-600 transition"
                                        aria-label="Show password"
                                    >
                                        <i
                                            id="passwordIcon"
                                            class="fa-regular fa-eye"
                                        ></i>
                                    </button>

                                </div>

                            </div>


                            <!-- Remember -->

                            <div class="flex items-center">

                                <label
                                    class="inline-flex items-center gap-2 cursor-pointer select-none"
                                >

                                    <input
                                        type="checkbox"
                                        name="remember"
                                        value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-zakat-600 focus:ring-zakat-500"
                                    >

                                    <span class="text-sm text-slate-500">
                                        Remember me
                                    </span>

                                </label>

                            </div>


                            <!-- Submit -->

                            <button
                                type="submit"
                                id="loginButton"
                                class="group w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-zakat-700 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-zakat-900/10 transition hover:bg-zakat-800 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-zakat-200 active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:translate-y-0"
                            >

                                <span id="loginButtonText">
                                    Sign in
                                </span>

                                <i
                                    id="loginButtonIcon"
                                    class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-0.5"
                                ></i>

                            </button>

                        </form>


                        <!-- =================================================
                             REGISTER
                        ================================================== -->

                        <div class="relative my-8">

                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>

                            <div class="relative flex justify-center">

                                <span
                                    class="bg-white px-4 text-xs text-slate-400"
                                >
                                    New to ZakatPro?
                                </span>

                            </div>

                        </div>

                        <a
                            href="register.php"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-zakat-300 hover:bg-zakat-50 hover:text-zakat-700 focus:outline-none focus:ring-4 focus:ring-zakat-100"
                        >

                            <i class="fa-solid fa-user-plus text-xs"></i>

                            Create an account

                        </a>


                        <!-- =================================================
                             SECURITY NOTE
                        ================================================== -->

                        <div class="mt-7 flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3.5">

                            <i
                                class="fa-solid fa-shield-halved mt-0.5 text-zakat-600"
                            ></i>

                            <p class="text-xs leading-5 text-slate-500">
                                Your account information is protected with
                                secure authentication. Never share your
                                password with anyone.
                            </p>

                        </div>


                        <!-- Footer -->

                        <div class="mt-8 text-center">

                            <p class="text-xs text-slate-400">
                                By continuing, you agree to use ZakatPro
                                responsibly and securely.
                            </p>

                        </div>

                    </div>

                </section>

            </div>

        </div>

    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <script>

        /*
        |--------------------------------------------------------------------------
        | PASSWORD VISIBILITY
        |--------------------------------------------------------------------------
        */

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (togglePassword && passwordInput && passwordIcon) {

            togglePassword.addEventListener('click', function () {

                const isPassword =
                    passwordInput.getAttribute('type') === 'password';

                if (isPassword) {

                    passwordInput.setAttribute('type', 'text');

                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');

                    togglePassword.setAttribute(
                        'aria-label',
                        'Hide password'
                    );

                } else {

                    passwordInput.setAttribute('type', 'password');

                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');

                    togglePassword.setAttribute(
                        'aria-label',
                        'Show password'
                    );
                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN BUTTON LOADING STATE
        |--------------------------------------------------------------------------
        */

        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const loginButtonText = document.getElementById('loginButtonText');
        const loginButtonIcon = document.getElementById('loginButtonIcon');

        if (loginForm) {

            loginForm.addEventListener('submit', function (event) {

                const email = document.getElementById('email');
                const password = document.getElementById('password');

                /*
                | Client-side validation only improves UX.
                | Server-side validation remains authoritative.
                */

                if (
                    !email.value.trim() ||
                    !password.value
                ) {
                    return;
                }

                if (loginButton) {
                    loginButton.disabled = true;
                }

                if (loginButtonText) {
                    loginButtonText.textContent = 'Signing in...';
                }

                if (loginButtonIcon) {

                    loginButtonIcon.classList.remove(
                        'fa-arrow-right'
                    );

                    loginButtonIcon.classList.add(
                        'fa-spinner',
                        'fa-spin'
                    );
                }

            });

        }

    </script>

</body>

</html>