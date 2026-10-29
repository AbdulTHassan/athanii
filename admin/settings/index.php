<?php
$pageTitle = 'System Settings';
require_once __DIR__ . '/../../includes/admin_init.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['settings_csrf'])) $_SESSION['settings_csrf'] = bin2hex(random_bytes(32));
function ssH($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$categories = [
 'general'=>['General Settings','fa-gear','Software identity and contact'],
 'nisab'=>['Zakat & Nisab','fa-scale-balanced','Manual Nisab valuation and source'],
 'currency'=>['Currency Settings','fa-coins','Manual currency exchange rates'],
 'users'=>['Users & Roles','fa-users','Account policies'],
 'payments'=>['Payment Settings','fa-credit-card','Payment configuration'],
 'certificates'=>['Certificates','fa-certificate','Certificate configuration'],
 'branding'=>['Organization Branding','fa-image','Upload certificate frame, logo, stamp and signature'],
 'notifications'=>['Notifications','fa-bell','Alerts and messages'],
 'security'=>['Security','fa-shield-halved','Security preferences'],
 'localization'=>['Localization','fa-globe','Language and timezone'],
 'maintenance'=>['Maintenance','fa-database','Maintenance preferences']
];
$fields = [
 'general'=>[
  'app_name'=>['Software Name','text','Zakat Management System'],
  'app_email'=>['Support Email','email',''],
  'app_phone'=>['Contact Phone','text',''],
  'app_description'=>['Software Description','text','']
 ],
 'currency'=>[
  'default_currency'=>['Default Currency','select','NGN',['NGN'=>'NGN','USD'=>'USD','EUR'=>'EUR']],
  'usd_to_ngn'=>['1 USD = NGN (manual rate)','number',''],
  'eur_to_ngn'=>['1 EUR = NGN (manual rate)','number','']
 ],
 'users'=>['allow_registration'=>['Allow New Registration','boolean','1']],
 'payments'=>[
   'bank_name'=>['Official Bank Name','text',''],
   'bank_account_name'=>['Official Account Name','text',''],
   'bank_account_number'=>['Official Account Number','text',''],
   'bank_account_active'=>['Enable Bank Account for Donations','boolean','0'],
   'payment_instructions'=>['Payment Instructions','text','']
  ],
 'certificates'=>[
 'certificate_prefix'=>['Certificate Reference Prefix','text','CERT'],
 'certificate_orientation'=>['Certificate Paper Orientation','select','portrait',['portrait'=>'A4 Portrait','landscape'=>'A4 Landscape']]
 ],
 'branding'=>[
 'organization_name'=>['Organization Name','text',''],
 'organization_short_name'=>['Organization Abbreviation','text',''],
 'signatory_name'=>['Authorized Signatory Name','text',''],
 'signatory_position'=>['Signatory Position','text','Authorized Officer']
 ],
 'notifications'=>['email_notifications'=>['Enable Email Notifications','boolean','0']],
 'security'=>['session_timeout_minutes'=>['Session Timeout (minutes) — policy only','number','30']],
 'localization'=>[
  'default_language'=>['Default Language','select','en',['en'=>'English','ha'=>'Hausa']],
  'app_timezone'=>['Timezone','select','Africa/Lagos',['Africa/Lagos'=>'Africa/Lagos','UTC'=>'UTC']]
 ],
 'maintenance'=>['maintenance_mode'=>['Maintenance Mode — policy only','boolean','0']]
];
$category = $_GET['category'] ?? 'general';
if (!is_string($category) || !isset($categories[$category])) $category='general';
$settings=[];
foreach ($pdo->query('SELECT setting_key,setting_value FROM system_settings')->fetchAll(PDO::FETCH_ASSOC) as $r) $settings[$r['setting_key']]=$r['setting_value'];
$prices=[]; $nisab=[];
foreach (['gold','silver'] as $metal) {
 $s=$pdo->prepare("SELECT * FROM nisab_settings WHERE metal=? AND currency_code='NGN' AND status='active' ORDER BY effective_from DESC,id DESC LIMIT 1");
 $s->execute([$metal]); $nisab[$metal]=$s->fetch(PDO::FETCH_ASSOC) ?: null;
 $s=$pdo->prepare("SELECT * FROM metal_prices WHERE metal=? AND currency_code='NGN' AND status='active' ORDER BY effective_from DESC,id DESC LIMIT 1");
 $s->execute([$metal]); $prices[$metal]=$s->fetch(PDO::FETCH_ASSOC) ?: null;
}
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $token=$_POST['csrf_token']??'';
 if (!is_string($token) || !hash_equals($_SESSION['settings_csrf'],$token)) $error='Security token expired. Refresh the page.';
 else {
  try {
   $pdo->beginTransaction();
   if ($category==='nisab') {
    $effective=trim((string)($_POST['effective_date']??''));
    $dt=DateTimeImmutable::createFromFormat('!Y-m-d',$effective);
    if (!$dt || $dt->format('Y-m-d')!==$effective) throw new InvalidArgumentException('Enter a valid effective date.');
    $effectiveAt=$effective.' 00:00:00';
    $source=trim((string)($_POST['source_reference']??''));
    if ($source==='' || mb_strlen($source)>1000) throw new InvalidArgumentException('Enter a source (up to 1000 characters).');
    $any=false;
    foreach (['gold'=>85,'silver'=>595] as $metal=>$grams) {
     $raw=trim((string)($_POST[$metal.'_value']??''));
     if ($raw==='') continue; // no change to this metal
     if (!is_numeric($raw) || !is_finite((float)$raw) || (float)$raw<=0 || (float)$raw>99999999999999) throw new InvalidArgumentException('Invalid '.ucfirst($metal).' Nisab value.');
     $amount=round((float)$raw,4); $any=true;
     // Keep history; end previously active rows for this metal/currency.
     $u=$pdo->prepare("UPDATE nisab_settings SET status='inactive',effective_to=NOW() WHERE metal=? AND currency_code='NGN' AND status='active'");
     $u->execute([$metal]);
     $i=$pdo->prepare("INSERT INTO nisab_settings (methodology_id,metal,threshold_grams,currency_code,valuation_price,effective_from,source_reference,status) VALUES (NULL,?,?,'NGN',?, ?,?,'active')");
     $i->execute([$metal,$grams,$amount,$effectiveAt,$source]);
     // Derived price per gram from this published total; not independently verified market quote.
     $perGram=round($amount/$grams,4);
     $u=$pdo->prepare("UPDATE metal_prices SET status='inactive',effective_to=NOW() WHERE metal=? AND currency_code='NGN' AND status='active'");
     $u->execute([$metal]);
     $i=$pdo->prepare("INSERT INTO metal_prices (metal,price_per_gram,currency_code,source_name,source_reference,effective_from,status) VALUES (? ,?,'NGN',?,?,?,'active')");
     $i->execute([$metal,$perGram,'Derived from published Nisab value',$source,$effectiveAt]);
    }
    if (!$any) throw new InvalidArgumentException('Enter at least one Gold or Silver Nisab value.');
   } else {
    if (!isset($fields[$category])) throw new InvalidArgumentException('This category is not editable.');
    $save=$pdo->prepare("INSERT INTO system_settings(setting_key,setting_value,setting_type,description,updated_by) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_type=VALUES(setting_type),description=VALUES(description),updated_by=VALUES(updated_by)");
    if ($category === 'branding') {
     $assetFields = ['organization_logo'=>'Organization Logo', 'certificate_frame'=>'Certificate Frame', 'official_stamp'=>'Official Stamp', 'authorized_signature'=>'Authorized Signature'];
     $dir = __DIR__ . '/../../uploads/organization';
     foreach ($assetFields as $assetKey=>$assetLabel) {
      if (!isset($_FILES[$assetKey]) || $_FILES[$assetKey]['error'] === UPLOAD_ERR_NO_FILE) continue;
      $file = $_FILES[$assetKey];
      if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) throw new InvalidArgumentException('Upload failed: '.$assetLabel);
      if ($file['size'] > 5 * 1024 * 1024 || $file['size'] <= 0) throw new InvalidArgumentException($assetLabel.' must be under 5 MB.');
      $info = @getimagesize($file['tmp_name']);
      if (!$info || !in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP], true)) throw new InvalidArgumentException('Only valid PNG, JPEG or WebP images are accepted for '.$assetLabel);
      $ext = [IMAGETYPE_PNG=>'png', IMAGETYPE_JPEG=>'jpg', IMAGETYPE_WEBP=>'webp'][$info[2]];
      if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) throw new RuntimeException('Cannot create organization upload folder.');
      $filename = $assetKey.'_'.bin2hex(random_bytes(12)).'.'.$ext;
      if (!move_uploaded_file($file['tmp_name'], $dir.'/'.$filename)) throw new RuntimeException('Could not save '.$assetLabel);
      $path = 'uploads/organization/'.$filename;
      $save->execute([$assetKey,$path,'string',$assetLabel,(int)($_SESSION['user_id']??0)]);
     }
    }
    foreach ($fields[$category] as $key=>$field) {
     $v=$_POST[$key]??'';
     if (!is_string($v)) throw new InvalidArgumentException('Invalid input for '.$field[0]);
     $v=trim($v);
     if (strlen($v)>2000) throw new InvalidArgumentException('Value too long: '.$field[0]);
     if ($category==='payments' && $key==='bank_account_number' && $v!=='' && !preg_match('/^[0-9]{10}$/', $v)) throw new InvalidArgumentException('Account number must contain exactly 10 digits.');
      if ($category==='payments' && $key==='bank_account_active' && $v==='1') {
       $bn=trim((string)($_POST['bank_name']??''));
       $an=trim((string)($_POST['bank_account_name']??''));
       $no=trim((string)($_POST['bank_account_number']??''));
       if ($bn==='' || $an==='' || !preg_match('/^[0-9]{10}$/',$no)) throw new InvalidArgumentException('Complete bank name, account name and 10-digit account number before enabling donations.');
      }
      if ($field[1]==='email' && $v!=='' && !filter_var($v,FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Invalid email.');
     if ($field[1]==='number' && $v!=='' && (!is_numeric($v)||!is_finite((float)$v)||(float)$v<=0)) throw new InvalidArgumentException('Invalid positive number: '.$field[0]);
     if ($field[1]==='select' && !array_key_exists($v,$field[3])) throw new InvalidArgumentException('Invalid selection: '.$field[0]);
     if ($field[1]==='boolean' && !in_array($v,['0','1'],true)) throw new InvalidArgumentException('Invalid toggle: '.$field[0]);
     $save->execute([$key,$v,$field[1]==='number'?'number':($field[1]==='boolean'?'boolean':'string'),$field[0],(int)($_SESSION['user_id']??0)]);
    }
   }
   $pdo->commit();
   header('Location: index.php?category='.urlencode($category).'&saved=1'); exit;
  } catch (Throwable $e) {
   if ($pdo->inTransaction()) $pdo->rollBack();
   $error=$e instanceof InvalidArgumentException?$e->getMessage():'Could not save settings. Check PHP error log.';
   if (!($e instanceof InvalidArgumentException)) error_log('ZakatPro settings: '.$e->getMessage());
  }
 }
}
if (isset($_GET['saved'])) $success=$category==='nisab' ? 'Nisab value(s) saved successfully. Any field left blank was not changed.' : 'Settings saved successfully.';
require_once __DIR__.'/../../includes/admin_header.php';
?>
<div class="space-y-6">
 <div><h1 class="text-2xl font-bold text-slate-900">System Settings</h1><p class="text-slate-500 text-sm mt-1">Manage your software configuration.</p></div>
 <?php if ($success): ?><div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-800"><?=ssH($success)?></div><?php endif; ?>
 <?php if ($error): ?><div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700"><?=ssH($error)?></div><?php endif; ?>
 <div class="grid lg:grid-cols-4 gap-6"><div class="bg-white border rounded-2xl p-3 space-y-1 lg:col-span-1 self-start">
 <?php foreach($categories as $key=>$item): ?><a href="?category=<?=ssH($key)?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm <?= $category===$key?'bg-emerald-700 text-white':'text-slate-700 hover:bg-slate-100' ?>"><i class="fa-solid <?=ssH($item[1])?> w-5"></i><?=ssH($item[0])?></a><?php endforeach; ?>
 </div><div class="bg-white border rounded-2xl p-6 lg:col-span-3"><div class="border-b pb-5 mb-6"><h2 class="text-xl font-bold"><i class="fa-solid <?=ssH($categories[$category][1])?> text-emerald-700 mr-2"></i><?=ssH($categories[$category][0])?></h2><p class="text-slate-500 text-sm mt-2"><?=ssH($categories[$category][2])?></p></div>
 <form method="post" enctype="multipart/form-data" action="?category=<?=ssH($category)?>"><input type="hidden" name="csrf_token" value="<?=ssH($_SESSION['settings_csrf'])?>">
 <?php if ($category==='nisab'): ?>
 <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6 text-sm">Fixed standards: <strong>Gold 85g</strong> and <strong>Silver 595g</strong>. You may enter <strong>Gold only, Silver only, or both together</strong>, then click Save Settings once. Leave a field blank to preserve its existing value. Enter the published <strong>total Nisab value in NGN</strong>, not a per-gram price.</div>
 <div class="grid md:grid-cols-2 gap-5">
 <?php foreach(['gold'=>'Gold (85g)','silver'=>'Silver (595g)'] as $metal=>$label): ?><div><label class="block text-sm font-semibold mb-2"><?=ssH($label)?> Nisab Value (₦)</label><input name="<?=ssH($metal)?>_value" type="number" min="0.0001" step="0.0001" placeholder="Optional: total NGN Nisab value" class="w-full border rounded-xl p-3"><p class="text-xs text-slate-500 mt-2">Current: <?= $nisab[$metal] && $nisab[$metal]['valuation_price']!==null ? '₦'.number_format((float)$nisab[$metal]['valuation_price'],2) : 'Not set' ?></p></div><?php endforeach; ?>
 <div><label class="block text-sm font-semibold mb-2">Effective Date</label><input type="date" name="effective_date" value="<?=ssH(date('Y-m-d'))?>" required class="w-full border rounded-xl p-3"></div>
 <div class="md:col-span-2"><label class="block text-sm font-semibold mb-2">Source / Reference</label><textarea name="source_reference" rows="3" maxlength="1000" required placeholder="Organization, publication, reference and date" class="w-full border rounded-xl p-3"></textarea></div></div>
 <p class="text-xs text-amber-800 bg-amber-50 rounded-xl p-3 mt-5">Gold and Silver values are stored separately in nisab_settings; derived per-gram values are recorded in metal_prices. Users select Gold or Silver themselves in the calculator. Confirm the source and gram standard of every published figure.</p>
 <?php else: ?><div class="grid md:grid-cols-2 gap-5">
 <?php foreach($fields[$category] as $key=>$field): $v=$settings[$key]??$field[2]; ?><div><label class="block text-sm font-semibold mb-2"><?=ssH($field[0])?></label>
 <?php if ($field[1]==='select'): ?><select name="<?=ssH($key)?>" class="w-full border rounded-xl p-3"><?php foreach($field[3] as $ov=>$ol): ?><option value="<?=ssH($ov)?>" <?=$v===$ov?'selected':''?>><?=ssH($ol)?></option><?php endforeach; ?></select>
 <?php elseif ($field[1]==='boolean'): ?><select name="<?=ssH($key)?>" class="w-full border rounded-xl p-3"><option value="1" <?=$v==='1'?'selected':''?>>Enabled</option><option value="0" <?=$v==='0'?'selected':''?>>Disabled</option></select>
 <?php else: ?><input type="<?=ssH($field[1])?>" name="<?=ssH($key)?>" value="<?=ssH($v)?>" <?= $field[1]==='number'?'min="0.000001" step="any"':'' ?> class="w-full border rounded-xl p-3"><?php endif; ?></div><?php endforeach; ?>
 </div>
 <?php if ($category==='branding'): ?>
 <div class="mt-7 grid md:grid-cols-2 gap-5">
 <?php foreach (['organization_logo'=>'Organization Logo','certificate_frame'=>'Certificate Frame (match selected orientation)','official_stamp'=>'Official Stamp','authorized_signature'=>'Authorized Signature'] as $assetKey=>$assetLabel): ?>
 <div class="rounded-xl border p-4"><label class="block font-semibold text-sm mb-2"><?=ssH($assetLabel)?></label>
 <?php if (!empty($settings[$assetKey]) && preg_match('~^uploads/organization/[a-zA-Z0-9_.-]+\.(png|jpg|jpeg|webp)$~i', $settings[$assetKey])): ?>
 <img src="../../<?=ssH($settings[$assetKey])?>" alt="Current <?=ssH($assetLabel)?>" class="max-h-40 max-w-full object-contain mb-3 rounded border">
 <?php endif; ?>
 <input type="file" name="<?=ssH($assetKey)?>" accept="image/png,image/jpeg,image/webp" class="w-full text-sm border rounded-xl p-3">
 <p class="text-xs text-slate-500 mt-2">PNG, JPEG or WebP; maximum 5 MB. Leave empty to keep the current image.</p></div>
 <?php endforeach; ?></div>
 <?php endif; ?>
 <?php if(in_array($category,['security','maintenance','users','payments','certificates','notifications','localization'],true)): ?><p class="text-sm bg-amber-50 text-amber-800 p-3 rounded-xl mt-5">These preferences are saved in the database; enforcement/integration into the relevant modules is a separate step.</p><?php endif; ?><?php endif; ?>
 <div class="border-t mt-8 pt-5 flex justify-end"><button class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-6 py-3 rounded-xl"><i class="fa-solid fa-floppy-disk mr-2"></i>Save Settings</button></div></form></div></div></div>
<?php require_once __DIR__.'/../../includes/admin_footer.php'; ?>
