<?php
$lab_number = "6";
$student_name = "Корепов Андрей Алексеевич";
$group = "241-352";
$logo_path = "logo.png";

$is_submitted = isset($_POST['task']);
$report_html = '';
$email_msg = '';
$version = 'browser';
$body_class = '';

if ($is_submitted) {
    $version = $_POST['version'] ?? 'browser';
    if ($version === 'print') $body_class = 'print-view';

    $fio = htmlspecialchars($_POST['fio'] ?? '');
    $grp = htmlspecialchars($_POST['group'] ?? '');
    $about = htmlspecialchars($_POST['about'] ?? '');
    
    $a = (float)str_replace(',', '.', $_POST['a'] ?? '0');
    $b = (float)str_replace(',', '.', $_POST['b'] ?? '0');
    $c = (float)str_replace(',', '.', $_POST['c'] ?? '0');
    $task = $_POST['task'] ?? 'mean';
    $user_ans_raw = str_replace(',', '.', $_POST['answer'] ?? '');
    $send_mail = isset($_POST['send_mail']);
    $email_to = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    $task_name = '';
    $result = 0;
    switch ($task) {
        case 'area_tri':
            $task_name = 'Площадь треугольника (формула Герона)';
            $p = ($a + $b + $c) / 2;
            $val = $p * ($p - $a) * ($p - $b) * ($p - $c);
            $result = $val > 0 ? round(sqrt($val), 4) : 'Недопустимые стороны';
            break;
        case 'perim_tri':
            $task_name = 'Периметр треугольника';
            $result = round($a + $b + $c, 4);
            break;
        case 'vol_paral':
            $task_name = 'Объем параллелепипеда';
            $result = round($a * $b * $c, 4);
            break;
        case 'mean':
            $task_name = 'Среднее арифметическое';
            $result = round(($a + $b + $c) / 3, 4);
            break;
        case 'hypotenuse':
            $task_name = 'Гипотенуза прямоугольного треугольника';
            $result = round(sqrt(pow($a, 2) + pow($b, 2)), 4);
            break;
        case 'quad_discriminant':
            $task_name = 'Дискриминант (A·x² + B·x + C)';
            $result = round(pow($b, 2) - 4 * $a * $c, 4);
            break;
        default:
            $task_name = 'Среднее арифметическое';
            $result = round(($a + $b + $c) / 3, 4);
    }

    $user_ans = $user_ans_raw !== '' ? (float)$user_ans_raw : null;
    if ($user_ans === null) {
        $test_msg = '<span class="msg-info">Задача самостоятельно решена не была</span>';
    } elseif (abs($user_ans - (is_numeric($result) ? $result : 0)) < 0.0001) {
        $test_msg = '<span class="msg-success">ТЕСТ ПРОЙДЕН</span>';
    } else {
        $test_msg = '<span class="msg-error">ОШИБКА: ТЕСТ НЕ ПРОЙДЕН!</span>';
    }

    $ans_display = $user_ans !== null ? $user_ans : 'Не введен';
    $report_html = "<div class='report-block'>";
    $report_html .= "<h3>Отчет о тестировании</h3>";
    $report_html .= "<p><b>ФИО:</b> $fio | <b>Группа:</b> $grp</p>";
    if ($about) $report_html .= "<p><b>О себе:</b> $about</p>";
    $report_html .= "<p><b>Тип задачи:</b> $task_name</p>";
    $report_html .= "<p><b>Входные данные:</b> A = $a, B = $b, C = $c</p>";
    $report_html .= "<p><b>Ваш ответ:</b> $ans_display</p>";
    $report_html .= "<p><b>Программный результат:</b> $result</p>";
    $report_html .= "<p><b>Итог:</b> $test_msg</p>";
    $report_html .= "</div>";

    if ($send_mail && $email_to) {
        $email_body = "ФИО: $fio\nГруппа: $grp\nО себе: $about\nЗадача: $task_name\nA: $a, B: $b, C: $c\nВаш ответ: $ans_display\nРезультат: $result\nИтог: " . strip_tags($test_msg);
        $headers = "From: lab6@mospoly.ru\r\nContent-Type: text/plain; charset=utf-8\r\n";
        if (@mail($email_to, "Результат тестирования ЛР№$lab_number", $email_body, $headers)) {
            $email_msg = "<p class='success-msg'>Результаты теста были автоматически отправлены на e-mail $email_to</p>";
        } else {
            $email_msg = "<p class='warning-msg'>Не удалось отправить письмо (не настроен SMTP-сервер).</p>";
        }
    }
}

$rand_val = fn() => round(mt_rand(0, 10000) / 100, 2);
$init_a = $rand_val();
$init_b = $rand_val();
$init_c = $rand_val();

$saved_fio = $is_submitted ? ($_POST['fio'] ?? '') : htmlspecialchars($_GET['FIO'] ?? '');
$saved_group = $is_submitted ? ($_POST['group'] ?? '') : htmlspecialchars($_GET['GR'] ?? '');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР №<?php echo $lab_number; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo $body_class; ?>">
    <header>
        <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo" class="logo" onerror="this.style.display='none'">
        <div class="header-info">
            <span class="student-info"><?php echo $student_name; ?> | Группа: <?php echo $group; ?></span>
        </div>
    </header>
    <main class="layout-container">
        <aside class="sidebar">
            <h1>ЛР №<?php echo $lab_number; ?></h1>
            <h2>Тест математических знаний</h2>
            <p class="no-print" style="font-size: 14px; color: #555; margin-top: 20px;">
                <?php echo $is_submitted ? 'Результаты готовы.' : 'Заполните форму ниже.'; ?>
            </p>
        </aside>
        <section class="content-area">
            <?php if (!$is_submitted): ?>
                <h1 class="no-print">Форма тестирования</h1>
                <form name="lab6_form" method="post" action="" class="lab-form">
                    <div class="form-group">
                        <label for="fio">ФИО:</label>
                        <input type="text" id="fio" name="fio" value="<?php echo $saved_fio; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="group">Номер группы:</label>
                        <input type="text" id="group" name="group" value="<?php echo $saved_group; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="a">Значение А:</label>
                        <input type="text" id="a" name="a" value="<?php echo $init_a; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="b">Значение В:</label>
                        <input type="text" id="b" name="b" value="<?php echo $init_b; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="c">Значение С:</label>
                        <input type="text" id="c" name="c" value="<?php echo $init_c; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="task">Выберите задачу:</label>
                        <select id="task" name="task" required>
                            <option value="area_tri">Площадь треугольника</option>
                            <option value="perim_tri">Периметр треугольника</option>
                            <option value="vol_paral">Объем параллелепипеда</option>
                            <option value="mean" selected>Среднее арифметическое</option>
                            <option value="hypotenuse">Гипотенуза прямоугольного треугольника</option>
                            <option value="quad_discriminant">Дискриминант квадратного уравнения</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="answer">Ваш ответ:</label>
                        <input type="text" id="answer" name="answer" placeholder="Введите число или оставьте пустым">
                    </div>
                    <div class="form-group checkbox-group">
                        <label for="send_mail">Отправить результат теста по е-майл:</label>
                        <input type="checkbox" id="send_mail" name="send_mail" onclick="toggleEmailField()">
                    </div>
                    <div id="email-field-container" class="form-group">
                        <label for="email">Ваш е-майл:</label>
                        <input type="email" id="email" name="email" placeholder="example@mail.ru">
                    </div>
                    <div class="form-group">
                        <label for="about">Немного о себе:</label>
                        <textarea id="about" name="about"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="version">Версия вывода:</label>
                        <select id="version" name="version">
                            <option value="browser">Версия для просмотра в браузере</option>
                            <option value="print">Версия для печати</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label></label>
                        <a href="#" class="submit-link" onclick="document.lab6_form.submit(); return false;">Проверить</a>
                    </div>
                </form>
                <script>
                    function toggleEmailField() {
                        const container = document.getElementById('email-field-container');
                        const checkbox = document.getElementById('send_mail');
                        container.style.display = checkbox.checked ? 'flex' : 'none';
                    }
                    window.onload = toggleEmailField;
                </script>
            <?php else: ?>
                <h1>Результаты тестирования</h1>
                <?php echo $report_html; ?>
                <?php echo $email_msg; ?>
                <?php if ($version === 'browser'): ?>
                    <a href="?FIO=<?php echo urlencode($_POST['fio'] ?? ''); ?>&GR=<?php echo urlencode($_POST['group'] ?? ''); ?>" id="back_button">Повторить тест</a>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <div class="footer-info">ЛР №<?php echo $lab_number; ?> | Тест математических знаний</div>
        <div class="timestamp"><?php echo date('d.m.Y H:i:s'); ?></div>
    </footer>
</body>
</html>