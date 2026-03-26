<?php
$logo_path = "logo.png";
$student_name = "Корепов Андрей Алексеевич";
$group = "241-352";
$lab_number = "4";

$columns_count = 4;

$structures = array(
    "А*Б*В*Г#Д*Е*Ё*Ж",
    "А*Б#В*Г",
    "А*Б*В*Г*Д#Е*Ё*Ж*З*И",
    "А#Б#В",
    "",
    "#",
    "*#*",
    "А**Б#В*Г*Д",
    "А*Б*В*Г",
    "А*Б*В*Г#Д*Е*Ё*Ж#З*И*Й*К"
);

function getTR($data, $requiredCols) {
    if (trim($data) === '') {
        return '';
    }

    $cells = explode('*', $data);
    $ret = '<tr>';
    
    for ($i = 0; $i < $requiredCols; $i++) {
        if ($i < count($cells)) {
            $content = trim($cells[$i]);
            $ret .= '<td>' . htmlspecialchars($content) . '</td>';
        } else {
            $ret .= '<td></td>';
        }
    }
    $ret .= '</tr>';
    return $ret;
}

function outTable($structure, $tableNumber, $requiredCols) {
    echo "<h2>Таблица №$tableNumber</h2>";

    if ($structure === '') {
        echo "<p>В таблице нет строк</p>";
        return;
    }

    $rows = explode('#', $structure);
    $rowsHtml = '';
    $hasRowsWithCells = false;

    foreach ($rows as $row) {
        $trHtml = getTR($row, $requiredCols);
        if ($trHtml !== '') {
            $rowsHtml .= $trHtml;
            $hasRowsWithCells = true;
        }
    }
    if (!$hasRowsWithCells) {
        echo "<p>В таблице нет строк с ячейками</p>";
        return;
    }
    echo "<table>";
    echo $rowsHtml;
    echo "</table>";
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
<main>
    <h1>Лабораторная работа №<?php echo $lab_number; ?></h1>
    <h2>Пользовательские функции. Вывод таблиц.</h2>
    
    <div class="func-container">
        <div class="func-block">
            <p><strong>Требуемое число колонок:</strong> <?php echo $columns_count; ?></p>
            <p><strong>Количество таблиц:</strong> <?php echo count($structures); ?></p>
        </div>
    </div>

    <?php
    if ($columns_count <= 0) {
        echo "<h2>Ошибка</h2>";
        echo "<p>Неправильное число колонок</p>";
    } else {
        for ($i = 0; $i < count($structures); $i++) {
            outTable($structures[$i], $i + 1, $columns_count);
        }
    }
    ?>
</main>
<footer>
    <div class="timestamp">Сформировано: <?php
    date_default_timezone_set('Europe/Moscow');
    echo date('H:i:s'); ?></div>
</footer>
</body>
</html>