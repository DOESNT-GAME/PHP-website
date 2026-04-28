<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'lab9_db');
if (mysqli_connect_errno()) die('<div class="msg-error">Ошибка подключения к БД</div>');
mysqli_set_charset($mysqli, 'utf8');

$message = '';
if (isset($_GET['del_id'])) {
    $id = (int)$_GET['del_id'];
    $check = mysqli_query($mysqli, "SELECT surname FROM friends WHERE id=$id LIMIT 1");
    $row_check = mysqli_fetch_assoc($check);
    if ($row_check) {
        mysqli_query($mysqli, "DELETE FROM friends WHERE id=$id");
        $message = '<div class="msg-success">Запись с фамилией ' . htmlspecialchars($row_check['surname']) . ' удалена</div>';
    }
}

$list_res = mysqli_query($mysqli, "SELECT id, surname, name, patronymic FROM friends ORDER BY surname, name");
?>

<?= $message ?>
<div class="delete-links">
<?php while ($row = mysqli_fetch_assoc($list_res)): ?>
    <?php
    $init_name = mb_substr($row['name'], 0, 1) . '.';
    $init_pat = $row['patronymic'] ? mb_substr($row['patronymic'], 0, 1) . '.' : '';
    ?>
    <a href="?p=delete&del_id=<?= $row['id'] ?>" class="delete-link">
        <?= htmlspecialchars($row['surname']) ?> <?= $init_name ?> <?= $init_pat ?>
    </a>
<?php endwhile; ?>
</div>