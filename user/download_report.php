<?php
require_once '../config/session.php';require_once '../includes/functions.php';requireLogin();
$c=getLatestCalculation((int)$_SESSION['user_id']);
if(!$c){header('Location: calculator.php');exit;}
$rid=createReportFromCalculation((int)$_SESSION['user_id'],(int)$c['id']);
if($rid){header('Location: report.php?id='.$rid);exit;}
header('Location: calculations.php');exit;
?>