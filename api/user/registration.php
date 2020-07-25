<?php
$link = mysqli_connect(
    'localhost',
    'root',
    '',
    'socialmedia'
);
if($link !== false){
    $data = json_decode(file_get_contents('php://input'), true);
    foreach($data as &$item){
        $item = htmlspecialchars($item);
    }
    $query = "insert into users(" . implode(',', array_keys($data)) . ") values('" . implode("','", array_values($data)) . "');";
    $res = mysqli_query($link, $query);

    $query = "select count(*) from users;";
    $countDb = mysqli_query($link, $query);
    if($res && $count = mysqli_fetch_assoc($countDb)){
        $query = "insert into news(`title`, `desc`, `author`) 
                              values(
                                  'Нас стало больше!',
                                  'Ура! Нас теперь {$count['count(*)']} на нашем дружном вирусном портале!',
                                  'Команда Bacterium, Inc'
                              )";
        mysqli_query($link, $query);
    }

    mysqli_close($link);
    die(json_encode(['res' => $res]));
}
die();