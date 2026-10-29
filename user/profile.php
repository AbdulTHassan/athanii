<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth-check.php';
$pageTitle='My Profile';
$uid=(int)($_SESSION['user_id']??($user['id']??0));
function ph($x){return htmlspecialchars((string)($x??''),ENT_QUOTES,'UTF-8');}
$notice='';$error='';
$cols=[];
try{$cols=array_column($pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_ASSOC),'Field');}
catch(Throwable $e){$error='Could not read user table structure.';}
$editable=[
 'phone'=>['phone','phone_number','mobile'],
 'address'=>['address','residential_address'],
 'name'=>['full_name','name']
];
$actual=[];
foreach($editable as $key=>$candidates){foreach($candidates as $col){if(in_array($col,$cols,true)){$actual[$key]=$col;break;}}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!isset($_SESSION['profile_csrf'])){$error='Session expired. Reload and try again.';}
 elseif(!hash_equals((string)$_SESSION['profile_csrf'],(string)($_POST['csrf']??''))){$error='Invalid request token. Reload and try again.';}
 elseif(!$uid){$error='You must log in again.';}
 else{
  $values=[];$set=[];
  foreach($actual as $key=>$col){
   $value=trim((string)($_POST[$key]??''));
   if($key==='phone' && $value!=='' && !preg_match('/^\+?[0-9 ()-]{7,20}$/',$value)){$error='Please enter a valid phone number.';break;}
   if($key==='name' && ($value===''||strlen($value)>120)){$error='Name is required (maximum 120 characters).';break;}
   if($key==='address' && strlen($value)>255){$error='Address must be at most 255 characters.';break;}
   $set[]="`$col` = ?";$values[]=$value;
  }
  if(!$error && $set){
   try{
    $values[]=$uid;
    $stmt=$pdo->prepare('UPDATE users SET '.implode(', ',$set).' WHERE id = ?');
    $stmt->execute($values);
    $notice='Your profile has been updated successfully.';
   }catch(Throwable $e){$error='Could not save changes. Please check your database configuration.';}
  }
 }
}
$_SESSION['profile_csrf']=$_SESSION['profile_csrf']??bin2hex(random_bytes(32));
try{$stmt=$pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');$stmt->execute([$uid]);$profile=$stmt->fetch(PDO::FETCH_ASSOC)?:$user;}
catch(Throwable $e){$profile=$user;$error=$error?:'Could not load updated profile details.';}
$labels=['name'=>'Full Name','phone'=>'Phone Number','address'=>'Address'];
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Profile - KAZWAB</title><script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body class="bg-slate-50 text-slate-800">
<?php require __DIR__.'/_sidebar.php'; ?>
<div class="md:ml-64 min-h-screen flex flex-col"><?php require __DIR__.'/_header.php'; ?>
<main class="flex-1 p-4 md:p-8"><div class="max-w-3xl mx-auto">
<h1 class="text-2xl font-bold text-emerald-900 mb-2"><i class="fa-regular fa-user mr-2"></i>My Profile</h1>
<p class="text-slate-500 mb-6">Update your contact details whenever they change.</p>
<?php if($notice):?><div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl mb-5"><?=ph($notice)?></div><?php endif;?>
<?php if($error):?><div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl mb-5"><?=ph($error)?></div><?php endif;?>
<div class="bg-white border rounded-2xl overflow-hidden shadow-sm">
<div class="bg-emerald-800 text-white p-6"><h2 class="text-xl font-bold"><?=ph($profile['full_name']??$profile['name']??'KAZWAB Donor')?></h2><p class="text-emerald-100 text-sm">Account information</p></div>
<form method="post" class="p-6 md:p-8 space-y-5">
<input type="hidden" name="csrf" value="<?=ph($_SESSION['profile_csrf'])?>">
<div><label class="block text-sm font-semibold mb-2">Email Address</label><input class="w-full bg-slate-100 border rounded-xl px-4 py-3 text-slate-500" value="<?=ph($profile['email']??'')?>" readonly><p class="text-xs text-slate-500 mt-1">Email changes require account verification.</p></div>
<?php foreach($labels as $key=>$label):if(!isset($actual[$key]))continue;$column=$actual[$key];?>
<div><label for="<?=ph($key)?>" class="block text-sm font-semibold mb-2"><?=ph($label)?></label>
<input id="<?=ph($key)?>" name="<?=ph($key)?>" type="<?= $key==='phone'?'tel':'text' ?>" maxlength="<?= $key==='address'?255:($key==='name'?120:20) ?>" value="<?=ph($profile[$column]??'')?>" class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500" <?= $key==='name'?'required':'' ?>>
</div>
<?php endforeach;?>
<?php if($actual):?><button type="submit" class="bg-emerald-800 hover:bg-emerald-900 text-white font-bold rounded-xl px-6 py-3"><i class="fa-solid fa-floppy-disk mr-2"></i>Save Changes</button>
<?php else:?><p class="text-amber-700">No editable profile fields were found in the users table.</p><?php endif;?>
</form></div>
<p class="text-xs text-slate-500 mt-4">Password and email are not editable here for account security.</p>
</div></main></div></body></html>
