<?php
/**
 * ZakatPro - Complete Profile
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

/*
|--------------------------------------------------------------------------
| Require Login
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_profile'])) {
    $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
}

$errors = [];

/*
|--------------------------------------------------------------------------
| Get User
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        address,
        city,
        state,
        country,
        language,
        profile_image
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Handle Form Submission
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CSRF Validation
    |--------------------------------------------------------------------------
    */
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_profile'],
            $_POST['csrf_token']
        )
    ) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    }

    /*
    |--------------------------------------------------------------------------
    | Get Form Values
    |--------------------------------------------------------------------------
    */
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $country = trim($_POST['country'] ?? 'Nigeria');
    $language = $_POST['language'] ?? 'en';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    if ($phone === '') {
        $errors[] = 'Please enter your phone number.';
    }

    if ($address === '') {
        $errors[] = 'Please enter your address.';
    }

    if ($city === '') {
        $errors[] = 'Please enter your city.';
    }

    if ($state === '') {
        $errors[] = 'Please enter your state or region.';
    }

    if ($country === '') {
        $errors[] = 'Please select your country.';
    }

    $allowedLanguages = ['en', 'ha', 'ar'];

    if (!in_array($language, $allowedLanguages, true)) {
        $errors[] = 'Invalid language selected.';
    }

    /*
    |--------------------------------------------------------------------------
    | Profile Image
    |--------------------------------------------------------------------------
    */
    $profileImage = $user['profile_image'];

    if (
        isset($_FILES['profile_image']) &&
        $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'There was a problem uploading your profile image.';
        } else {

            $file = $_FILES['profile_image'];

            /*
            | Maximum 2MB
            */
            if ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Profile image must not exceed 2MB.';
            }

            /*
            | Detect MIME type
            */
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file(
                $finfo,
                $file['tmp_name']
            );
            finfo_close($finfo);

            if (!isset($allowedMimeTypes[$mimeType])) {
                $errors[] = 'Only JPG, PNG, and WEBP images are allowed.';
            }

            /*
            | Save Image
            */
            if (empty($errors)) {

                $extension = $allowedMimeTypes[$mimeType];

                $uploadDirectory =
                    __DIR__ . '/../assets/images/profiles/';

                if (!is_dir($uploadDirectory)) {
                    mkdir(
                        $uploadDirectory,
                        0755,
                        true
                    );
                }

                $newFileName =
                    'profile_' .
                    $userId .
                    '_' .
                    bin2hex(random_bytes(8)) .
                    '.' .
                    $extension;

                $destination =
                    $uploadDirectory .
                    $newFileName;

                if (
                    move_uploaded_file(
                        $file['tmp_name'],
                        $destination
                    )
                ) {

                    /*
                    | Delete old profile image
                    */
                    if (!empty($user['profile_image'])) {

                        $oldImage =
                            $uploadDirectory .
                            basename($user['profile_image']);

                        if (is_file($oldImage)) {
                            unlink($oldImage);
                        }
                    }

                    $profileImage = $newFileName;

                } else {

                    $errors[] =
                        'Unable to save the uploaded profile image.';
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Database
    |--------------------------------------------------------------------------
    */
    if (empty($errors)) {

        try {

            $update = $pdo->prepare("
                UPDATE users
                SET
                    phone = ?,
                    address = ?,
                    city = ?,
                    state = ?,
                    country = ?,
                    language = ?,
                    profile_image = ?,
                    profile_completed_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            $update->execute([
                $phone,
                $address,
                $city,
                $state,
                $country,
                $language,
                $profileImage,
                $userId
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Session Language
            |--------------------------------------------------------------------------
            */
            $_SESSION['user_language'] = $language;

            /*
            |--------------------------------------------------------------------------
            | Redirect To Dashboard
            |--------------------------------------------------------------------------
            */
            header(
                'Location: ../user/dashboard.php?profile=completed'
            );
            exit;

        } catch (PDOException $e) {

            $errors[] =
                'Unable to update your profile. Please try again.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Keep Submitted Values
    |--------------------------------------------------------------------------
    */
    $user['phone'] = $phone;
    $user['address'] = $address;
    $user['city'] = $city;
    $user['state'] = $state;
    $user['country'] = $country;
    $user['language'] = $language;
    $user['profile_image'] = $profileImage;
}

/*
|--------------------------------------------------------------------------
| Escape Helper
|--------------------------------------------------------------------------
*/
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
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

    <title>Complete Profile | ZakatPro</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen bg-[#FAF9F4] text-slate-800">

<main class="min-h-screen flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-3xl">

        <!-- Main Card -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            <!-- Header -->
            <div class="px-6 sm:px-8 pt-7 pb-5 text-center border-b border-slate-100">

                <!-- ZakatPro Logo -->
                <div class="mx-auto mb-3 w-12 h-12 rounded-xl bg-emerald-700 flex items-center justify-center">

                    <svg
                        class="w-7 h-7 text-white"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6M8 9h.01M12 9h.01M16 9h.01"
                        />

                    </svg>

                </div>

                <h1 class="text-xl font-bold text-slate-900">
                    Complete Your Profile
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Add your personal information to complete your ZakatPro account.
                </p>

            </div>

            <!-- Progress -->
            <div class="px-6 sm:px-8 pt-5">

                <div class="flex items-center justify-center gap-3 text-xs">

                    <div class="flex items-center gap-2 text-emerald-700 font-medium">

                        <span class="w-7 h-7 rounded-full bg-emerald-700 text-white flex items-center justify-center">
                            ✓
                        </span>

                        Account

                    </div>

                    <div class="w-10 sm:w-14 h-px bg-emerald-200"></div>

                    <div class="flex items-center gap-2 text-emerald-700 font-medium">

                        <span class="w-7 h-7 rounded-full bg-emerald-700 text-white flex items-center justify-center">
                            2
                        </span>

                        Profile

                    </div>

                    <div class="w-10 sm:w-14 h-px bg-slate-200"></div>

                    <div class="flex items-center gap-2 text-slate-400">

                        <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                            3
                        </span>

                        Dashboard

                    </div>

                </div>

            </div>

            <!-- Form -->
            <form
                method="POST"
                enctype="multipart/form-data"
                class="px-6 sm:px-8 py-6"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_profile']) ?>"
                >

                <!-- Errors -->
                <?php if (!empty($errors)): ?>

                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">

                        <div class="flex gap-3">

                            <span class="text-red-600 font-bold">
                                !
                            </span>

                            <div>

                                <p class="font-semibold text-red-800 text-sm">
                                    Please correct the following:
                                </p>

                                <ul class="mt-1 text-sm text-red-700 list-disc ml-5 space-y-1">

                                    <?php foreach ($errors as $error): ?>

                                        <li><?= e($error) ?></li>

                                    <?php endforeach; ?>

                                </ul>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Full Name -->
                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Full Name
                        </label>

                        <input
                            type="text"
                            value="<?= e($user['full_name']) ?>"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed"
                        >

                        <p class="mt-1 text-xs text-slate-400">
                            Your registered name
                        </p>

                    </div>


                    <!-- Email -->
                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email Address
                        </label>

                        <input
                            type="email"
                            value="<?= e($user['email']) ?>"
                            disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed"
                        >

                        <p class="mt-1 text-xs text-slate-400">
                            Your registered email
                        </p>

                    </div>


                    <!-- Phone -->
                    <div>

                        <label
                            for="phone"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            Phone Number
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= e($user['phone']) ?>"
                            placeholder="e.g. 08012345678"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                    </div>


                    <!-- Country -->
                    <div>

                        <label
                            for="country"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            Country
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="country"
                            name="country"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                            <option
                                value="Nigeria"
                                <?= $user['country'] === 'Nigeria' ? 'selected' : '' ?>
                            >
                                Nigeria
                            </option>

                            <option
                                value="Ghana"
                                <?= $user['country'] === 'Ghana' ? 'selected' : '' ?>
                            >
                                Ghana
                            </option>

                            <option
                                value="United Kingdom"
                                <?= $user['country'] === 'United Kingdom' ? 'selected' : '' ?>
                            >
                                United Kingdom
                            </option>

                            <option
                                value="United States"
                                <?= $user['country'] === 'United States' ? 'selected' : '' ?>
                            >
                                United States
                            </option>

                            <option
                                value="Other"
                                <?= $user['country'] === 'Other' ? 'selected' : '' ?>
                            >
                                Other
                            </option>

                        </select>

                    </div>


                    <!-- Address -->
                    <div class="md:col-span-2">

                        <label
                            for="address"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            Residential / Contact Address
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="<?= e($user['address']) ?>"
                            placeholder="Enter your full address"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                    </div>


                    <!-- City -->
                    <div>

                        <label
                            for="city"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            City
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="<?= e($user['city']) ?>"
                            placeholder="e.g. Kano"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                    </div>


                    <!-- State -->
                    <div>

                        <label
                            for="state"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            State / Region
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="state"
                            name="state"
                            value="<?= e($user['state']) ?>"
                            placeholder="e.g. Kano State"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                    </div>


                    <!-- Language -->
                    <div>

                        <label
                            for="language"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            Preferred Language
                        </label>

                        <select
                            id="language"
                            name="language"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >

                            <option
                                value="en"
                                <?= $user['language'] === 'en' ? 'selected' : '' ?>
                            >
                                English
                            </option>

                            <option
                                value="ha"
                                <?= $user['language'] === 'ha' ? 'selected' : '' ?>
                            >
                                Hausa
                            </option>

                            <option
                                value="ar"
                                <?= $user['language'] === 'ar' ? 'selected' : '' ?>
                            >
                                Arabic
                            </option>

                        </select>

                    </div>


                    <!-- Profile Image -->
                    <div>

                        <label
                            for="profile_image"
                            class="block text-sm font-medium text-slate-700 mb-1.5"
                        >
                            Profile Image
                            <span class="text-slate-400 font-normal">
                                (Optional)
                            </span>
                        </label>

                        <input
                            type="file"
                            id="profile_image"
                            name="profile_image"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:border-0 file:rounded-lg file:bg-emerald-50 file:px-3 file:py-1.5 file:text-emerald-700 file:font-medium"
                        >

                        <p class="mt-1 text-xs text-slate-400">
                            JPG, PNG or WEBP · Maximum 2MB
                        </p>

                    </div>

                </div>


                <!-- Bottom Actions -->
                <div class="mt-7 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">

                    <a
                        href="../user/dashboard.php"
                        class="text-sm text-slate-500 hover:text-slate-800 transition"
                    >
                        Skip for now
                    </a>

                    <button
                        type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-200 transition"
                    >

                        Complete Profile

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12h14M13 6l6 6-6 6"
                            />

                        </svg>

                    </button>

                </div>

            </form>

        </div>


        <!-- Footer -->
        <p class="text-center text-xs text-slate-400 mt-4">
            © <?= date('Y') ?> ZakatPro. All rights reserved.
        </p>

    </div>

</main>

</body>

</html>