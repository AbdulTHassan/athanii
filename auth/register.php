<?php
/** ZakatPro public registration: creates normal users only. */
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['csrf_register'])) $_SESSION['csrf_register'] = bin2hex(random_bytes(32));
$error = '';
$fullName = '';
$email = '';
function registerEscape($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
if (!empty($_SESSION['user_id'])) {
    $q = $pdo->prepare('SELECT id, role_id, status, profile_completed_at FROM users WHERE id = ? LIMIT 1');
    $q->execute([(int)$_SESSION['user_id']]);
    $account = $q->fetch();
    if ($account && $account['status'] === 'active') {
        $role = (int)$account['role_id'];
        if (in_array($role, [1, 7], true)) { header('Location: ../admin/dashboard.php'); exit; }
        if ($role === 2) {
            header('Location: ' . (empty($account['profile_completed_at']) ? 'complete-profile.php' : '../user/dashboard.php'));
            exit;
        }
    }
    unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_language']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!is_string($csrf) || !hash_equals($_SESSION['csrf_register'], $csrf)) {
        $error = 'Your session has expired. Please refresh the page and try again.';
    } else {
        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 150) {
            $error = 'Full name must contain between 2 and 150 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $error = 'Please enter a valid email address.';
        } elseif (!is_string($password) || strlen($password) < 8) {
            $error = 'Password must contain at least 8 characters.';
        } elseif (!is_string($confirm) || $password !== $confirm) {
            $error = 'The passwords do not match.';
        } elseif (empty($_POST['terms'])) {
            $error = 'Please accept the terms and conditions.';
        } else {
            try {
                $q = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $q->execute([$email]);
                if ($q->fetch()) {
                    $error = 'An account with this email address already exists.';
                } else {
                    $q = $pdo->prepare("INSERT INTO users (role_id, full_name, email, password, language, status) VALUES (2, ?, ?, ?, 'en', 'active')");
                    $q->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    $id = (int)$pdo->lastInsertId();
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = 2;
                    $_SESSION['user_name'] = $fullName;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_language'] = 'en';
                    header('Location: complete-profile.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log('ZakatPro registration: ' . $e->getMessage());
                $error = 'We could not create your account right now. Please try again later.';
            }
        }
    }
}
$pageTitle = 'Create Account';
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
        content="Create your ZakatPro account."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <title>
        <?= registerEscape($pageTitle) ?> | ZakatPro
    </title>


    <!-- =========================================================
         TAILWIND
    ========================================================== -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- =========================================================
         FONT AWESOME
    ========================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >


    <!-- =========================================================
         TAILWIND CONFIG
    ========================================================== -->

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

                        'register':
                            '0 25px 70px rgba(20, 58, 48, 0.10)',

                        'button':
                            '0 10px 25px rgba(29, 87, 70, 0.18)'

                    }

                }

            }

        };

    </script>


    <!-- =========================================================
         CUSTOM CSS
    ========================================================== -->

    <style>

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #FAF9F4;
        }

        .page-pattern {

            background-image:
                radial-gradient(
                    rgba(49, 130, 103, 0.07) 1px,
                    transparent 1px
                );

            background-size: 26px 26px;

        }


        /*
        |--------------------------------------------------------------------------
        | Card animation
        |--------------------------------------------------------------------------
        */

        @keyframes registerAppear {

            from {

                opacity: 0;

                transform:
                    translateY(15px)
                    scale(0.985);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);

            }

        }

        .register-card {

            animation:
                registerAppear
                0.5s
                ease-out;

        }


        /*
        |--------------------------------------------------------------------------
        | Autofill
        |--------------------------------------------------------------------------
        */

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {

            -webkit-text-fill-color: #1f2937;

            -webkit-box-shadow:
                0 0 0px 1000px #ffffff inset;

            transition:
                background-color
                5000s
                ease-in-out
                0s;

        }


        input::-ms-reveal,
        input::-ms-clear {

            display: none;

        }

    </style>

</head>


<body class="min-h-screen">


    <!-- =========================================================
         MAIN PAGE
    ========================================================== -->

    <main
        class="page-pattern min-h-screen flex items-center justify-center px-4 py-6 sm:px-6"
    >

        <div class="w-full max-w-md">


            <!-- =================================================
                 REGISTER CARD
            ================================================== -->

            <section
                class="register-card rounded-3xl border border-slate-200 bg-white p-6 shadow-register sm:p-8"
            >


                <!-- =================================================
                     LOGO
                ================================================== -->

                <div class="text-center">


                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zakat-700 text-white shadow-button"
                    >

                        <i
                            class="fa-solid fa-mosque text-xl"
                        ></i>

                    </div>


                    <h1
                        class="mt-3 text-xl font-bold tracking-tight text-zakat-900"
                    >
                        ZakatPro
                    </h1>

                    <p
                        class="mt-1 text-xs text-slate-500"
                    >
                        Zakat Management System
                    </p>

                </div>


                <!-- =================================================
                     HEADER
                ================================================== -->

                <div class="mt-6 mb-6 text-center">

                    <h2
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        Create your account
                    </h2>

                    <p
                        class="mt-2 text-sm leading-5 text-slate-500"
                    >
                        Start managing your Zakat activities securely.
                    </p>

                </div>


                <!-- =================================================
                     ERROR
                ================================================== -->

                <?php if ($error !== ''): ?>

                    <div
                        class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-3.5"
                        role="alert"
                    >

                        <div class="flex items-start gap-3">

                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                            >

                                <i
                                    class="fa-solid fa-circle-exclamation text-sm"
                                ></i>

                            </div>


                            <div>

                                <p
                                    class="text-sm font-semibold text-red-800"
                                >
                                    Registration failed
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-red-700"
                                >
                                    <?= registerEscape($error) ?>
                                </p>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    method="POST"
                    action=""
                    id="registerForm"
                    class="space-y-4"
                    novalidate
                >


                    <!-- CSRF -->

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= registerEscape($_SESSION['csrf_register']) ?>"
                    >


                    <!-- =================================================
                         FULL NAME
                    ================================================== -->

                    <div>

                        <label
                            for="full_name"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >
                            Full name
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >

                                <i
                                    class="fa-regular fa-user"
                                ></i>

                            </div>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?= registerEscape($fullName) ?>"
                                placeholder="Enter your full name"
                                autocomplete="name"
                                maxlength="150"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                            >

                        </div>

                    </div>


                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <div>

                        <label
                            for="email"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >
                            Email address
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >

                                <i
                                    class="fa-regular fa-envelope"
                                ></i>

                            </div>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?= registerEscape($email) ?>"
                                placeholder="you@example.com"
                                autocomplete="email"
                                inputmode="email"
                                maxlength="150"
                                spellcheck="false"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                            >

                        </div>

                    </div>


                    <!-- =================================================
                         PASSWORD
                    ================================================== -->

                    <div>

                        <label
                            for="password"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >
                            Password
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >

                                <i
                                    class="fa-solid fa-lock"
                                ></i>

                            </div>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Create a password"
                                autocomplete="new-password"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                            >

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-zakat-600"
                                aria-label="Show password"
                            >

                                <i
                                    id="passwordIcon"
                                    class="fa-regular fa-eye"
                                ></i>

                            </button>

                        </div>


                        <!-- Password requirement -->

                        <p
                            class="mt-1.5 text-xs text-slate-400"
                        >
                            Use at least 8 characters.
                        </p>

                    </div>


                    <!-- =================================================
                         CONFIRM PASSWORD
                    ================================================== -->

                    <div>

                        <label
                            for="confirm_password"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >
                            Confirm password
                        </label>

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"
                            >

                                <i
                                    class="fa-solid fa-lock"
                                ></i>

                            </div>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Repeat your password"
                                autocomplete="new-password"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-zakat-500 focus:ring-4 focus:ring-zakat-100"
                            >

                            <button
                                type="button"
                                id="toggleConfirmPassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-zakat-600"
                                aria-label="Show password"
                            >

                                <i
                                    id="confirmPasswordIcon"
                                    class="fa-regular fa-eye"
                                ></i>

                            </button>

                        </div>

                    </div>


                    <!-- =================================================
                         TERMS
                    ================================================== -->

                    <div class="pt-1">

                        <label
                            class="flex cursor-pointer items-start gap-3"
                        >

                            <input
                                type="checkbox"
                                id="terms"
                                name="terms"
                                value="1"
                                required
                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-zakat-600 focus:ring-zakat-500"
                            >

                            <span
                                class="text-xs leading-5 text-slate-500"
                            >
                                I agree to the ZakatPro
                                <a
                                    href="#"
                                    class="font-semibold text-zakat-600 hover:text-zakat-800"
                                >
                                    Terms of Service
                                </a>
                                and
                                <a
                                    href="#"
                                    class="font-semibold text-zakat-600 hover:text-zakat-800"
                                >
                                    Privacy Policy
                                </a>.
                            </span>

                        </label>

                    </div>


                    <!-- =================================================
                         CREATE ACCOUNT BUTTON
                    ================================================== -->

                    <button
                        type="submit"
                        id="registerButton"
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-zakat-700 px-5 py-3.5 text-sm font-semibold text-white shadow-button transition hover:-translate-y-0.5 hover:bg-zakat-800 focus:outline-none focus:ring-4 focus:ring-zakat-200 active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-70"
                    >

                        <span id="registerButtonText">
                            Create account
                        </span>

                        <i
                            id="registerButtonIcon"
                            class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"
                        ></i>

                    </button>

                </form>


                <!-- =================================================
                     LOGIN LINK
                ================================================== -->

                <div
                    class="mt-6 text-center"
                >

                    <p
                        class="text-sm text-slate-500"
                    >

                        Already have an account?

                        <a
                            href="login.php"
                            class="font-semibold text-zakat-600 transition hover:text-zakat-800"
                        >
                            Sign in
                        </a>

                    </p>

                </div>


                <!-- =================================================
                     SECURITY MESSAGE
                ================================================== -->

                <div
                    class="mt-5 flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3"
                >

                    <i
                        class="fa-solid fa-shield-halved mt-0.5 text-zakat-600"
                    ></i>

                    <p
                        class="text-xs leading-5 text-slate-500"
                    >
                        Your password is securely encrypted before it
                        is stored. Keep your login details private.
                    </p>

                </div>

            </section>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div
                class="mt-5 text-center"
            >

                <p
                    class="text-xs text-slate-400"
                >
                    © <?= date('Y') ?> ZakatPro.
                    All rights reserved.
                </p>

            </div>

        </div>

    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <script>

        /*
        |--------------------------------------------------------------------------
        | PASSWORD TOGGLE FUNCTION
        |--------------------------------------------------------------------------
        */

        function setupPasswordToggle(
            buttonId,
            inputId,
            iconId
        ) {

            const button =
                document.getElementById(buttonId);

            const input =
                document.getElementById(inputId);

            const icon =
                document.getElementById(iconId);


            if (!button || !input || !icon) {
                return;
            }


            button.addEventListener(
                'click',
                function () {

                    const hidden =
                        input.type === 'password';


                    if (hidden) {

                        input.type = 'text';

                        icon.classList.remove(
                            'fa-eye'
                        );

                        icon.classList.add(
                            'fa-eye-slash'
                        );

                        button.setAttribute(
                            'aria-label',
                            'Hide password'
                        );

                    } else {

                        input.type = 'password';

                        icon.classList.remove(
                            'fa-eye-slash'
                        );

                        icon.classList.add(
                            'fa-eye'
                        );

                        button.setAttribute(
                            'aria-label',
                            'Show password'
                        );

                    }

                }
            );

        }


        setupPasswordToggle(
            'togglePassword',
            'password',
            'passwordIcon'
        );


        setupPasswordToggle(
            'toggleConfirmPassword',
            'confirm_password',
            'confirmPasswordIcon'
        );


        /*
        |--------------------------------------------------------------------------
        | PASSWORD MATCH INDICATOR
        |--------------------------------------------------------------------------
        */

        const password =
            document.getElementById('password');

        const confirmPassword =
            document.getElementById('confirm_password');


        if (password && confirmPassword) {

            confirmPassword.addEventListener(
                'input',
                function () {

                    if (
                        confirmPassword.value !== '' &&
                        password.value !== confirmPassword.value
                    ) {

                        confirmPassword.classList.remove(
                            'border-slate-200',
                            'focus:border-zakat-500',
                            'focus:ring-zakat-100'
                        );

                        confirmPassword.classList.add(
                            'border-red-300',
                            'focus:border-red-500',
                            'focus:ring-red-100'
                        );

                    } else {

                        confirmPassword.classList.remove(
                            'border-red-300',
                            'focus:border-red-500',
                            'focus:ring-red-100'
                        );

                        confirmPassword.classList.add(
                            'border-slate-200',
                            'focus:border-zakat-500',
                            'focus:ring-zakat-100'
                        );

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FORM SUBMIT LOADING STATE
        |--------------------------------------------------------------------------
        */

        const registerForm =
            document.getElementById('registerForm');

        const registerButton =
            document.getElementById('registerButton');

        const registerButtonText =
            document.getElementById('registerButtonText');

        const registerButtonIcon =
            document.getElementById('registerButtonIcon');


        if (registerForm) {

            registerForm.addEventListener(
                'submit',
                function () {

                    if (
                        !registerForm.checkValidity()
                    ) {

                        return;

                    }


                    if (registerButton) {

                        registerButton.disabled = true;

                    }


                    if (registerButtonText) {

                        registerButtonText.textContent =
                            'Creating account...';

                    }


                    if (registerButtonIcon) {

                        registerButtonIcon.classList.remove(
                            'fa-arrow-right'
                        );

                        registerButtonIcon.classList.add(
                            'fa-spinner',
                            'fa-spin'
                        );

                    }

                }
            );

        }

    </script>

</body>

</html>