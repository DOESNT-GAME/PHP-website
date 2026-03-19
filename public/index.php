<?php
$logo_path = "logo.png";
$student_name = "Корепов Андрей Алексеевич";
$group = "241-352";
$lab_number = "3";

if (!isset($_GET['store'])) {
    $_GET['store'] = '';
}
if (!isset($_GET['count'])) {
    $_GET['count'] = 0;
}

if (isset($_GET['key'])) {
    if ($_GET['key'] === 'reset') {
        $_GET['store'] = '';
        $_GET['count'] = (int)$_GET['count'] + 1;
    } else {
        $_GET['store'] .= $_GET['key'];
        $_GET['count'] = (int)$_GET['count'] + 1;
    }
}

$store = $_GET['store'];
$count = (int)$_GET['count'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР №<?php echo $lab_number; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo" class="logo" onerror="this.style.display='none'">
    <div class="header-info">
        <span class="student-info"><?php echo $student_name; ?> | Группа: <?php echo $group; ?></span>
    </div>
</header>

<main>
    <h1>Лабораторная работа №<?php echo $lab_number; ?></h1>
    <h2>Виртуальная клавиатура</h2>
    
    <div class="result-label">Окно просмотра результата:</div>
    <div class="result-window">
        <?php echo htmlspecialchars($store); ?>
    </div>

    <div class="keyboard-container">
        <div class="keyboard-row">
            <a href="?key=1&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">1</a>
            <a href="?key=2&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">2</a>
            <a href="?key=3&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">3</a>
        </div>
        <div class="keyboard-row">
            <a href="?key=4&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">4</a>
            <a href="?key=5&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">5</a>
            <a href="?key=6&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">6</a>
        </div>
        <div class="keyboard-row">
            <a href="?key=7&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">7</a>
            <a href="?key=8&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">8</a>
            <a href="?key=9&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">9</a>
        </div>
        <div class="keyboard-row center">
            <a href="?key=0&store=<?php echo urlencode($store); ?>&count=<?php echo $count; ?>" class="key-btn">0</a>
        </div>
        <div class="keyboard-row">
            <a href="?key=reset&store=&count=<?php echo $count; ?>" class="key-btn reset">СБРОС</a>
        </div>
    </div>

    <div class="click-counter">Всего нажатий: <?php echo $count; ?></div>
</main>

<footer>
    <div>Тип вёрстки: GET-параметры</div>
    <div class="timestamp">Сформировано: <?php
    date_default_timezone_set('Europe/Moscow');
    echo date('H:i:s'); ?></div>
</footer>
</body>
</html>