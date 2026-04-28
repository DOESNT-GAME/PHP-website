<?php
function get_menu_html() {
    $_GET['p'] = in_array($_GET['p'] ?? '', ['viewer', 'add', 'edit', 'delete']) ? $_GET['p'] : 'viewer';
    $_GET['sort'] = in_array($_GET['sort'] ?? '', ['byid', 'fam', 'birth']) ? $_GET['sort'] : 'byid';
    $html = '<nav class="main-menu">';
    $html .= '<a href="?p=viewer" class="' . ($_GET['p'] == 'viewer' ? 'selected' : '') . '">Просмотр</a>';
    $html .= '<a href="?p=add" class="' . ($_GET['p'] == 'add' ? 'selected' : '') . '">Добавление записи</a>';
    $html .= '<a href="?p=edit" class="' . ($_GET['p'] == 'edit' ? 'selected' : '') . '">Редактирование записи</a>';
    $html .= '<a href="?p=delete" class="' . ($_GET['p'] == 'delete' ? 'selected' : '') . '">Удаление записи</a>';
    $html .= '</nav>';
    if ($_GET['p'] == 'viewer') {
        $html .= '<nav class="side-menu submenu">';
        $html .= '<a href="?p=viewer&sort=byid" class="' . ($_GET['sort'] == 'byid' ? 'selected' : '') . '">По умолчанию</a>';
        $html .= '<a href="?p=viewer&sort=fam" class="' . ($_GET['sort'] == 'fam' ? 'selected' : '') . '">По фамилии</a>';
        $html .= '<a href="?p=viewer&sort=birth" class="' . ($_GET['sort'] == 'birth' ? 'selected' : '') . '">По дате рождения</a>';
        $html .= '</nav>';
    }
    return $html;
}
?>