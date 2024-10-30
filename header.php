<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Бібліотека</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="/labsWeb/lab_7/index.php">Головна</a>
    <a href="/labsWeb/lab_7/catalog.php">Каталог книг</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="/labsWeb/lab_7/admin/add_book.php">Додати книгу</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/labsWeb/lab_7/logout.php">Вийти</a>
    <?php else: ?>
        <a href="/labsWeb/lab_7/register.php">Реєстрація</a>
        <a href="/labsWeb/lab_7/login.php">Авторизація</a>
    <?php endif; ?>
</nav>
