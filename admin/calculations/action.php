<?php
require_once __DIR__.'/../../includes/admin_init.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if ($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);exit('POST required');}
$token=$_POST['csrf_token']??'';
if (!is_string($token)||empty($_SESSION['workflow_csrf'])||!hash_equals($_SESSION['workflow_csrf'],$token)) {http_response_code(403);exit('Invalid CSRF token');}
$id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
$action=$_POST['action']??'';
$note=trim((string)($_POST['review_note']??''));
if (!$id||!in_array($action,['approve','reject'],true)||strlen($note)>1000||($action==='reject'&&$note==='')) {http_response_code(422);exit('Invalid action or missing rejection note');}
try {
 $pdo->beginTransaction();
 $st=$pdo->prepare('SELECT status FROM zakat_calculations WHERE id=? FOR UPDATE');$st->execute([$id]);$old=$st->fetchColumn();
 if (!in_array($old,['calculated','saved','submitted','under_review'],true)) throw new RuntimeException('Calculation is no longer awaiting review.');
 $next=$action==='approve'?'approved':'rejected';
 $st=$pdo->prepare('UPDATE zakat_calculations SET status=?,status_v2=? WHERE id=?');$st->execute([$next,$next,$id]);
 $st=$pdo->prepare("INSERT INTO audit_logs (user_id,action,entity_type,entity_id,old_values,new_values) VALUES (?,?,?,?,?,?)");
 $st->execute([(int)$_SESSION['user_id'],'calculation_'.$next,'zakat_calculations',$id,json_encode(['status'=>$old]),json_encode(['status'=>$next,'review_note'=>$note])]);
 $pdo->commit();header('Location: view.php?id='.$id);exit;
} catch(Throwable $e) {if($pdo->inTransaction())$pdo->rollBack();error_log('Calculation review: '.$e->getMessage());http_response_code(409);exit('Unable to update calculation. Verify database migration and current status.');}
