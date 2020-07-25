<?php
$link = mysqli_connect(
    'localhost',
    'root',
    '',
    'socialmedia'
);
if($link !== false){
    $query = "select * from news order by `date` desc, `id` desc limit 10";
    $resDb = mysqli_query($link, $query);
    $res = [];
    while($item = mysqli_fetch_assoc($resDb)){
        $item['date'] = date('d.m G:i', strtotime($item['date']));
        $res[] = $item;
    }
    mysqli_close($link);
    die(json_encode($res));
}
die();