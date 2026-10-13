<?php
$pageTitle='Profile'; require_once '_header.php';
$userId=(int)$_SESSION['user_id'];$message='';$error='';$u=getUser($userId);
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['full_name']??'');$phone=trim($_POST['phone']??'');$lang=$_POST['language']??'en';
 if($name==='')$error='Full name is required.';else{$st=$pdo->prepare("UPDATE users SET full_name=?,phone=?,language=? WHERE id=?");$st->execute([$name,$phone,in_array($lang,['en','ha','ar'],true)?$lang:'en',$userId]);$_SESSION['user_name']=$name;$_SESSION['lang']=$lang;$message='Profile updated successfully.';$u=getUser($userId);logActivity($userId,'profile_update',$userId,'Updated profile');}
}
?>
<div class="max-w-2xl mx-auto"><div class="mb-7"><h1 class="text-2xl font-bold">Profile & Settings</h1><p class="text-slate-500 text-sm mt-1">Keep your ZakatPro account information up to date.</p></div><div class="bg-white border rounded-2xl p-6">
<?php if($message):?><div class="mb-5 p-3 rounded-xl bg-green-50 text-green-700"><?=$message?></div><?php endif;?><?php if($error):?><div class="mb-5 p-3 rounded-xl bg-red-50 text-red-700"><?=$error?></div><?php endif;?>
<form method="POST" class="space-y-5"><div><label class="block text-sm font-semibold mb-2">Full name</label><input name="full_name" value="<?=e($u['full_name'])?>" class="w-full border rounded-xl px-4 py-3" required></div><div><label class="block text-sm font-semibold mb-2">Email</label><input value="<?=e($u['email'])?>" disabled class="w-full border rounded-xl px-4 py-3 bg-slate-50 text-slate-500"></div><div><label class="block text-sm font-semibold mb-2">Phone</label><input name="phone" value="<?=e($u['phone']??'')?>" class="w-full border rounded-xl px-4 py-3"></div><div><label class="block text-sm font-semibold mb-2">Language</label><select name="language" class="w-full border rounded-xl px-4 py-3"><option value="en" <?=$u['language']==='en'?'selected':''?>>English</option><option value="ha" <?=$u['language']==='ha'?'selected':''?>>Hausa</option><option value="ar" <?=$u['language']==='ar'?'selected':''?>>العربية</option></select></div><button class="bg-emerald-700 text-white px-5 py-3 rounded-xl font-semibold">Save Changes</button></form>
<div class="mt-8 pt-6 border-t"><p class="text-sm text-slate-500">Member since</p><p class="font-semibold mt-1"><?=date('M d, Y',strtotime($u['created_at']))?></p></div>
</div></div><?php require_once '_footer.php'; ?>
