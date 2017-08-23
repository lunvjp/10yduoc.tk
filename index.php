<?php

/*
 * Tạo 4 bảng
 * + user: PK: id - FK:
 * + test: PK: id
 * + question: PK: id - FK: test_id
 * + answer: PK: id - FK: test_id, user_id, question_id
 *
 *
 *
 *
 * * Chức năng:
 * 1. Phòng luyện
 * - random câu hỏi trong bộ đề, giới hạn thời gian làm bài, hiển thị top 10 người đứng đầu với làm được bao nhiêu câu hỏi (số câu đúng, số câu sai)
 * - vừa làm vừa có thể trò chuyện
 * - thời gian mở phòng luyện như làm nhiệm vụ sự kiện, được tăng thêm level, exp các kiểu
 * 2. Có các huy hiệu
 * - Huy hiệu level, huy hiệu BTĐL
 * 3. Chức năng làm lại tất cả các bài đã làm, có tùy chọn set thời gian và tạo ra số câu hỏi theo ý thích
 * 4. Chức năng tạo phòng luyện với bạn bè để solo nữa (giống y hệt phòng luyện bình thường chỉ có là có link mới vào được)
 */
/*
 * Có thể giữ lại phần tìm mooner kh ạ ^^ và cả phần ô tính số bài tập đã làm của các môn khác nhau trong 1 tháng kh ạ ??! @@ như vậy em mới dễ quản lí việc học của mình hơn
 * - Nếu người dùng đã làm rồi thì lưu dữ liệu vô bảng này, nếu chưa làm thì ko có


 * * TODO:
 * 2. Trang xem tổng hợp các đề chỉ có tài khoản mới vào được.
 * 3. Có 1 danh sách các đề ở bên trái giống như w3schools.com hiển thị số % làm bài
 * 4. Trang index ko có nút
 */
require_once "functions.php";
require_once "connect.php";
session_start();


$fullname = '';
$username = '';
$password = '';
$errorFullname = '';
$errorUsername = '';
$errorPassword = '';

if (isset($_POST['fullname']) && isset($_POST['usernameregister']) && isset($_POST['passwordregister'])) {
//    die('NGU');
    $fullname = $_POST['fullname'];
    $username = $_POST['usernameregister'];
    $password = $_POST['passwordregister'];

    if (!$fullname) {
        $errorFullname = 'Bạn chưa nhập họ tên';
//        $_SESSION['errorFullname'] = 'Bạn chưa nhập họ tên';
    }
    if (!$username) {
        $errorUsername = 'Bạn chưa nhập tên đăng nhập';
//        $_SESSION['errorUsername'] = 'Bạn chưa nhập tên đăng nhập';
    }
    if (!$password) {
        $errorPassword = 'Bạn chưa nhập mật khẩu';
//        $_SESSION['errorPassword'] = 'Bạn chưa nhập mật khẩu';
    }

    // Kiểm tra tên đăng nhập có bị trùng hay không
    $database->table = 'user';
    $query = "select username from user";
    $database->query($query);
    $usernamelist = $database->select();
//    echo '<pre>';print_r($usernamelist);echo '</pre>';
//     die();

    $database->query("select max(id) as lastid from user");
    $lastid = $database->select()[0]['lastid'];
//    echo '<pre>';print_r($lastid);echo '</pre>';
    foreach($usernamelist as $key => $value) {
        if (in_array($username, $value)) // bị trùng
        {
            $errorUsername = 'Tài khoản đã tồn tại';
//            $_SESSION['errorUsername'] = 'Tài khoản đã tồn tại';
            break;
        }
    }


    //----------------------------------------------
    if (!$errorFullname && !$errorUsername && !$errorPassword) {
//        echo gettype($lastid);
//        $insert['id'] = $lastid+1;
//        $insert['username'] = $username;
//        $insert['password'] = $password;
//        $insert['fullname'] = $fullname;
        $insert = array('id' => $lastid + 1, 'username' => $username, 'password' => $password, 'fullname' => $fullname);
//        echo '<pre>';print_r($insert);echo '</pre>';
        $database->insert($insert, 'single');
        if ($database->showRows()>0) {
            $_SESSION['success'] = 'Tạo thành công';
            header('location: .');
            exit();
        } else {
//            $_SESSION['']
        }
    }
}


if (isset($_POST['username']) && isset($_POST['password'])) {
    // Tìm trong CSDL nếu thấy giống thì làm
    $username = mysql_real_escape_string($_POST['username']);
    $password = mysql_real_escape_string($_POST['password']);

    $database->table = 'user';
    $query[] = "select *";
    $query[] = "from `user`";
    $query[] = "where `username` = '$username'";
    $query[] = "and `password` = '$password'";
    $query = implode(' ', $query);
    $database->query($query);

    $ketqua = $database->select();
    $ketqua = $ketqua[0];

    if (!empty($ketqua)) // Đăng nhập thành công
    {
//        echo '<pre>';
//        print_r($ketqua);
//        echo '</pre>';
        $id = $ketqua['id'];
        $username = $ketqua['username'];

//        echo $ketqua['username'];
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $username;
        header("location: ./giaiphau");
        exit();
    } else {
        header("location: .");
        exit();
    }
}

$html = '';
$account = '';
if (isset($_SESSION['username'])) { // đăng nhập thành công
    // Truyền dữ liệu

    $account = "<a href='logout.php'>Đăng xuất</a>";
    $account .= ' Chào ' . $_SESSION['username'];

//    $database->table = 'test';
//    $query = "select a.id,a.name,count(b.question_id) as total from test as a, manage_test as b
//              where a.id = b.test_id
//              group by a.id";
//    $database->query($query);
//    $data = $database->select();
//
//    $donesentence = "select e.id, e.name, count(c.id) as donetotal
//                    from user as a, do_question as b, question as c, manage_test as d, test as e
//                    where a.id = b.user_id
//                    and b.question_id = c.id
//                    and d.question_id = c.id
//                    and e.id = d.test_id
//                    and a.id = " . $_SESSION['id'] . "
//                    group by e.id";
//    $database->query($donesentence);
//    $donesentencelist = $database->select();
//
//
////    echo '<pre>';print_r($donesentencelist);echo '</pre>';
//    foreach ($data as $key => $value) {
//        $idsentence = $value['id']; // tất cả mã đề từ bảng dữ liệu
////        echo '<pre>';print_r($donesentencelist);echo '</pre>';
//        $check = false;
//        if (!empty($donesentencelist)) {
//            foreach ($donesentencelist as $index => $val) {
////                echo gettype($val);
////            print_r($val);
//                if ($idsentence == $val['id']) {
////                if (in_array($idsentence,$val)) { // Nếu mã đề đã làm rồi thì khi click vào link sẽ hiển thị các câu đã làm
//                    // nếu chưa làm thì khi clic kvaof thì vào trang làm bài
//                    $html .= '<li>
//                            <span><a onclick="seeDetail();" href=".?id=' . $value["id"] . '">' . $value["name"] . '</a></span>
//                            <span style="padding-left:10px;color: yellowgreen">' . $val['donetotal'] . '/' . $value['total'] . '</span>
//                            <span style="padding-left:10px;"><a style="color: yellowgreen" href="lambai.php?id=' . $value['id'] . '">Làm tiếp</a></span>
//                        </li>';
//                    $check = true;
//                    break;
//                }
//            }
//        }
//
//        //                 <span style="padding-left:10px;color: yellowgreen">'.$value['total'].'</span>
//        if ($check == false) { // Chưa làm 1 câu hỏi nào trong mã đề này
//            $html .= '<li>
//                    <span><a href="lambai.php?id=' . $value["id"] . '">' . $value["name"] . '</a></span>
//                </li>';
//        }
//    }
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
            background-color: lightskyblue;
        }
    </style>
</head>
<body>

<div class="mynavbar">
    <!--                <a href="" class="topnav-icons fa fa-menu w3-hide-large w3-left w3-bar-item w3-button" title="Menu"></a>-->
    <a href="." title="Home"><i class="fa fa-home" style="line-height: 35px;"  aria-hidden="true"></i></a>
    <a href="./giaiphau" title="Làm Đề Giải Phẫu">GIẢI PHẪU</a>
    <a href="./sinhditruyen" title="Làm Đề Giải Phẫu">SINH DI TRUYỀN</a>
    <div style="float: right;margin-right:10px;">
        <?php
        if (isset($_SESSION['username']) && $_SESSION['username'] == 'momabz6') echo "<a href='edit.php'>CHỈNH SỬA ĐỀ</a>";
        echo $account; ?>
    </div>
</div>

<div class="form-setup" style="border-right: none;"> <!-- Hiện các bộ đề đã làm ở đây -->
    <?php
    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success'>
                <span style='width:100%;'>".$_SESSION['success']."</span>
            </div>";
        unset($_SESSION['success']);
    }
    ?>
    <ol>
        <?php echo $html; ?>
    </ol>
</div>

<div class="content"> <!-- Hiển thị số câu đã làm ở đây -->
    <?php
    if (!isset($_SESSION['username'])) { // Đăng nhập không thành công
        ?>

        <div class="form-login">
            <div class="container">
                <h2>Đăng nhập làm đề</h2>
                <form class="form-horizontal" action="" method="post">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Username:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="username" placeholder="Enter username"
                                   name="username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="pwd">Password:</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="pwd" placeholder="Enter password"
                                   name="password">
                        </div>
                    </div>
                    <!--        <div class="form-group">-->
                    <!--            <div class="col-sm-offset-2 col-sm-10">-->
                    <!--                <div class="checkbox">-->
                    <!--                    <label><input type="checkbox" name="remember"> Remember me</label>-->
                    <!--                </div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-info">Đăng nhập</button>
                            <!--                            <button type="button" class="btn btn-info" style="margin-left:10px;" id="register-button">Tạo tài khoản</button>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="register" style="margin-top:20px;height:370px;background-color: rgba(0,0,0,0.2);">
            <div class="container-fluid">
                <h2>Tạo tài khoản</h2>
                <form action="" method="post" id="form-register" name="form-register">
                    <div class="form-group">
                        <label for="email">Họ và tên:</label>
                        <input type="text" class="form-control" id="fullname" placeholder="Enter full name"
                               name="fullname" value="<?php echo $fullname; ?>">
                        <span style="color: red;"><?php echo $errorFullname; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Tên đăng nhập:</label>
                        <input type="text" class="form-control" id="usernameregister" placeholder="Enter email"
                               name="usernameregister" value="<?php echo $username; ?>">
                        <span style="color: red;"><?php echo $errorUsername; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="pwd">Mật khẩu:</label>
                        <input type="password" class="form-control" id="passwordregister" placeholder="Enter password"
                               name="passwordregister" value="<?php echo $password; ?>">
                        <span style="color: red;"><?php echo $errorPassword; ?></span>
                    </div>
                    <button type="submit" class="btn btn-success">Tạo tài khoản</button>
                </form>
            </div>
        </div>


        <?php
    } else {
        ?>
        <div id="choiceuser">
            <?php
            //-----------------------------------------------------------
            // Hiển thị các câu đã làm (do_question) trong 1 đề (test) của user (user)
            //
            if (isset($_GET['id'])) {
                $query = "SELECT b.id, b.name, b.a, b.b, b.c, b.d, b.e, b.f, a.check, a.answerofuser, b.answer
                        from do_question as a, question as b, manage_test as c, test as d
                        where a.question_id = b.id
                        and c.question_id = b.id
                        and c.test_id = d.id
                        and a.user_id = " . $_SESSION['username'] . "
                        and d.id = " . $_GET['id']."";
                $database->query($query);
                $detailofdonesentence = $database->select();

                $xhtml = '';
                if (!empty($detailofdonesentence)) {
                    foreach ($detailofdonesentence as $key => $value) {
                        $xhtml .= '<div class="question">
                                    <div class="item">
                                        <p class="title">Câu ' . ($key + 1) . '.</p>
                                        <p class="title-content">' . $value['name'] . '</p>
                                    </div>';
                        $temp['A'] = $value['a'];
                        $temp['B'] = $value['b'];
                        $temp['C'] = $value['c'];
                        $temp['D'] = $value['d'];
                        if ($value['e']) $temp['E'] = $value['e'];
                        if ($value['f']) $temp['F'] = $value['f'];

                        foreach ($temp as $key2 => $value2) {
                            $xhtml .= '<div class="item">
                                <p class="answer">' . $key2 . '.</p>
                                <p>' . $value2 . '</p>
                            </div>';
                        }

                        $color = $value['check'] == 0 ? 'red' : 'blue';

                        $xhtml .= '<p style="padding-left:10px;display:block;font-family:Arial,sans-serif;font-size:13px;line-height:30px;font-weight: 600;height:30px;color: ' . $color . ';background:#e9ebee">' . strtoupper($value['answer']) . ' - Trả lời ' . strtoupper($value['answerofuser']) . '</p></div>';

                    }
//                $xhtml .= '</div>';
                }
                echo $xhtml;
            }
            ?>
        </div>

        <?php
    }
    ?>

    <div class="form-add-submit" style="position: fixed;">
        <?php
        if (isset($_SESSION['id'])) { // Đăng nhập thành công
            ?>
            <form method="post" name="form-edit">
                <input hidden id="wrongsentence" name="wrongsentence"
                       value="<?php if (isset($_GET['id'])) echo $_GET['id']; ?>">
                <button type="button" id="wrong-button">Bài làm sai</button>
                <button type="button" id="right-button">Bài làm đúng</button>
                <!--                <button><a href=".">Quay lại</a></button>-->
            </form>
        <?php
        }
        ?>

    </div>
</div>
<script src="./js/check.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
