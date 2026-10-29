<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth-check.php';

$pageTitle = 'Zakat Certificate';
$userId = (int)($_SESSION['user_id'] ?? ($user['id'] ?? 0));
function certEscape($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function certColumns($pdo, $table) {
    try {
        $q = $pdo->query("SHOW COLUMNS FROM `" . $table . "`");
        return array_column($q->fetchAll(PDO::FETCH_ASSOC), 'Field');
    } catch (Throwable $e) { return []; }
}
function certMoney($amount, $currency = 'NGN') {
    return ($currency === 'NGN' ? '₦' : $currency . ' ') . number_format((float)$amount, 2);
}
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$calc = null;
$calculations = [];
$verifiedPayments = [];
$verifiedTotal = 0;
$paymentCols = certColumns($pdo, 'zakat_payments');
$validStatuses = ['verified', 'confirmed', 'completed', 'paid'];

try {
    if ($id && $userId) {
        $q = $pdo->prepare('SELECT * FROM zakat_calculations WHERE id = ? AND user_id = ? LIMIT 1');
        $q->execute([$id, $userId]);
        $calc = $q->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($calc && in_array('calculation_id', $paymentCols, true)
            && in_array('user_id', $paymentCols, true)
            && in_array('status', $paymentCols, true)) {
            $q = $pdo->prepare('SELECT * FROM zakat_payments WHERE calculation_id = ? AND user_id = ? ORDER BY id ASC');
            $q->execute([$id, $userId]);
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $payment) {
                if (in_array(strtolower(trim((string)($payment['status'] ?? ''))), $validStatuses, true)) {
                    $verifiedPayments[] = $payment;
                    $verifiedTotal += (float)($payment['amount'] ?? 0);
                }
            }
        }
    } else {
        $q = $pdo->prepare('SELECT id, reference_no, zakat_due, status FROM zakat_calculations WHERE user_id = ? ORDER BY id DESC LIMIT 60');
        $q->execute([$userId]);
        $calculations = $q->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $dbError = true;
}
// Optional branding values; the certificate still works before settings are configured.
$branding = [];
try {
    $stmtBrand = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('organization_name','organization_short_name','certificate_prefix','certificate_orientation','organization_logo','certificate_frame','official_stamp','authorized_signature','signatory_name','signatory_position')");
    foreach ($stmtBrand->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $branding[(string)$r['setting_key']] = (string)$r['setting_value'];
    }
} catch (Throwable $ignored) {}
function certAsset($value) {
    $value = trim((string)$value);
    if ($value === '' || str_contains($value, '..') || preg_match('~^(?:https?:)?//~i', $value)) return '';
    $value = ltrim(str_replace('\\', '/', $value), '/');
    // Only serve assets under the site's dedicated upload folder.
    if (!preg_match('~^uploads/organization/[a-zA-Z0-9_./-]+\.(png|jpe?g|webp)$~i', $value)) return '';
    $root = realpath(__DIR__ . '/..');
    $full = realpath(__DIR__ . '/../' . $value);
    $uploadRoot = realpath(__DIR__ . '/../uploads/organization');
    if (!$root || !$full || !$uploadRoot || !str_starts_with($full, $uploadRoot . DIRECTORY_SEPARATOR) || !is_file($full)) return '';
    return '../' . $value;
}
$orgName = trim($branding['organization_name'] ?? '') ?: 'Issuing Organization';
$orgShort = trim($branding['organization_short_name'] ?? '') ?: '';
$logo = certAsset($branding['organization_logo'] ?? '');
$frame = certAsset($branding['certificate_frame'] ?? '');
if (!$frame && is_file(__DIR__ . '/../assets/templates/certificate-frame.png')) $frame = '../assets/templates/certificate-frame.png';
$stamp = certAsset($branding['official_stamp'] ?? '');
$signature = certAsset($branding['authorized_signature'] ?? '');
$signatoryName = trim($branding['signatory_name'] ?? '');
$signatoryPosition = trim($branding['signatory_position'] ?? 'Authorized Officer');
$donorName = trim((string)($user['full_name'] ?? $user['name'] ?? $user['username'] ?? 'Registered Donor'));
$currency = (string)($calc['currency_code'] ?? 'NGN');
$hasCertificate = $calc && $verifiedTotal > 0 && count($verifiedPayments) > 0;
$orientation = (($branding['certificate_orientation'] ?? 'portrait') === 'landscape') ? 'landscape' : 'portrait';
$certificateNo = $calc ? (trim($branding['certificate_prefix'] ?? '') ?: 'CERT') . '-CASH-' . str_pad((string)$calc['id'], 6, '0', STR_PAD_LEFT) : '';
$verifiedDate = '';
if ($verifiedPayments) {
    $last = end($verifiedPayments);
    foreach (['verified_at', 'confirmed_at', 'approved_at', 'updated_at'] as $key) {
        if (!empty($last[$key])) { $verifiedDate = (string)$last[$key]; break; }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Zakat Certificate - <?= certEscape($orgName) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
<?php if ($orientation === 'landscape'): ?>
@page { size: A4 landscape; margin: 0; }
<?php else: ?>
@page { size: A4 portrait; margin: 0; }
<?php endif; ?>
.cert-page { width: var(--paper-width); height: var(--paper-height); min-height: 0; margin: auto; position: relative; box-sizing: border-box; background: #fffdf2; padding: 15mm; color: #16432e; overflow: hidden; }
.cert-page.has-frame { background-color: #fffdf2; background-image: var(--cert-frame); background-size: 100% 100%; background-repeat: no-repeat; }
.cert-page:not(.has-frame)::before { content: ''; position: absolute; inset: 7mm; border: 0; pointer-events: none; }
.cert-content { position: relative; z-index: 1; height: 100%; min-height: 0; padding: 2mm 7mm; display: flex; flex-direction: column; justify-content: space-between; text-align: center; box-sizing: border-box; }
.cert-logo { height: 21mm; max-width: 34mm; object-fit: contain; margin: 0 auto 1mm; flex-shrink:0; }
.cert-org { font-size: 19pt; font-weight: 800; line-height: 1.25; text-transform: uppercase; }
.cert-heading { margin-top: 2mm; padding: 2mm 2mm; background: #07583e; color: white; border: 2px solid #bda34c; border-radius: 3mm; font-family: Georgia, serif; font-size: 20pt; font-weight: bold; }
.cert-name { font-family: Georgia, serif; font-size: 23pt; font-weight: bold; border-bottom: 2px solid #bda34c; padding: 2mm 3mm; display: inline-block; max-width: 100%; overflow-wrap: anywhere; }
.cert-amount { font-size: 23pt; font-weight: 800; margin-top: 2mm; }
.cert-data { margin-top: 2mm; border: 1px solid #cbb36a; border-radius: 3mm; padding: 5mm; text-align: left; font-size: 10pt; }
.cert-data-row { display: flex; justify-content: space-between; gap: 4mm; padding: 1mm 0; border-bottom: 1px solid #e6e4d8; }
.cert-data-row:last-child { border-bottom: none; }
.cert-data-row strong { text-align: right; overflow-wrap: anywhere; }
.cert-qr { display:flex; justify-content:center; align-items:center; min-height:19mm; margin-top:1mm; flex-shrink:0; }
.cert-qr canvas,.cert-qr img { max-width:19mm; max-height:19mm; }
.cert-footer { margin-top: 1mm; display: grid; grid-template-columns: 1fr 1fr; gap: 12mm; align-items: end; font-size: 10pt; }
.cert-footer img { height: 13mm; max-width: 38mm; object-fit: contain; margin: 0 auto 2mm; }
.cert-line { border-top: 1px solid #65756b; padding-top: 2mm; }
@media screen { .cert-page { box-shadow: 0 12px 30px #0002; } }
@media print {
 html, body { margin: 0 !important; padding: 0 !important; background: white !important; }
 .no-print, aside, nav, header { display: none !important; }
 .print-main, main, main > div { margin: 0 !important; padding: 0 !important; max-width: none !important; min-height: 0 !important; }
 .cert-page { width: var(--paper-width) !important; height: var(--paper-height) !important; min-height: 0 !important; box-shadow: none !important; page-break-inside: avoid; break-inside: avoid; }
 .cert-content { height:100% !important; }
 .cert-page { print-color-adjust:exact !important; -webkit-print-color-adjust:exact !important; }
 * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
@media print { .cert-page { margin:0 !important; } .cert-content { padding-top:4mm !important; padding-bottom:4mm !important; } }
<?php if ($orientation === 'landscape'): ?>
.cert-page { padding: 9mm 15mm; }
.cert-content { padding: 2mm 8mm; }
.cert-logo { height: 17mm; }
.cert-org { font-size: 16pt; }
.cert-heading { font-size: 17pt; margin-top: 2mm; padding:2mm; }
.cert-name,.cert-amount { font-size: 19pt; }
.cert-data { margin-top: 3mm; padding: 3mm 5mm; }
.cert-data-row { padding: 1mm 0; }
.cert-qr { min-height:16mm; }
.cert-qr canvas,.cert-qr img { max-width:16mm; max-height:16mm; }
.cert-footer img { height:12mm; }
<?php endif; ?>
</style>
</head>
<body class="bg-slate-50 text-slate-800">
<div class="no-print"><?php require __DIR__ . '/_sidebar.php'; ?></div>
<div class="md:ml-64 min-h-screen flex flex-col print-main">
<div class="no-print"><?php require __DIR__ . '/_header.php'; ?></div>
<main class="flex-1 p-4 md:p-8 print-main"><div class="max-w-5xl mx-auto">
<div class="no-print flex flex-wrap items-center justify-between gap-3 mb-6">
  <div><h1 class="text-2xl font-bold text-emerald-900">Zakat Certificate</h1>
  <p class="text-sm text-slate-500">Certificate of verified zakat payment</p></div>
  <a class="border bg-white rounded-lg px-4 py-2 text-sm font-semibold" href="zakat-status.php<?= $id ? '?id=' . (int)$id : '' ?>">View Zakat Status</a>
</div>

<?php if (!empty($dbError)): ?>
<div class="no-print bg-red-50 border border-red-200 rounded-xl p-6 text-red-800">Unable to load certificate data. Please check the database connection and tables.</div>
<?php elseif (!$id): ?>
<div class="no-print bg-white border rounded-2xl p-6">
<h2 class="font-bold mb-4">Select a calculation</h2>
<?php if (!$calculations): ?><p class="text-slate-500">No calculations found.</p><?php endif; ?>
<div class="space-y-3"><?php foreach ($calculations as $c): ?>
<a href="certificate.php?id=<?= (int)$c['id'] ?>" class="block border rounded-xl p-4 hover:bg-emerald-50">
<span class="font-semibold"><?= certEscape($c['reference_no'] ?? '#' . $c['id']) ?></span>
<span class="text-slate-500 text-sm ml-2"><?= certMoney($c['zakat_due'] ?? 0) ?></span>
<i class="fa-solid fa-chevron-right float-right text-emerald-700"></i>
</a><?php endforeach; ?></div></div>
<?php elseif (!$calc): ?>
<div class="no-print bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-6">Calculation not found, or it does not belong to your account.</div>
<?php elseif (!$hasCertificate): ?>
<div class="no-print bg-white border rounded-2xl p-8">
<div class="text-center py-5">
<i class="fa-solid fa-lock text-4xl text-amber-600 mb-4"></i>
<h2 class="text-xl font-bold text-amber-900">Certificate Not Yet Available</h2>
<p class="text-slate-600 mt-3 max-w-xl mx-auto">No verified cash payment was found for this calculation. Approval of a calculation alone does not confirm receipt of zakat. Contact the Board if you have already paid.</p>
<p class="text-sm mt-4">Reference: <strong><?= certEscape($calc['reference_no'] ?? $calc['id']) ?></strong></p>
</div></div>
<?php else: ?>
<div class="no-print bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 text-sm text-blue-900">
<strong>Scope:</strong> This certificate covers only the verified cash payments shown below. It does not certify livestock, crops, or other physical items. An official Board-issued certificate number/signature must be provided by the authorized office.
</div>
<div class="cert-page <?= $frame ? 'has-frame' : '' ?>" style="--paper-width:<?= $orientation === 'landscape' ? '297mm' : '210mm' ?>;--paper-height:<?= $orientation === 'landscape' ? '210mm' : '297mm' ?>;<?= $frame ? '--cert-frame:url(\'' . certEscape($frame) . '\')' : '' ?>">
 <div class="cert-content">
  <?php if ($logo): ?><img class="cert-logo" src="<?= certEscape($logo) ?>" alt="Organization logo"><?php else: ?><div style="font-size:30pt;color:#bda34c">✦</div><?php endif; ?>
  <div class="cert-org"><?= certEscape($orgName) ?></div>
  <div style="font-size:11pt;letter-spacing:3px;color:#b58f36;margin-top:2mm"><?= certEscape($orgShort) ?></div>
  <div class="cert-heading">CERTIFICATE OF ZAKAT PAYMENT</div>
  <p style="font-size:11pt;margin-top:2mm">This is to acknowledge verified zakat payment received from</p>
  <div style="margin-top:1mm"><span class="cert-name"><?= certEscape($donorName) ?></span></div>
  <p style="font-size:11pt;margin-top:1mm">Total verified cash zakat received</p>
  <div class="cert-amount"><?= certEscape(certMoney($verifiedTotal, $currency)) ?></div>
  <div class="cert-data">
   <div class="cert-data-row"><span>Zakat Category</span><strong>Cash Payment</strong></div>
   <div class="cert-data-row"><span>Calculation Reference</span><strong><?= certEscape($calc['reference_no'] ?? $calc['id']) ?></strong></div>
   <div class="cert-data-row"><span>Certificate Tracking ID</span><strong><?= certEscape($certificateNo) ?></strong></div>
   <div class="cert-data-row"><span>Number of Verified Payments</span><strong><?= count($verifiedPayments) ?></strong></div>
   <div class="cert-data-row"><span>Date of Verification</span><strong><?= certEscape($verifiedDate ?: 'Not recorded') ?></strong></div>
  </div>
  <div class="cert-qr" id="certificate-qr"></div>
  <p style="font-size:8pt;margin:0;color:#66756b">Scan to open this certificate page (login may be required).</p>
  <div class="cert-footer">
   <div>
    <?php if ($signature): ?><img src="<?= certEscape($signature) ?>" alt="Authorized signature"><?php else: ?><div style="height:13mm"></div><?php endif; ?>
    <div class="cert-line"><strong><?= certEscape($signatoryName ?: 'Authorized Officer') ?></strong><br><?= certEscape($signatoryPosition) ?></div>
   </div>
   <div>
    <?php if ($stamp): ?><img src="<?= certEscape($stamp) ?>" alt="Official stamp"><?php else: ?><div style="height:13mm"></div><?php endif; ?>
    <div class="cert-line">Official Stamp / Seal</div>
   </div>
  </div>
  <p style="font-size:8pt;color:#66756b;margin-top:1mm">Certificate tracking reference only. Subject to official authentication by the issuing organization.</p>
 </div>
</div>
<div class="no-print flex flex-wrap gap-3 mt-6">
<button type="button" onclick="window.print()" class="bg-emerald-800 text-white rounded-lg px-5 py-3 font-bold"><i class="fa-solid fa-print mr-2"></i>Print / Save as PDF</button>
<a href="reports.php" class="bg-white border rounded-lg px-5 py-3 font-semibold">My Reports</a>
</div>
<?php endif; ?>
</div></main></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function(){
 var el=document.getElementById('certificate-qr');
 if(el && typeof QRCode!=='undefined') new QRCode(el,{text:window.location.href,width:88,height:88,correctLevel:QRCode.CorrectLevel.M});
})();
</script>
</body></html>
