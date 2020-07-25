<?php
$link = mysqli_connect(
    'localhost',
    'root',
    '',
    'socialmedia'
);

if($link !== false){
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $data = [];
        foreach($_GET as $key=>$item){
            $data[$key] = htmlspecialchars($item);
        }
        $query = "select users.id, name, avatar, type, dateOpen, 
                            (select status 
                                from statuses 
                                where statuses.userId=users.id 
                                order by statuses.id desc 
                                limit 1) as status,
                            group_concat(friends.user, ',') as fids_1,
                            group_concat(friends.friend, ',') as fids_2
                           from users 
                           join friends on friends.user=users.id or 
                                           friends.friend=users.id
                           where users.id={$data['id']} 
                           limit 1;";
        $res = mysqli_fetch_assoc(mysqli_query($link, $query));
        $fids = array_filter(
            array_unique(
                array_merge(
                    explode(',', $res['fids_1']),
                    explode(',', $res['fids_2'])
                )
            )
        );
        // $query = "select id, avatar from users 
        //                                 where id in (".implode(',', $fids).")";
        // $friendsDb = mysqli_query($link, $query);    
        // $friends = [];
        // while($item = mysqli_fetch_assoc($friendsDb)){
        //     $friends[] = $item;
        // }
        // unset($res['fids_1']);
        // unset($res['fids_2']);
        // $res['friends'] = $friends;
        // var_dump($fids);
        // var_dump(mysqli_error($link));
        mysqli_close($link);
        if($res !== false && !empty($res)){
            die(json_encode([
                'res' => true,
                'user' =>  $res
            ]));
        }
        else{
            die(json_encode(['res' => false]));
        }
    }
    else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
        $data = json_decode(file_get_contents('php://input'), true);
        foreach($data as $key=>&$item){
            $item = $key . '="' . htmlspecialchars($item) . '"';
        }
        $query = "update users set " . implode(',', $data) . " where id=" . htmlspecialchars($_GET['id']);
        $res = mysqli_query($link, $query);
        mysqli_close($link);
        die(json_encode(['res' => $res]));
    }
    else if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $query = "select login from users 
                        where id=".htmlspecialchars($_POST['userId']).
                        " limit 1;";
        $resDb = mysqli_query($link, $query);
        if($resDb && $user = mysqli_fetch_assoc($resDb)){
            $name = $user['login'].'avatar.jpg';
            move_uploaded_file($_FILES['file']['tmp_name'], "/opt/lampp/htdocs/uploads/$name");
            $query = "update users 
                            set avatar='//localhost/uploads/$name' 
                            where id=".htmlspecialchars($_POST['userId']);
            if(mysqli_query($link, $query)){
                mysqli_close($link);
                die(json_encode([
                    'res' => true, 
                    'src' => "//localhost/uploads/$name"
                ]));
            }
        }
        die(json_encode(['res' => false]));
    }
}
die(json_encode(['res' => false]));