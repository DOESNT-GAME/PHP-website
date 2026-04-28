<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'lab9_db');
if (mysqli_connect_errno()) die('<div class="msg-error">Ошибка подключения к БД</div>');
mysqli_set_charset($mysqli, 'utf8');

if (isset($_POST['submit_edit'])) {
    $id = (int)$_POST['id'];
    $surname = mysqli_real_escape_string($mysqli, $_POST['surname']);
    $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    $patronymic = mysqli_real_escape_string($mysqli, $_POST['patronymic']);
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'] ?: NULL;
    $phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
    $address = mysqli_real_escape_string($mysqli, $_POST['address']);
    $email = mysqli_real_escape_string($mysqli, $_POST['email']);
    $comment = mysqli_real_escape_string($mysqli, $_POST['comment']);

    $sql = "UPDATE friends SET surname='$surname', name='$name', patronymic='$patronymic', gender='$gender', 
            birthdate=" . ($birthdate ? "'$birthdate'" : "NULL") . ", phone='$phone', address='$address', email='$email', comment='$comment' 
            WHERE id=$id";
    mysqli_query($mysqli, $sql);
    $_GET['id'] = $id;
}

$current_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($current_id <= 0) {
    $first = mysqli_query($mysqli, "SELECT id FROM friends ORDER BY surname, name LIMIT 1");
    if ($r = mysqli_fetch_assoc($first)) $current_id = (int)$r['id'];
}

$current_row = [];
if ($current_id > 0) {
    $res = mysqli_query($mysqli, "SELECT * FROM friends WHERE id=$current_id LIMIT 1");
    $current_row = mysqli_fetch_assoc($res);
}

$list_res = mysqli_query($mysqli, "SELECT id, surname, name FROM friends ORDER BY surname, name");
?>

<div class="edit-links">
<?php while ($row = mysqli_fetch_assoc($list_res)): ?>
    <?php if ((int)$row['id'] == $current_id): ?>
        <span class="current-edit-link"><?= htmlspecialchars($row['surname']) ?> <?= htmlspecialchars($row['name']) ?></span>
    <?php else: ?>
        <a href="?p=edit&id=<?= $row['id'] ?>" class="edit-link"><?= htmlspecialchars($row['surname']) ?> <?= htmlspecialchars($row['name']) ?></a>
    <?php endif; ?>
<?php endwhile; ?>
</div>

<?php if ($current_row): ?>
<form method="post" action="?p=edit" class="analysis-form">
    <input type="hidden" name="id" value="<?= $current_row['id'] ?>">
    <input type="text" name="surname" value="<?= htmlspecialchars($current_row['surname']) ?>" required>
    <input type="text" name="name" value="<?= htmlspecialchars($current_row['name']) ?>" required>
    <input type="text" name="patronymic" value="<?= htmlspecialchars($current_row['patronymic']) ?>">
    <select name="gender" required>
        <option value="male" <?= $current_row['gender']=='male'?'selected':'' ?>>Мужской</option>
        <option value="female" <?= $current_row['gender']=='female'?'selected':'' ?>>Женский</option>
    </select>
    <input type="date" name="birthdate" value="<?= htmlspecialchars($current_row['birthdate']) ?>">
    <input type="text" name="phone" value="<?= htmlspecialchars($current_row['phone']) ?>">
    <input type="text" name="address" value="<?= htmlspecialchars($current_row['address']) ?>">
    <input type="email" name="email" value="<?= htmlspecialchars($current_row['email']) ?>">
    <textarea name="comment" rows="3"><?= htmlspecialchars($current_row['comment']) ?></textarea>
    <button type="submit" name="submit_edit" class="analyze-btn">Изменить запись</button>
</form>
<?php else: ?>
    <p class="src_error">Записей в базе данных нет.</p>
<?php endif; ?>