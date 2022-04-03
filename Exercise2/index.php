<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css"
          integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn"
          crossorigin="anonymous">
    <title>Задание 2</title>
</head>
<body>
<?php
$fullFilename = __DIR__ . '/table.txt';
$f = fopen($fullFilename, "r");
$data = [];
while ($row = fgetcsv($f, 0, "\t")) {
    $id = $row[0]; 
    $name = $row[1];
    $price = $row[2];
    $data[] = [
        'id' => $id,
        'name' => $name,
        'price' => $price
    ];
}
fclose($f);
?>
<div class="container">
<h1 class="text-center">Задание 2</h1>
<table class="table border border-dark">
    <thead class="thead-dark">
        <tr>
          <th >Номер</th>
          <th >Название</th>
          <th >Цена</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row): ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= $row['name']; ?></td>
          <td><?= $row['price']; ?></td>
       </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</body>
</html>