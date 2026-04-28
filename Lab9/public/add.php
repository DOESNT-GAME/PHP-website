<?php
$message = '';
if (isset($_POST['submit_add'])) {
    $mysqli = mysqli_connect('localhost', 'root', '', 'lab9_db');
    if (mysqli_connect_errno()) {
        $message = '<div class="msg-error">Ошибка подключения к БД</div>';
    } else {
        mysqli_set_charset($mysqli, 'utf8');
        $surname = mysqli_real_escape_string($mysqli, $_POST['surname']);
        $name = mysqli_real_escape_string($mysqli, $_POST['name']);
        $patronymic = mysqli_real_escape_string($mysqli, $_POST['patronymic']);
        $gender = $_POST['gender'];
        $birthdate = $_POST['birthdate'] ?: NULL;
        $phone = mysqli_real_escape_string($mysqli, $_POST['phone']);
        $address = mysqli_real_escape_string($mysqli, $_POST['address']);
        $email = mysqli_real_escape_string($mysqli, $_POST['email']);
        $comment = mysqli_real_escape_string($mysqli, $_POST['comment']);

        $sql = "INSERT INTO friends (surname, name, patronymic, gender, birthdate, phone, address, email, comment) 
                VALUES ('$surname', '$name', '$patronymic', '$gender', " . ($birthdate ? "'$birthdate'" : "NULL") . ", '$phone', '$address', '$email', '$comment')";
        
        if (mysqli_query($mysqli, $sql)) {
            $message = '<div class="msg-success">Запись добавлена</div>';
        } else {
            $message = '<div class="msg-error">Ошибка: запись не добавлена</div>';
        }
    }
}
?>
<form method="post" action="?p=add" class="analysis-form">
    <input type="text" name="surname" placeholder="Фамилия *" required>
    <input type="text" name="name" placeholder="Имя *" required>
    <input type="text" name="patronymic" placeholder="Отчество">
    <select name="gender" required>
        <option value="">Выберите пол *</option>
        <option value="male">Мужской</option>
        <option value="female">Женский</option>
    </select>
    <input type="date" name="birthdate">
    <input type="text" name="phone" placeholder="Телефон">
    <input type="text" name="address" placeholder="Адрес">
    <input type="email" name="email" placeholder="E-mail">
    <textarea name="comment" placeholder="Комментарий" rows="3"></textarea>
    <button type="submit" name="submit_add" class="analyze-btn">Добавить запись</button>
</form>
<?= $message ?>