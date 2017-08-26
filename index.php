<?php
require_once "functions.php";
require_once "connect.php";
session_start();

$html = '';
//$account = '';
//if (isset($_SESSION['username'])) { // đăng nhập thành công
//    $account = "<a href='logout.php'>Đăng xuất</a>";
//    $account .= ' Chào ' . $_SESSION['username'];
//    header("location: ./giaiphau/");
//    exit();
//}

$database->table = 'test';
$query = "select test.id, test.name, count(manage_test.question_id) as total from subject, unit, test, manage_test
                where manage_test.test_id = test.id
                and test.unit_id = unit.id
                and unit.subject_id = subject.id
                and subject.id = 1
                group by test.id";
$database->query($query);
$data = $database->select();

foreach ($data as $key => $value) {
    if ($key <= 4) {
        $html .= '<li><span><a style="cursor: pointer;color: yellowgreen" onclick="doTest(' . $value['id'] . ')">' . $value["name"] . '</a></span></li>';
    } else {
        $html .= '<li><span><a style="cursor: pointer;color: red;" data-toggle="modal" data-target="#myModal">' . $value["name"] . '</a></span></li>';
    }
}

$content = '<form method="post" name="form-add" id="form-do-test"><input type="hidden" name="done" value="1">';

$query = "select a.id,a.name as question,a.a,a.b,a.c,a.d,a.e,a.f,a.answer FROM question as a, manage_test as b, test
                where a.id = b.question_id
                and b.test_id = test.id
                and test.id = 1";
$database->query($query);
$data = $database->select();

$result = array();
foreach ($data as $key => $value) {
    if ($key == 20) break;
    $result[$value['id']] = $value['answer'];

    $item['A'] = $value['a'];
    $item['B'] = $value['b'];
    $item['C'] = $value['c'];
    $item['D'] = $value['d'];
    if ($value['e']) $item['E'] = $value['e'];
    if ($value['f']) $item['F'] = $value['f'];


    $temp = '<div class="question" id="'.$value['id'].'">
                    <div class="item">
                        <p class="title">Câu ' . ($key+1) . '.</p>
                        <p class="title-content">' . $value['question'] . '</p>
                    </div>';
    foreach($item as $i => $val) {
        $temp .= '<div class="item">
                        <p class="answer">'.$i.'.</p>
                        <p style="width:2%;vertical-align: middle;"><input class="'.$value['id'].'" value="'.strtolower($i).'" type="radio" name="'.$value['id'].'"></p>
                        <p style="padding-left:10px;"><span>'.$val.'</span></p>
                    </div>';
    }
    $temp .='<hr></div>';
    $content .=$temp;

}
$content .='</form>';
if (!isset($_SESSION['answer'])) $_SESSION['answer'] = $result;


if (isset($_POST['done'])) {
    $database->table = 'do_question';

    array_shift($_POST); // Xóa thằng input bị che đi
    $result = $_SESSION['answer'];

    $done = $_POST;

    $dem = 0;
    foreach ($done as $key => $value) {
        $right = $result[$key];
        if (strtolower($right) == strtolower($value)) { // Right
            $dem++; // số câu đúng
        }
    }

    $_SESSION['result'] = "<div style='position: relative;' class='alert alert-success alert-dismissable fade in'>
                        <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>
                        <span style='width:100%;'>Kết quả: " . $dem . "/20</span>
                        <span style='width:100%;'>Đăng nhập để xem chi tiết đáp án và làm thêm thật nhiều đề nhé</span>
                    </div>";
    header('location: .');
    exit();
}
?>

<html>
<head>
    <title>LunVjp</title>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="./css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .content {
            border: 1px solid grey;
            padding:0;
            box-sizing: border-box;
            position: fixed;
            top: 35px;
            bottom: 38px;
            left: 30%;
            width: 70%;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: white;
            border-top: none;
        }

        .content .question {
            font-family: "Times New Roman", sans-serif;
            padding-top: 20px;
        }

        .content .question hr {
            width: 100%;
            height: 1px;
            background-color: grey;
            margin: 0;
        }

        .content #id {
            background:  lightskyblue;
        }

        .auto-padding {
            padding-top: 40px;
        }
    </style>
</head>
<body>

<div class="mynavbar">
    <!--                <a href="" class="topnav-icons fa fa-menu w3-hide-large w3-left w3-bar-item w3-button" title="Menu"></a>-->
    <a href="." title="Home"><i class="fa fa-home" style="line-height: 35px;"  aria-hidden="true"></i></a>
    <a href="./giaiphau" title="Làm Đề Giải Phẫu">GIẢI PHẪU</a>
    <a href="./sinhditruyen" title="Làm Đề Giải Phẫu">SINH DI TRUYỀN</a>
    <div id="account" style="float: right;margin-right:10px;">
        <?php
        if (isset($_SESSION['info'])) echo $_SESSION['info'];
        ?>
    </div>
</div>

<div class="form-setup" style="border-right: none;"> <!-- Hiện các bộ đề đã làm ở đây -->
    <a href="javascript:void(0);" onclick="fbLogin()" id="fbLink"><img src="images/fblogin.png"></a>
    <ol>
        <?php echo $html; ?>
    </ol>
</div>

<div class="content"> <!-- Hiển thị số câu đã làm ở đây -->

        <div id="time"
             style="position:fixed;width:100%;background: lightskyblue; height:40px;border-bottom:1px solid grey;font-size:25px;font-weight: bold;font-family: Arial,sans-serif;color: #ffff80;line-height: 40px;padding-left:10px;">
            <span>BẮT ĐẦU</span></div>

    <div id="result"><?php
        if (isset($_SESSION['result'])) {
            echo $_SESSION['result'];
        }
        ?></div>

    <div id="ajax-load" style="display: none; height: 100px; width: 160px; margin: auto; margin-top:60px">
        <i class="fa fa-spinner fa-spin" style="font-size: 7em; color: #D9ECFF;"></i>
    </div>




    <div id="choiceuser" style="padding-top: 40px">
        <?php
        if (!isset($_SESSION['result'])) echo $content;
        else unset($_SESSION['result']);
        ?>
    </div>


    <div class="form-add-submit" style="position: fixed;">
        <form id="form-fade" method="post"></form>
        <?php
//        if (isset($_SESSION['id'])) { // Đăng nhập thành công
            ?>
<!--            <button type="button" id="wrong-button">Bài làm sai</button>-->
<!--            <button type="button" id="right-button">Bài làm đúng</button>-->
        <button type="button" id="submit-button">Nộp bài</button>
        <?php
//        }
        ?>

    </div>
</div>
<script src="check.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
