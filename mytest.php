<?php
/*
* Запись сообщения и сопутствующих параметров в Базу Данных
*/
function dbConnect ()
{
    $link=mysqli_connect(HOST, USER, PASSWD, BASENAME);
    if (!$link) {
        die (mysqli_connect_error($link));
    }
    return $link;
}

$res['ok']='No'; //Флаг для AJAX

//Дополнительная проверка на заполнение обязательных полей
if (!isset($_POST['username']) ||
    !isset($_POST['usermail']) ||
    !isset($_POST['message'])) {
    die();
}
//Дополнительная проверка имени
$reg = "/^[a-z0-9-_]{2,50}$/i";
if (!preg_match($reg, $_POST['username'])) {
    die();
}

//Дополнительная проверка адреса электронной почты. HEREDOC нельзя сдвигать
$reg = <<< LongRegExp
/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()
[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
LongRegExp;
if (!preg_match($reg, $_POST['usermail'])) {
    die();
}
//Сохраняем правильно введенные данные в таблицу
$link1 = dbConnect();
$message = $_POST['message'];
$timestamp = date("Y-m-d H:i:s");
$ip = (string) $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER["HTTP_USER_AGENT"];
$user = $_POST['username']; //пока так
//Проверяем, нет ли уже такого Юзера, если есть, берем его id
$email = $_POST['usermail']; //пока так
//Если Юзер есть, проверяем его email, если такой есть, берем id_email

$sql="INSERT INTO messages (message, currenttime, ip, browser, id_user, id_email) 
      VALUES ('$message', '$timestamp', '$ip', '$browser', 1, 2)"; //пока так
echo "$sql"."<br>";
$result=mysqli_query($link1, $sql);
if (!$result) {
    die (mysqli_error($result));
}
mysqli_close ($link1);
/*
* Здесь следовало бы обработать полученные значения полей формы
* с помощью функций htmlspecialchars() и strip_tags() но, поскольку
* они не сохраняются в файл и в задании такого требования нет,
* я делать этого не буду */



$res['ok']='Y'; //Успех
echo json_encode($res);
