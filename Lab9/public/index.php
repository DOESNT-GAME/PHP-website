<?php
require_once 'menu.php';

$p = $_GET['p'] ?? 'viewer';
$page = intval($_GET['page'] ?? 0);
$sort = $_GET['sort'] ?? 'byid';
if (!in_array($sort, ['byid', 'fam', 'birth'])) $sort = 'byid';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР №9 - Записная книжка</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <img src="logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
        <div class="header-info">
            <span class="student-info">Корепов Андрей Алексеевич | Группа: 241-352</span>
        </div>
    </header>
    <main class="layout-container">
        <aside class="sidebar">
            <h1>ЛР №9</h1>
            <h2>Записная книжка</h2>
            <?= get_menu_html() ?>
        </aside>
        <section class="content-area">
            <h1>База данных контактов</h1>
            <?php
            if ($p == 'viewer') {
                require_once 'viewer.php';
                echo getFriendsList($sort, $page);
            } elseif ($p == 'add') {
                include 'add.php';
            } elseif ($p == 'edit') {
                include 'edit.php';
            } elseif ($p == 'delete') {
                include 'delete.php';
            } else {
                echo '<div class="msg-error">Неверный параметр запроса.</div>';
            }
            ?>
        </section>
    </main>
    <footer>
        <div class="footer-info">Лабораторная работа №9. Основы баз данных и программных модулей.</div>
        <div class="timestamp" id="current-date"></div>
    </footer>
    <script>
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('ru-RU');
    </script>
</body>
</html>