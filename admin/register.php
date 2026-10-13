<?php
require_once '../config/session.php';require_once '../includes/functions.php';
$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['full_name']??'');$email=trim($_POST['email']??'');$password=$_POST['password']??'';$confirm=$_POST['confirm_password']??'';
 if($name===''||$email===''||$password===''||$confirm==='')$error='All fields are required.';
 elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Enter a valid email address.';
 elseif(strlen($password)<8)$error='Password must be at least 8 characters.';
 elseif($password!==$confirm)$error='Passwords do not match.';
 else{try{$st=$pdo->prepare("INSERT INTO users(role_id,full_name,email,password) VALUES(2,?,?,?)");$st->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);$success='Registration successful. You can now login.';}catch(PDOException $e){$error='This email is already registered.';}}
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register - ZakatPro</title><script src="https://cdn.tailwindcss.com"></script></head><body class="min-h-screen bg-[#f7faf8] flex items-center justify-center p-4"><div class="w-full max-w-md bg-white rounded-2xl border p-7 shadow-sm"><div class="text-center"><div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl">☪</div><h1 class="text-2xl font-bold text-emerald-800 mt-4">Create your ZakatPro account</h1></div><?php if($error):?><div class="mt-5 p-3 rounded-xl bg-red-50 text-red-700 text-sm"><?=e($error)?></div><?php endif;?><?php if($success):?><div class="mt-5 p-3 rounded-xl bg-green-50 text-green-700 text-sm"><?=e($success)?></div><?php endif;?><form method="POST" class="mt-6 space-y-4"><input name="full_name" required placeholder="Full Name" class="w-full border rounded-xl px-4 py-3"><input type="email" name="email" required placeholder="Email" class="w-full border rounded-xl px-4 py-3"><input type="password" name="password" required placeholder="Password (8+ characters)" class="w-full border rounded-xl px-4 py-3"><input type="password" name="confirm_password" required placeholder="Confirm Password" class="w-full border rounded-xl px-4 py-3"><button class="w-full bg-emerald-700 text-white py-3 rounded-xl font-semibold">Create Account</button></form><p class="text-center text-sm mt-5 text-slate-500">Already registered? <a href="login.php" class="text-emerald-700 font-semibold">Login</a></p></div></body></html>
