<?php
// admin/includes/header.php

$user = currentUser();

require_once __DIR__ . '/../../includes/app_settings.php';

$softwareName = appName($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($pageTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>
        -
        <?= htmlspecialchars($softwareName, ENT_QUOTES, 'UTF-8') ?>
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">