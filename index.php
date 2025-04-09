<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
  }
  // Включаем содержимое файла form.php.
  include('form.php');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.

// Проверяем ошибки.
$errors = FALSE;
if (empty($_POST['name']) || !ctype_alpha($_POST['name']) || strlen($_POST['name'])>150) {
  print('Заполните ФИО <br/>');
  $errors = TRUE;
} 
if (empty($_POST['phone']) || !is_numeric($_POST['phone']) || strlen($_POST['phone'])!=11){
  print('Напишите Телефон <br/>');
  $errors = TRUE;
}
if (empty($_POST['email']) || !preg_match('/@/', $_POST['email'])){
  print('Напишите e-mail <br/>');
  $errors = TRUE;
}
if (empty($_POST['birthdate']) || (!is_numeric($_POST['birthdate']) && !preg_match('/./', $_POST['birthdate']))) {
  print('Напишите дату рождения <br/>');
  $errors = TRUE;
}
if (empty($_POST['gender']) || ($_POST['gender']!='male' && $_POST['gender']!='female')){
  print('Укажите пол <br/>');
  $errors = TRUE;
}
if (empty($_POST['languages'])){
  print('Укажите любимый язык программирования <br/>');
  $errors = TRUE;
}
if (empty($_POST['bio']) || strlen($_POST['bio'])<10){
  print('Напишите Биографию <br/>');
  $errors = TRUE;
}
if (empty($_POST['contract_accepted'])){
  print('Ознакомьтесь с чем-то <br/>');
  $errors = TRUE;
}



if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  exit();
}

// Сохранение в базу данных.

$user = 'u68671'; // Заменить на ваш логин uXXXXX
$pass = '5868553'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68671', $user, $pass,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

// Подготовленный запрос. Не именованные метки.
try {
  $stmt = $db->prepare("INSERT INTO applications (full_name, phone, email, birthdate, gender, biography, agreement) VALUES (:name, :phone, :email, :birthdate, :gender, :bio, :contract)");
  $stmt->execute([
    ':name' => $_POST['name'],
    ':phone' => $_POST['phone'],
    ':email' => $_POST['email'],
    ':birthdate' => $_POST['birthdate'],
    ':gender' => $_POST['gender'],
    ':bio' => $_POST['bio'],
    ':contract' => isset($_POST['contract_accepted']) ? 1 : 0
]);
$applicationId = $pdo->lastInsertId();
$validLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
$selectedLanguages = array_intersect($_POST['languages'] ?? [], $validLanguages);
}
catch(PDOException $e){
  print('Error : ' . $e->getMessage());
  exit();
}

//  stmt - это "дескриптор состояния".
 
//  Именованные метки.
//$stmt = $db->prepare("INSERT INTO test (label,color) VALUES (:label,:color)");
//$stmt -> execute(['label'=>'perfect', 'color'=>'green']);
 
//Еще вариант
/*$stmt = $db->prepare("INSERT INTO users (firstname, lastname, email) VALUES (:firstname, :lastname, :email)");
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$firstname = "John";
$lastname = "Smith";
$email = "john@test.com";
$stmt->execute();
*/

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');



