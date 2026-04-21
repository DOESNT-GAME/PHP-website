<?php
$text_raw = isset($_POST['data']) ? $_POST['data'] : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР №8 - Результаты</title>
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
            <h1>ЛР №8</h1>
            <h2>Результаты анализа</h2>
            <nav class="side-menu">
                <a href="index.html">Ввод текста</a>
                <a href="result.php" class="selected_menu">Результаты</a>
            </nav>
        </aside>
        <section class="content-area">
            <h1>Анализ введенного текста</h1>
<?php
if (empty(trim($text_raw))) {
    echo '<div class="src_error">Нет текста для анализа</div>';
} else {
    $text = iconv('UTF-8', 'CP1251//IGNORE', $text_raw);
    echo '<div class="src_text">' . htmlspecialchars(iconv('CP1251', 'UTF-8', $text)) . '</div>';
    if (function_exists('test_it')){
        test_it($text);
    };
}
?>
            <a href="index.html" class="another-analysis-btn">Другой анализ</a>
        </section>
    </main>
    <footer>
        <div class="footer-info">Лабораторная работа №8. Анализ текстовой информации.</div>
        <div class="timestamp" id="current-date"></div>
    </footer>
    <script>
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('ru-RU');
    </script>
</body>
</html>

<?php
function test_it($text) {
    $total_chars = strlen($text);
    $digits = ['0'=>1,'1'=>1,'2'=>1,'3'=>1,'4'=>1,'5'=>1,'6'=>1,'7'=>1,'8'=>1,'9'=>1];
    $punct = ['.'=>1,','=>1,'!'=>1,'?'=>1,';'=>1,':'=>1,'-'=>1,'—'=>1,'('=>1,')'=>1,'['=>1,']'=>1,'{'=>1,'}'=>1,'"'=>1,"'"=>1,'»'=>1,'«'=>1,'…'=>1];
    $letters = 0; $lower = 0; $upper = 0; $digit_count = 0; $punct_count = 0;
    $char_freq = [];
    $words = [];
    $word = '';
    
    $len = strlen($text);
    for ($i = 0; $i < $len; $i++) {
        $ch = $text[$i];
        $ch_lower = strtolower($ch);
        $char_freq[$ch_lower] = ($char_freq[$ch_lower] ?? 0) + 1;
        if (isset($digits[$ch])) {
            $digit_count++;
        } elseif (isset($punct[$ch])) {
            $punct_count++;
            if ($word !== '') {
                $words[$word] = ($words[$word] ?? 0) + 1;
                $word = '';
            }
        } elseif ($ch === ' ' || $ch === "\n" || $ch === "\r" || $ch === "\t") {
            if ($word !== '') {
                $words[$word] = ($words[$word] ?? 0) + 1;
                $word = '';
            }
        } else {
            $letters++;
            if ($ch === $ch_lower) {
                $lower++;
            } else {
                $upper++;
            }
            $word .= $ch;
        }
    }
    if ($word !== '') {
        $words[$word] = ($words[$word] ?? 0) + 1;
    }
    ksort($words, SORT_STRING);
    
    echo '<table class="result-table">';
    echo '<tr><td>Количество символов</td><td>' . $total_chars . '</td></tr>';
    echo '<tr><td>Количество букв</td><td>' . $letters . '</td></tr>';
    echo '<tr><td>Строчные буквы</td><td>' . $lower . '</td></tr>';
    echo '<tr><td>Заглавные буквы</td><td>' . $upper . '</td></tr>';
    echo '<tr><td>Знаки препинания</td><td>' . $punct_count . '</td></tr>';
    echo '<tr><td>Цифры</td><td>' . $digit_count . '</td></tr>';
    echo '<tr><td>Количество слов</td><td>' . count($words) . '</td></tr>';
    

    echo '<tr><td>Вхождения каждого символа</td><td>';
    echo '<table class="inner-table">';
    foreach ($char_freq as $c => $cnt) {
        $display = ($c === ' ') ? '(пробел)' : htmlspecialchars(iconv('CP1251', 'UTF-8', $c));
        echo "<tr><td>{$display}</td><td>{$cnt}</td></tr>";
    }
    echo '</table></td></tr>';

    echo '<tr><td>Вхождения слов</td><td>';
    echo '<table class="inner-table">';
    foreach ($words as $w => $cnt) {
        echo '<tr><td>' . htmlspecialchars(iconv('CP1251', 'UTF-8', $w)) . "</td><td>{$cnt}</td></tr>";
    }
    echo '</table></td></tr>';
    
    echo '</table>';
}
?>