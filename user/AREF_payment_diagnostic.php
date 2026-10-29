
<?php
/**
 * KAZWAB - Bank Payment Submission
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth-check.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function payH($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($userId <= 0) {
    http_response_code(403);
    exit('Please log in.');
}

if (!$id) {
    header('Location: calculations.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM zakat_calculations
    WHERE id = ? AND user_id = ?
    LIMIT 1
");

$stmt->execute([$id, $userId]);
$calculation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$calculation) {
    http_response_code(404);
    exit('Calculation not found.');
}

$amount = (float)$calculation['zakat_due'];
$status = strtolower(trim((string)$calculation['status']));
$allowedPaymentStatuses = ['calculated', 'saved', 'draft', 'submitted', 'under_review', 'approved'];

$notes = json_decode(
    (string)($calculation['notes'] ?? ''),
    true
);

$category = is_array($notes)
    ? strtolower(trim((string)($notes['category'] ?? '')))
    : '';

$physicalCategories = [
    'agriculture',
    'goats',
    'cows',
    'camels',
    'livestock',
    'zakat_fitr'
];

// Keep the payment gate strict; report which check failed instead of a generic error.
// This is intentionally NOT a bypass for physical Zakat, invalid status or zero due.
$paymentBlockReason = null;
if (in_array($category, $physicalCategories, true)) {
    $paymentBlockReason = 'Physical Zakat does not use bank payment.';
} elseif (!is_finite($amount) || $amount <= 0) {
    $paymentBlockReason = 'Zakat Due is zero or invalid. Please recalculate your Zakat.';
} elseif (!in_array($status, $allowedPaymentStatuses, true)) {
    $paymentBlockReason = 'Calculation status does not permit payment (' . $status . ').';
}

if ($paymentBlockReason !== null) {
    error_log(sprintf(
        'Payment eligibility rejected: calculation_id=%d, category=%s, status=%s, amount=%s, reason=%s',
        $id,
        $category,
        $status,
        (string)$amount,
        $paymentBlockReason
    ));
    http_response_code(403);
    exit(htmlspecialchars($paymentBlockReason, ENT_QUOTES, 'UTF-8'));
}

/*
 * IMPORTANT:
 * Enter VERIFIED KAZWAB bank details.
 */
// Read only Admin-approved payment details from System Settings.
$bankName = '';
$accountName = '';
$accountNumber = '';
$paymentInstructions = '';
$bankActive = false;
$settingsError = false;

try {
    $keys = ['bank_name', 'bank_account_name', 'bank_account_number',
             'bank_account_active', 'payment_instructions'];
    $placeholders = implode(',', array_fill(0, count($keys), '?'));
    $settingsStmt = $pdo->prepare(
        "SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ($placeholders)"
    );
    $settingsStmt->execute($keys);
    $paymentSettings = $settingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $bankName = trim((string)($paymentSettings['bank_name'] ?? ''));
    $accountName = trim((string)($paymentSettings['bank_account_name'] ?? ''));
    $accountNumber = trim((string)($paymentSettings['bank_account_number'] ?? ''));
    $paymentInstructions = trim((string)($paymentSettings['payment_instructions'] ?? ''));
    $bankActive = ($paymentSettings['bank_account_active'] ?? '0') === '1';
} catch (Throwable $e) {
    error_log('KAZWAB payment settings read: ' . $e->getMessage());
    $settingsError = true;
}

$bankReady = !$settingsError && $bankActive &&
    $bankName !== '' && $accountName !== '' &&
    preg_match('/^[0-9]{10}$/D', $accountNumber) === 1;

if (empty($_SESSION['payment_csrf'])) {
    $_SESSION['payment_csrf'] = bin2hex(random_bytes(32));
}

$error = '';

$stmt = $pdo->prepare("
    SELECT *
    FROM zakat_payments
    WHERE calculation_id = ?
      AND user_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$id, $userId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

/*
 * Payment submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['payment_csrf'], $token)) {

        $error = 'Invalid form session. Refresh the page.';

    } elseif (!$bankReady) {

        $error = 'Official bank details are not enabled or configured.';

    } elseif ($existing) {

        $error = 'Payment already submitted.';

    } else {

        $transactionRef = trim(
            (string)($_POST['payment_reference'] ?? '')
        );

        if (
            $transactionRef === '' ||
            strlen($transactionRef) > 120
        ) {

            $error = 'Enter a valid transaction reference.';

        } elseif (
            !isset($_FILES['payment_proof']) ||
            $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK
        ) {

            $error = 'Upload your payment proof.';

        } else {

            $file = $_FILES['payment_proof'];

            if (
                $file['size'] <= 0 ||
                $file['size'] > 5 * 1024 * 1024
            ) {

                $error = 'Maximum proof size is 5MB.';

            } elseif (!is_uploaded_file($file['tmp_name'])) {

                $error = 'Invalid uploaded file.';

            } else {

                $mime = (new finfo(FILEINFO_MIME_TYPE))
                    ->file($file['tmp_name']);

                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'application/pdf' => 'pdf'
                ];

                if (!isset($allowed[$mime])) {

                    $error = 'Only JPG, PNG or PDF is allowed.';

                } else {

                    $uploadDir = __DIR__ .
                        '/uploads/payment_proofs/';

                    if (
                        !is_dir($uploadDir) &&
                        !mkdir($uploadDir, 0700, true)
                    ) {

                        $error = 'Cannot create upload folder.';

                    } else {

                        /*
                         * Deny direct HTTP access.
                         */
                        file_put_contents(
                            $uploadDir . '.htaccess',
                            "Require all denied\nDeny from all\n"
                        );

                        $storedName =
                            bin2hex(random_bytes(20)) .
                            '.' . $allowed[$mime];

                        $path = $uploadDir . $storedName;

                        if (!move_uploaded_file(
                            $file['tmp_name'],
                            $path
                        )) {

                            $error = 'Could not save uploaded proof.';

                        } else {

                            try {

                                $pdo->beginTransaction();

                                /*
                                 * Lock calculation while
                                 * checking existing payments.
                                 */
                                $lock = $pdo->prepare("
                                    SELECT id
                                    FROM zakat_calculations
                                    WHERE id = ? AND user_id = ?
                                    FOR UPDATE
                                ");

                                $lock->execute([$id, $userId]);

                                $check = $pdo->prepare("
                                    SELECT id
                                    FROM zakat_payments
                                    WHERE calculation_id = ?
                                      AND user_id = ?
                                    LIMIT 1
                                ");

                                $check->execute([$id, $userId]);

                                if ($check->fetchColumn()) {
                                    throw new RuntimeException(
                                        'Payment already submitted.'
                                    );
                                }

                                $insert = $pdo->prepare("
                                    INSERT INTO zakat_payments
                                    (
                                        user_id,
                                        calculation_id,
                                        amount,
                                        payment_method,
                                        payment_reference,
                                        status
                                    )
                                    VALUES (?, ?, ?, ?, ?, ?)
                                ");

                                $insert->execute([
                                    $userId,
                                    $id,
                                    $amount,
                                    'Bank Transfer',
                                    $transactionRef,
                                    'pending'
                                ]);

                                $paymentId =
                                    (int)$pdo->lastInsertId();

                                $proof = $pdo->prepare("
                                    INSERT INTO zakat_payment_proofs
                                    (
                                        payment_id,
                                        stored_filename,
                                        original_filename,
                                        mime_type,
                                        file_size
                                    )
                                    VALUES (?, ?, ?, ?, ?)
                                ");

                                $proof->execute([
                                    $paymentId,
                                    $storedName,
                                    substr(
                                        basename($file['name']),
                                        0,
                                        200
                                    ),
                                    $mime,
                                    (int)$file['size']
                                ]);

                                // Make the monetary calculation visible to Admin review
                                // only after the bank payment proof is submitted.
                                $advance = $pdo->prepare("
                                    UPDATE zakat_calculations
                                    SET status = 'submitted'
                                    WHERE id = ? AND user_id = ?
                                      AND status IN ('calculated', 'saved', 'draft')
                                ");
                                $advance->execute([$id, $userId]);

                                $pdo->commit();

                                header(
                                    'Location: payment.php?id=' .
                                    $id . '&submitted=1',
                                    true,
                                    303
                                );
                                exit;

                            } catch (Throwable $e) {

                                if ($pdo->inTransaction()) {
                                    $pdo->rollBack();
                                }

                                @unlink($path);

                                error_log(
                                    'KAZWAB PAYMENT: ' .
                                    $e->getMessage()
                                );

                                $error =
                                    'Payment submission failed. ' .
                                    'Please check database setup.';
                            }
                        }
                    }
                }
            }
        }
    }
}

$pageTitle = 'Zakat Payment';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width,initial-scale=1">

    <title>Payment - KAZWAB</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 text-slate-800">

<?php require __DIR__ . '/_sidebar.php'; ?>

<div class="md:ml-64 min-h-screen flex flex-col">

    <?php require __DIR__ . '/_header.php'; ?>

    <main class="flex-1 p-4 md:p-8">

        <div class="max-w-3xl mx-auto">

            <div class="mb-6">
                <h1 class="text-3xl font-bold">
                    Zakat Donation
                </h1>

                <p class="text-slate-500 mt-2">
                    Bank transfer and payment verification
                </p>
            </div>

            <div class="bg-white rounded-2xl border
                        shadow-sm p-6">

                <div class="bg-emerald-50 rounded-xl
                            p-5 mb-6">

                    <p class="text-sm text-emerald-700">
                        Zakat Due
                    </p>

                    <h2 class="text-4xl font-bold
                               text-emerald-800 my-2">
                        ₦<?= number_format($amount, 2) ?>
                    </h2>

                    <p class="text-sm">
                        Reference:
                        <strong>
                            <?= payH($calculation['reference_no']) ?>
                        </strong>
                    </p>

                </div>

                <h3 class="text-xl font-bold mb-4">
                    Official KAZWAB Bank Account
                </h3>

                <?php if ($bankReady): ?>

                    <div class="border rounded-xl
                                bg-slate-50 p-5 mb-6">

                        <p class="text-sm text-slate-500">
                            Bank Name
                        </p>

                        <p class="font-bold mb-3">
                            <?= payH($bankName) ?>
                        </p>

                        <p class="text-sm text-slate-500">
                            Account Name
                        </p>

                        <p class="font-bold mb-3">
                            <?= payH($accountName) ?>
                        </p>

                        <p class="text-sm text-slate-500">
                            Account Number
                        </p>

                        <div class="flex items-center gap-3">

                            <p class="text-2xl font-bold
                                      text-emerald-700">
                                <?= payH($accountNumber) ?>
                            </p>

                            <button type="button"
                                    onclick="copyAccount()"
                                    class="bg-white border rounded-lg
                                           px-3 py-2 text-sm">
                                Copy
                            </button>

                        </div>

                        <?php if ($paymentInstructions !== ''): ?>
                            <p class="mt-4 text-sm text-slate-700 whitespace-pre-line"><?= payH($paymentInstructions) ?></p>
                        <?php endif; ?>
                    </div>

                <?php else: ?>

                    <div class="bg-amber-50
                                border border-amber-200
                                text-amber-800 rounded-xl
                                p-4 mb-6">

                        Official bank account is not yet enabled or fully configured by Admin.

                        Payment submission is disabled until Admin activates the account.

                    </div>

                <?php endif; ?>

                <?php if ($error): ?>

                    <div class="bg-red-50 text-red-700
                                rounded-xl p-4 mb-5">

                        <?= payH($error) ?>

                    </div>

                <?php endif; ?>

                <?php if ($existing): ?>

                    <div class="bg-blue-50 border
                                border-blue-200
                                rounded-xl p-5">

                        <h3 class="text-xl font-bold
                                   text-blue-800">
                            Payment Submitted
                        </h3>

                        <p class="mt-2">
                            Your donation has been submitted
                            for verification.
                        </p>

                        <p class="mt-3">
                            Payment Status:
                            <strong>
                                <?= payH($existing['status']) ?>
                            </strong>
                        </p>

                        <p class="mt-3 text-sm text-slate-600">
                            Certificate is not issued until
                            the payment is verified and
                            authorized by Admin.
                        </p>

                        <a href="zakat-status.php"
                           class="inline-block mt-5
                                  bg-emerald-700
                                  text-white rounded-xl
                                  px-5 py-3">

                            View Zakat Status

                        </a>

                    </div>

                <?php else: ?>

                    <form method="POST"
                          enctype="multipart/form-data"
                          class="space-y-5">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= payH(
                                   $_SESSION['payment_csrf']
                               ) ?>">

                        <div>
                            <label class="block font-semibold mb-2">
                                Bank Transaction Reference
                            </label>

                            <input type="text"
                                   name="payment_reference"
                                   maxlength="120"
                                   required
                                   placeholder="Enter bank transfer reference"
                                   class="w-full border
                                          rounded-xl px-4 py-3"
                                   <?= !$bankReady ? 'disabled' : '' ?>>
                        </div>

                        <div>
                            <label class="block font-semibold mb-2">
                                Upload Proof of Payment
                            </label>

                            <input type="file"
                                   name="payment_proof"
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   required
                                   class="w-full border
                                          rounded-xl p-3"
                                   <?= !$bankReady ? 'disabled' : '' ?>>

                            <p class="text-xs text-slate-500 mt-2">
                                JPG, PNG or PDF. Maximum 5MB.
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full bg-emerald-700
                                       hover:bg-emerald-800
                                       text-white font-bold
                                       rounded-xl py-3
                                       disabled:opacity-50"
                                <?= !$bankReady ? 'disabled' : '' ?>>

                            Submit Proof for Verification

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

<script>
function copyAccount() {
    const account = <?= json_encode($accountNumber) ?>;

    if (navigator.clipboard && account) {
        navigator.clipboard.writeText(account)
            .then(() => alert('Account number copied.'));
    }
}
</script>

</body>
</html>
