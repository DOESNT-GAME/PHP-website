<?php
$logo_path = "logo.png";
$student_name = "Корепов Андрей Алексеевич";
$group = "241-352";
$lab_number = "5";
date_default_timezone_set('Europe/Moscow');

$html_type = isset($_GET['html_type']) ? $_GET['html_type'] : 'TABLE';
if ($html_type !== 'TABLE' && $html_type !== 'DIV') {
    $html_type = 'TABLE';
}

$content = isset($_GET['content']) ? $_GET['content'] : null;
if ($content !== null && (!is_numeric($content) || $content < 2 || $content > 9)) {
    $content = null;
}

function outNumAsLink($x) {
    if ($x >= 2 && $x <= 9) {
        return '<a href="?content=' . $x . '">' . $x . '</a>';
    }
    return (string)$x;
}

function outRowLine($n, $i) {
    $result = $n * $i;
    echo outNumAsLink($n) . '×' . outNumAsLink($i) . '=' . outNumAsLink($result) . '<br>';
}

function outRow($n) {
    for ($i = 2; $i <= 9; $i++) {
        outRowLine($n, $i);
    }
}

function outTableForm() {
    global $content;
    echo '<table class="multiplication-table">';
    
    if ($content === null) {
        echo '<tr>';
        for ($n = 2; $n <= 9; $n++) {
            echo '<td class="mult-column">';
            outRow($n);
            echo '</td>';
        }
        echo '</tr>';
    } else {
        echo '<tr><td class="mult-column single">';
        outRow($content);
        echo '</td></tr>';
    }
    
    echo '</table>';
}

function outDivForm() {
    global $content;
    
    if ($content === null) {
        echo '<div class="mult-container">';
        for ($n = 2; $n <= 9; $n++) {
            echo '<div class="mult-column block">';
            outRow($n);
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="mult-container">';
        echo '<div class="mult-column single block">';
        outRow($content);
        echo '</div>';
        echo '</div>';
    }
}

function outMainMenu() {
    global $html_type, $content;
    
    echo '<nav class="main-menu">';
    echo '<a href="?html_type=TABLE';
    if ($content !== null) {
        echo '&content=' . $content;
    }

    echo '"';
    if ($html_type === 'TABLE') {
        echo ' class="selected_menu"';
    }
    echo '>Табличная верстка</a>';
    
    echo '<a href="?html_type=DIV';
    if ($content !== null) {
        echo '&content=' . $content;
    }
    echo '"';
    if ($html_type === 'DIV') {
        echo ' class="selected_menu"';
    }
    echo '>Блочная верстка</a>';
    
    echo '</nav>';
}

function outSideMenu() {
    global $content, $html_type;
    
    echo '<nav class="side-menu">';
    echo '<a href="?';
    if ($html_type === 'DIV') {
        echo 'html_type=DIV';
    }
    echo '"';
    if ($content === null) {
        echo ' class="selected_menu"';
    }
    echo '>Всё</a>';
    
    for ($i = 2; $i <= 9; $i++) {
        echo '<a href="?content=' . $i;
        if ($html_type === 'DIV') {
            echo '&html_type=DIV';
        }
        echo '"';
        if ($content !== null && (int)$content === $i) {
            echo ' class="selected_menu"';
        }
        echo '>' . $i . '</a>';
    }
    
    echo '</nav>';
}

function outFooterInfo() {
    global $html_type, $content;
    
    $layout_info = ($html_type === 'TABLE') ? 'Табличная верстка.' : 'Блочная верстка.';
    $content_info = ($content === null) ? 'Полная таблица умножения.' : 'Столбец таблицы умножения на ' . $content . '.';
    
    echo '<div class="footer-info">';
    echo $layout_info . ' ' . $content_info . ' ';
    echo 'Сформировано: ' . date('H:i:s');
    echo '</div>';
}
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

<main class="layout-container">
    <aside class="sidebar">
        <h1>ЛР №<?php echo $lab_number; ?></h1>
        <h2>Таблица умножения</h2>
        <?php outSideMenu(); ?>
    </aside>
    
    <section class="content-area">
        <?php outMainMenu(); ?>
        
        <div class="table-container">
            <?php
            if ($html_type === 'TABLE') {
                outTableForm();
            } else {
                outDivForm();
            }
            ?>
        </div>
    </section>
</main>

<footer>
    <?php outFooterInfo(); ?>
    <div class="timestamp"><?php echo date('d.m.Y'); ?></div>
</footer>
</body>
</html>