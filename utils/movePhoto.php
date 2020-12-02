<?php

function movePhoto()
{

    $photo = $_FILES['photo'];
    $nameImage = md5($photo['name'] . rand(0, 9999));
    $extension = substr($photo['name'], -4);
    $completeName = "{$nameImage}{$extension}";

    $image = $photo['tmp_name'];

    move_uploaded_file($image, "./uploads/{$completeName}");

    return $completeName;
}
