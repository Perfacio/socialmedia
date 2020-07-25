<?php
$link = mysqli_connect(
    'localhost',
    'root',
    '',
    'socialmedia'
);

if($link !== false){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = json_decode(file_get_contents('php://input'), true);
        foreach($data as &$item){
            $item = htmlspecialchars($item);
        }
        $query = "insert into statuses(status, userId) 
                            values('{$data['status']}', {$data['userId']});";
        $res = mysqli_query($link, $query);
        mysqli_close($link);
        die(json_encode(['res' => $res]));
    }
    else if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "select status from statuses where userId=".htmlspecialchars($_GET['id'])." order by id desc limit 1";
        $resDb = mysqli_query($link, $query);
        if($res = mysqli_fetch_assoc($resDb)){
            mysqli_close($link);
            die(json_encode([
                'res' => true, 
                'status' => $res['status']
            ]));
        }
        mysqli_close($link);
    }
}

die(json_encode(['res' => false]));