<?php
/* Функции для работы с БД */

function dbConnect ()
{
    $link=mysqli_connect(HOST, USER, PASSWD, BASENAME);
    if (!$link) {
        die (mysqli_connect_error($link));
    }
    return $link;
}

function dbResult ($link, $sql)
{
    $res = mysqli_query($link, $sql);
    if (!$res) {
        die (mysqli_error($link));
    } 
    return $res;
}