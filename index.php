<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
        integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn"
        crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-2.2.4.min.js" 
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>
  <title>Тестовое задание. Гостевая книга</title>
</head>

<?php
//Подключаем константы
require_once 'partials/constants.php';
//Подключаем функции для открытия/записи в БД
require_once 'partials/dbfunctions.php';




//Запись в гостевую книгу
if (!empty($_POST)) {
    //Дополнительная проверка на заполнение обязательных полей
    if (!isset($_POST['username']) ||
        !isset($_POST['usermail']) ||
        !isset($_POST['message'])) {
        die("Обязательные поля не заполнены!");
    }
    //Дополнительная проверка имени
    $reg = "/^[a-zA-Z0-9-_]{2,50}$/i";
    if (!preg_match($reg, $_POST['username'])) {
        die("Неправильный формат имени пользователя!");
    }

    //Дополнительная проверка адреса электронной почты. HEREDOC нельзя сдвигать
$reg = <<< LongRegExp
/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()
[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
LongRegExp;
    if (!preg_match($reg, $_POST['usermail'])) {
        die("Неправильный формат адреса электронной почты!");
    }
    //Сохраняем правильно введенные данные в таблицу
    $link1 = dbConnect();
    $writeMessage = false; //Флаг возможности записи сообщения в гостевую книгу
    if (!empty($_POST['homepage'])) {
        $homepage = trim(strip_tags($_POST['homepage']));
    }
    $message = trim(strip_tags($_POST['message']));
    $timestamp = date("Y-m-d H:i:s");
    $ip = (string) $_SERVER['REMOTE_ADDR'];
    $browser = $_SERVER["HTTP_USER_AGENT"];
    $user = trim(strip_tags($_POST['username'])); //пока так
    $email = trim(strip_tags($_POST['usermail'])); //пока так
    //Проверяем, нет ли уже такого Юзера, если есть, берем его id
    //Если Юзер есть, проверяем его email
    $result = dbResult ($link1, "SELECT * FROM users");
    $epms=[];
    while ($row = mysqli_fetch_assoc ($result)) {
        $epms[] = $row;
    }
    if (empty($epms)) {
        $sql = "INSERT INTO users (user_name, email, homepage) 
                VALUES ('$user', '$email', '$homepage')"; echo "2 жопа";
        $result = dbResult($link1, $sql);
        $userId = 1;
        $writeMessage = true;
    } else {
        foreach ($epms as $row) {
            if ($user == $row['user_name']) {
                $userId = (int) $row['id']; var_dump($userId);
                //Проверка почты
                if ($email != $row['email']) {
                    $sql = "SELECT email FROM users WHERE email = '$email'";
                    echo "5 жопа"; echo $sql;
                    $result = dbResult ($link1, $sql);
                    $row = mysqli_fetch_assoc($result); var_dump($row);
                    if (!empty($row)) {
                        echo "Адрес принадлежит другому пользователю! ";
                    } else {
                        //Заменяем старый адрес новым
                        $sql = "UPDATE users SET email = '$email' WHERE id = $userId";
                        echo "3 жопа"; echo $sql;
                        $result = dbResult ($link1, $sql);
                        $writeMessage = true;
                        //Если есть страница, пишем и ее, какой бы она ни была
                        if (isset($homepage)) {
                            $sql = "UPDATE users SET homepage = '$homepage' WHERE id = $userId";
                            $result = dbResult ($link1, $sql);
                        }
                    }
                } else {
                    //Если есть страница, пишем и ее, какой бы она ни была 
                    //Разрешаем запись в message, даже если пользователь оставил поле страницы пустым
                    $writeMessage = true;
                    if (isset($homepage)) {
                        $sql = "UPDATE users SET homepage = '$homepage' WHERE id = $userId";
                        $result = dbResult ($link1, $sql);
                    }
                }
            }
        }
        if (!isset($userId)) {
            $sql = "INSERT INTO users (user_name, email, homepage) 
                    VALUES ('$user', '$email', '$homepage')"; echo "жопа";
            $result = dbResult ($link1, $sql);
            $sql = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
            $result = dbResult ($link1, $sql);
            $row = mysqli_fetch_assoc($result);
            var_dump($row);
            $userId = (int) $row['id']; var_dump($userId);
            $writeMessage = true;
        }
    }
    if ($writeMessage) {
        $sql="INSERT INTO messages (message, currenttime, ip, browser, id_user, email)
              VALUES ('$message', '$timestamp', '$ip', '$browser', $userId, '$email')";
        echo "$sql"."<br>";
        $result=dbResult ($link1, $sql);
    }

    mysqli_close ($link1);
}
?>

<body>
  <div class="container">
    <h1 class="text-center">Гостевая книга</h1>
<?php 
//Вывод гостевой книги

$link2 = dbConnect();
$sql = "SELECT messages.id, messages.currenttime, messages.message, 
        messages.email, users.user_name
        FROM messages
        LEFT JOIN users ON users.id = messages.id_user";
$result = dbResult ($link2, $sql);
$epms=[];
while ($row = mysqli_fetch_assoc ($result)) {
    $epms[] = $row;
}
mysqli_close ($link2);

?>
    <table id="Output" class="table border border-dark">
      <thead class="thead-dark">
        <tr>
          <th id="uId" class="align-top">Номер</th>
          <th id="uName" class="align-top">Имя пользователя</th>
          <th id="uEmail" class="align-top">E-mail</th>
          <th id="uTime" class="align-top">Время добавления</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($epms as $row): ?>
        <tr class="table-sm">
          <td><?= $row['id']; ?></td>
          <td><?= $row['user_name']; ?></td>
          <td><?= $row['email']; ?></td>
          <td><?= $row['currenttime']; ?></td>
        </tr>
        <tr class="table-sm">
          <td class="table-dark" colspan="4">Сообщение:</td>
        </tr>
        <tr class="table-sm table-active">
          <td colspan="4"><?php
              $outMessage = $row['message'];
              $order = array("\r\n", "\n", "\r");
              $replace = '<br>';
              echo str_replace($order, $replace, $outMessage);
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h2>Добавить новое сообщение</h2>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return callWriteFiles(this);">
      <div class="form-group">
        <label for="username">Имя пользователя (от 2 до 50 символов)*</label>
        <input type="text" class="form-control" name="username" id="username" placeholder="Введите имя (цифры и буквы латинского алфавита)" required>
      </div>
      <div class="form-group">
        <label for="usermail">E-mail*</label>
        <!-- type="email" не поддерживает кириллицу -->
        <input type="text" class="form-control" name="usermail" id="usermail" placeholder="Введите адрес электронной почты" required>
      </div>
      <div class="form-group">
        <label for="homepage">Домашняя страница</label>
        <input type="text" class="form-control" name="homepage" id="homepage" placeholder="Введите адрес домашней страницы (если есть)">
      </div>
      <div class="form-group">
        <label for="message">Сообщение*</label>
        <textarea class="form-control" rows="3" name="message" id="message" placeholder="Введите сообщение (HTML тэги недопустимы)"></textarea>
      </div>            
      <button type="submit" class="btn btn-primary btn-lg">Запись в книгу</button> 
    </form>
  </div>

<script>
function callWriteFiles(myForm) {
let regName = /^[a-zA-Z0-9-_]{2,50}$/i
//Регулярка почты учитывает русские символы. Длиннее 80 символов, но разрывать нецелесообразно 
let regEmail = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

    //Проверка заполнения полей
    if (myForm.username.value == "" ||
        myForm.usermail.value == "" ||
        myForm.message.value == "") { 
        alert("Необходимо заполнить все обязательные поля");
        return false; 
    }
    //Проверка имени пользователя
    if (regName.test(myForm.username.value) == false) {
        alert("Неверное имя пользователя!");
        return false; 
    }
    //Проверка адреса электронной почты
    if (regEmail.test(myForm.usermail.value) == false) {
        alert("Неверный адрес электронной почты!");
        return false; 
    }
}
</script>

<script>
$('#username').on({
    keypress: function() {
        $('#username').css('background-color', 'yellow');
    }
});

</script>

<script>
$("#Output th").on({
    mouseover: function() {
        $(this).addClass("bg-info");
    },
    mouseleave: function() {
        $(this).removeClass("bg-info");
        $(this).removeClass("bg-success");
    },
    click: function() {
        $(this).addClass("bg-success");
    }
});
</script>
</body>
</html>
