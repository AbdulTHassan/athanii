<?php
/**
 * ZakatPro - User Authentication & Dashboard Data
 *
 * File: user/includes/auth-check.php
 *
 * Only active normal users (role_id = 2) can access
 * the user area.
 */

require_once __DIR__ . '/../../config/config.php';

/*
|--------------------------------------------------------------------------
| 1. REQUIRE LOGIN
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['user_id']) ||
    !isLoggedIn()
) {
    $_SESSION['redirect_after_login'] =
        $_SERVER['REQUEST_URI'] ?? null;

    redirect(SITE_URL . '/auth/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| 2. GET USER
|--------------------------------------------------------------------------
*/

$user = getUser($userId);

if (!$user) {
    logoutUser();

    redirect(SITE_URL . '/auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| 3. CHECK ACCOUNT STATUS
|--------------------------------------------------------------------------
*/

if (($user['status'] ?? '') !== 'active') {
    logoutUser();

    redirect(SITE_URL . '/auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| 4. ROLE AUTHORIZATION
|--------------------------------------------------------------------------
*/

$roleId = (int) ($user['role_id'] ?? 0);

if ($roleId !== 2) {

    if (in_array($roleId, [1, 7], true)) {
        redirect(SITE_URL . '/admin/dashboard.php');
        exit;
    }

    http_response_code(403);
    exit('Access denied.');
}

/*
|--------------------------------------------------------------------------
| 5. REQUIRE COMPLETED PROFILE
|--------------------------------------------------------------------------
|
| The registration process must be completed before
| accessing the user dashboard.
|
*/

$profileStmt = $pdo->prepare("
    SELECT
        phone,
        address,
        city,
        state,
        country,
        profile_completed_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$profileStmt->execute([$userId]);

$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    logoutUser();

    redirect(SITE_URL . '/auth/login.php');
    exit;
}

$profileComplete =
    !empty($profile['phone']) &&
    !empty($profile['address']) &&
    !empty($profile['city']) &&
    !empty($profile['state']) &&
    !empty($profile['country']) &&
    !empty($profile['profile_completed_at']);

if (!$profileComplete) {
    redirect(SITE_URL . '/auth/complete-profile.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| 6. UNREAD NOTIFICATIONS
|--------------------------------------------------------------------------
*/

$unread = getUnreadNotifications($userId);

/*
|--------------------------------------------------------------------------
| 7. ACTIVE CALCULATION
|--------------------------------------------------------------------------
|
| The current database supports:
| draft, calculated, saved, cancelled
|
| We do not use payment approval statuses here.
|
*/

$activeCalcStmt = $pdo->prepare("
    SELECT *
    FROM zakat_calculations
    WHERE user_id = ?
      AND status IN ('draft', 'calculated', 'saved')
    ORDER BY created_at DESC
    LIMIT 1
");

$activeCalcStmt->execute([$userId]);

$activeCalculation =
    $activeCalcStmt->fetch(PDO::FETCH_ASSOC) ?: null;

/*
|--------------------------------------------------------------------------
| 8. TOTAL ZAKAT DUE
|--------------------------------------------------------------------------
|
| Calculate outstanding zakat from saved calculations,
| subtracting payments that have been confirmed.
|
| Payment confirmation is separate from calculation
| status.
|
*/

$totalDueStmt = $pdo->prepare("
    SELECT COALESCE(
        SUM(
            GREATEST(
                c.zakat_due - COALESCE(p.confirmed_amount, 0),
                0
            )
        ),
        0
    )
    FROM zakat_calculations c

    LEFT JOIN (
        SELECT
            calculation_id,
            SUM(amount) AS confirmed_amount
        FROM zakat_payments
        WHERE status IN ('confirmed', 'approved')
        GROUP BY calculation_id
    ) p ON p.calculation_id = c.id

    WHERE c.user_id = ?
      AND c.status IN ('calculated', 'saved')
");

$totalDueStmt->execute([$userId]);

$totalZakatDue =
    (float) $totalDueStmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| 9. REPORT COUNT
|--------------------------------------------------------------------------
*/

$reportsCountStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM reports
    WHERE user_id = ?
      AND status = 'generated'
");

$reportsCountStmt->execute([$userId]);

$myReportsCount =
    (int) $reportsCountStmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| 10. VALID CERTIFICATE
|--------------------------------------------------------------------------
|
| A certificate must be valid and linked to a
| confirmed payment.
|
*/

$certStmt = $pdo->prepare("
    SELECT c.*
    FROM certificates c

    INNER JOIN zakat_payments p
        ON p.id = c.payment_id
       AND p.user_id = c.user_id

    WHERE c.user_id = ?
      AND c.status = 'valid'
      AND c.revoked = 0
      AND c.issued_at IS NOT NULL
      AND p.status IN ('confirmed', 'approved')

    ORDER BY c.issued_at DESC
    LIMIT 1
");

$certStmt->execute([$userId]);

$myCertificate =
    $certStmt->fetch(PDO::FETCH_ASSOC) ?: null;

/*
|--------------------------------------------------------------------------
| 11. CALCULATION COUNTS
|--------------------------------------------------------------------------
|
| Keep compatibility with the existing dashboard.
|
*/

$counts = getCalculationCounts($userId);