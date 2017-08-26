<?php
require_once "connect.php";
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

//Convert JSON data into PHP variable
$userData = json_decode($_POST['userData']);
if(!empty($userData)){
    $database->table = 'user';
    $_SESSION['username'] = $userData->first_name.' '.$userData->last_name;
    if ($userData->email == 'lunvjp@gmail.com') $_SESSION['email'] = 'lunvjp@gmail.com';

    $oauth_provider = $_POST['oauth_provider'];
    //Check whether user data already exists in database
    $prevQuery = "SELECT * FROM user WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$userData->id."'";

    $prevResult = $database->query($prevQuery);
    $prevResult = $database->select();

    $edit['first_name'] = $userData->first_name;
    $edit['last_name'] = $userData->last_name;
    $edit['email'] = $userData->email;
    $edit['gender'] = $userData->gender;
    $edit['locale'] = $userData->locale;
    $edit['picture'] = $userData->picture->data->url;
    $edit['link'] = $userData->link;
    $edit['modified'] = date("Y-m-d H:i:s");

    if($database->showRows() > 0){ // Tài khoản đã tồn
        $update = array('first_name'=>$userData->first_name);
        $condition = array('oauth_provider'=>$oauth_provider,'oauth_uid'=>$userData->id);
        $database->update($edit,$condition);
    }else{ // Đăng nhập lần đầu tiên vào web
        $edit['oauth_provider'] = $oauth_provider;
        $edit['oauth_uid'] = $userData->id;
        $edit['created'] = date("Y-m-d H:i:s");
        $database->insert($edit);


    }

    // Tạo $_SESSION['id'] sau khi nó insert trong database
    $query = "SELECT id FROM user WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$userData->id."'";
    $database->query($query);
    $id = $database->select()[0]['id'];
    $_SESSION['id'] = $id;

    $account = "<a href='../logout.php'>Đăng xuất</a>";
    $account .= '<a style="pointer-events: none">Chào ' . $_SESSION['username'].'</a>';
    $account .= "<a style='padding:0'><img src=".$userData->picture->data->url."></a>";
    $_SESSION['info'] = $account;
//    $_SESSION['imageUser'] = $userData->picture->data->url;
}
?>