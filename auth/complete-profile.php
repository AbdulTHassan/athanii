<?php
/** ZakatPro mandatory onboarding profile. */
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$userId = (int)$_SESSION['user_id'];
if (empty($_SESSION['csrf_profile'])) $_SESSION['csrf_profile'] = bin2hex(random_bytes(32));
$errors = [];
$stmt = $pdo->prepare('SELECT id, role_id, status, full_name, email, phone, address, city, state, country, language, profile_image, profile_completed_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['status'] !== 'active' || (int)$user['role_id'] !== 2) {
    unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_language']);
    header('Location: login.php'); exit;
}
if (!empty($user['profile_completed_at'])) { header('Location: ../user/dashboard.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!is_string($csrf) || !hash_equals($_SESSION['csrf_profile'], $csrf)) {
        $errors[] = 'Invalid security token. Please refresh the page.';
    }
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $city = trim((string)($_POST['city'] ?? ''));
    $state = trim((string)($_POST['state'] ?? ''));
    $country = trim((string)($_POST['country'] ?? 'Nigeria'));
    $language = (string)($_POST['language'] ?? 'en');
    if (!preg_match('/^[0-9+() .-]{7,30}$/', $phone)) $errors[] = 'Enter a valid phone number (7–30 characters).';
    if ($address === '' || mb_strlen($address) > 1000) $errors[] = 'Enter a valid address.';
    if ($city === '' || mb_strlen($city) > 100) $errors[] = 'Enter a valid city.';
    if ($state === '' || mb_strlen($state) > 100) $errors[] = 'Enter a valid state or region.';
    if (!in_array($country, ['Nigeria', 'Ghana', 'United Kingdom', 'United States', 'Other'], true)) $errors[] = 'Select a valid country.';
    if (!in_array($language, ['en', 'ha', 'ar'], true)) $errors[] = 'Select a valid language.';
    $profileImage = $user['profile_image'];
    $uploadedPath = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_image'];
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024 || $file['size'] < 1 || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'Profile image upload failed or exceeds 2MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $imageInfo = @getimagesize($file['tmp_name']);
            if (!isset($allowed[$mime]) || !$imageInfo || $imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
                $errors[] = 'Only valid JPG, PNG, or WEBP images are allowed (maximum 5000 pixels per side).';
            }
        }
    }
    if (!$errors) {
        if (isset($file, $mime) && isset($allowed[$mime])) {
            $uploadDir = __DIR__ . '/../assets/images/profiles/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                $errors[] = 'Cannot create profile image folder.';
            } else {
                $profileImage = 'profile_' . $userId . '_' . bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
                $uploadedPath = $uploadDir . $profileImage;
                if (!move_uploaded_file($file['tmp_name'], $uploadedPath)) {
                    $errors[] = 'Could not save the uploaded image.';
                }
            }
        }
        if (!$errors) {
            try {
                $update = $pdo->prepare('UPDATE users SET phone = ?, address = ?, city = ?, state = ?, country = ?, language = ?, profile_image = ?, profile_completed_at = NOW(), updated_at = NOW() WHERE id = ? AND role_id = 2');
                $update->execute([$phone, $address, $city, $state, $country, $language, $profileImage, $userId]);
                $_SESSION['user_language'] = $language;
                unset($_SESSION['csrf_profile']);
                header('Location: ../user/dashboard.php?profile=completed'); exit;
            } catch (PDOException $e) {
                error_log('ZakatPro profile update: ' . $e->getMessage());
                if ($uploadedPath && is_file($uploadedPath)) unlink($uploadedPath);
                $errors[] = 'Unable to update your profile. Please try again.';
            }
        }
    }
    $user['phone'] = $phone;
    $user['address'] = $address;
    $user['city'] = $city;
    $user['state'] = $state;
    $user['country'] = $country;
    $user['language'] = $language;
}
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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