<?php
function getFriendsList($type, $page) {
    $mysqli = mysqli_connect('localhost', 'root', '', 'lab9_db');
    if (mysqli_connect_errno()) return '<div class="msg-error">Ошибка подключения к БД: ' . mysqli_connect_error() . '</div>';

    mysqli_set_charset($mysqli, 'utf8');

    $order = 'id ASC';
    if ($type == 'fam') $order = 'surname ASC, name ASC';
    elseif ($type == 'birth') $order = 'birthdate ASC';

    $count_res = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM friends");
    if (!$count_res) return '<div class="msg-error">Ошибка запроса</div>';
    $row_count = mysqli_fetch_assoc($count_res);
    $total_records = (int)$row_count['total'];
    $pages_count = ceil($total_records / 10);
    
    if ($page < 0) $page = 0;
    if ($page >= $pages_count) $page = max(0, $pages_count - 1);

    $offset = $page * 10;
    $sql = "SELECT * FROM friends ORDER BY $order LIMIT $offset, 10";
    $result = mysqli_query($mysqli, $sql);
    if (!$result) return '<div class="msg-error">Ошибка выполнения запроса</div>';

    $html = '<table class="result-table">';
    $html .= '<thead><tr><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Пол</th><th>Дата рождения</th><th>Телефон</th><th>Адрес</th><th>E-mail</th><th>Комментарий</th></tr></thead><tbody>';
    
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['surname'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['name'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['patronymic'] ?? '') . '</td>';
        $html .= '<td>' . ($row['gender'] == 'male' ? 'Муж.' : 'Жен.') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['birthdate'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['phone'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['address'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['email'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['comment'] ?? '') . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    if ($pages_count > 1) {
        $html .= '<div class="pagination">';
        for ($i = 0; $i < $pages_count; $i++) {
            if ($i == $page) {
                $html .= '<span class="current-page">' . ($i + 1) . '</span>';
            } else {
                $html .= '<a href="?p=viewer&sort=' . htmlspecialchars($type) . '&page=' . $i . '" class="page-link">' . ($i + 1) . '</a>';
            }
        }
        $html .= '</div>';
    }
    return $html;
}
?>