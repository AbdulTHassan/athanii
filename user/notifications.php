<?php
require_once __DIR__ . '/includes/auth-check.php';
$pageTitle = 'Notifications';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> - KAZWAB</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body class="bg-slate-50 text-slate-800">
<?php require __DIR__ . '/_sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col">
<?php require __DIR__ . '/_header.php'; ?>
<main class="flex-1 p-5 md:p-8"><div class="max-w-6xl mx-auto">

<h1 class="text-2xl font-bold text-emerald-900 mb-2"><i class="fa-regular fa-bell mr-2"></i>Notifications</h1>
<p class="text-slate-500 mb-6">Updates about your zakat calculations and account.</p>
<?php
$items=[]; $noticeError=false;
try {
  $stmt=$pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 100');
  $stmt->execute([(int)$user['id']]);
  $items=$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) { $noticeError=true; }
?>
<?php if($noticeError): ?>
<div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-5">Notifications are temporarily unavailable. The notification table may use a different schema.</div>
<?php elseif(!$items): ?>
<div class="bg-white border rounded-2xl p-12 text-center text-slate-500"><i class="fa-regular fa-bell text-4xl mb-4 text-emerald-700"></i><p>No notifications yet.</p></div>
<?php else: ?>
<div class="space-y-3">
<?php foreach($items as $n):
$title=(string)($n['title']??$n['subject']??'Notification');
$msg=(string)($n['message']??$n['body']??$n['content']??'');
$date=$n['created_at']??$n['date_created']??null;
?>
<div class="bg-white border border-slate-200 rounded-2xl p-5 flex gap-4">
<div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0"><i class="fa-solid fa-bell"></i></div>
<div class="min-w-0 flex-1"><h2 class="font-bold text-slate-900"><?=htmlspecialchars($title,ENT_QUOTES,'UTF-8')?></h2>
<p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap"><?=htmlspecialchars($msg,ENT_QUOTES,'UTF-8')?></p>
<?php if($date): ?><p class="text-xs text-slate-400 mt-3"><?=htmlspecialchars((string)$date,ENT_QUOTES,'UTF-8')?></p><?php endif; ?>
</div></div>
<?php endforeach; ?></div>
<?php endif; ?>
</div></main></div></body></html>