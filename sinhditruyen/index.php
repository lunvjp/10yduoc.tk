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
 */
/*
 * Có thể giữ lại phần tìm mooner kh ạ ^^ và cả phần ô tính số bài tập đã làm của các môn khác nhau trong 1 tháng kh ạ ??! @@ như vậy em mới dễ quản lí việc học của mình hơn
 * - Nếu người dùng đã làm rồi thì lưu dữ liệu vô bảng này, nếu chưa làm thì ko có


 * * TODO:
 * 2. Trang xem tổng hợp các đề chỉ có tài khoản mới vào được.
 * 3. Có 1 danh sách các đề ở bên trái giống như w3schools.com hiển thị số % làm bài
 * 4. Trang index ko có nút
 */
require_once "../functions.php";
require_once "../connect.php";
session_start();


$html = '';
$account = '';
if (isset($_SESSION['username'])) { // đăng nhập thành công
    // Truyền dữ liệu

    $account = "<a href='../logout.php'>Đăng xuất</a>";
    $account .= ' Chào ' . $_SESSION['username'];

    $database->table = 'test';
    $query = "select a.id,a.name,count(b.question_id) as total from test as a, manage_test as b
              where a.id = b.test_id
              group by a.id";
    $database->query($query);
    $data = $database->select();

//    $donesentence = "select e.id, e.name, count(c.id) as donetotal
//                    from user as a, do_question as b, question as c, manage_test as d, test as e
//                    where a.id = b.user_id
//                    and b.question_id = c.id
//                    and d.question_id = c.id
//                    and e.id = d.test_id
//                    and a.id = " . $_SESSION['id'] . "
//                    group by e.id";
    $donesentence = "select e.id, e.name, count(c.id) as donetotal
                    from user as a, do_question as b, question as c, manage_test as d, test as e, unit, subject
                    where a.id = b.user_id
                    and b.question_id = c.id
                    and d.question_id = c.id
                    and e.id = d.test_id
                    and e.unit_id = unit.id
                    and unit.subject_id = subject.id
                    and a.id = " . $_SESSION['id'] . "
                    and subject.id = 2
                    group by e.id";
    $database->query($donesentence);
    $donesentencelist = $database->select();


//    echo '<pre>';print_r($donesentencelist);echo '</pre>';
    foreach ($data as $key => $value) {
        $idsentence = $value['id']; // tất cả mã đề từ bảng dữ liệu
//        echo '<pre>';print_r($donesentencelist);echo '</pre>';
        $check = false;
        if (!empty($donesentencelist)) {
            foreach ($donesentencelist as $index => $val) {
//                echo gettype($val);
//            print_r($val);
                if ($idsentence == $val['id']) {
//                if (in_array($idsentence,$val)) { // Nếu mã đề đã làm rồi thì khi click vào link sẽ hiển thị các câu đã làm
                    // nếu chưa làm thì khi clic kvaof thì vào trang làm bài
                    $html .= '<li>
                            <span><a onclick="seeDetail();" href=".?id=' . $value["id"] . '">' . $value["name"] . '</a></span> 
                            <span style="padding-left:10px;color: yellowgreen">' . $val['donetotal'] . '/' . $value['total'] . '</span>
                            <span style="padding-left:10px;"><a style="color: yellowgreen" href="../lambai.php?id=' . $value['id'] . '">Làm tiếp</a></span>
                        </li>';
                    $check = true;
                    break;
                }
            }

            //                 <span style="padding-left:10px;color: yellowgreen">'.$value['total'].'</span>
            if ($check == false) { // Chưa làm 1 câu hỏi nào trong mã đề này
                $html .= '<li>
                    <span><a href="../lambai.php?id=' . $value["id"] . '">' . $value["name"] . '</a></span>
                </li>';
            }
        }


    }
} else {
    header('location: ..');
    exit();
}
?>

<html>
<head>
    <title>LunVjp</title>
    <meta charset="utf-8">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .mynavbar {
            background-color: #5f5f5f;
            color: #f1f1f1;
            font-size: 17px;
            letter-spacing: 1px;
            height: 35px;
            line-height: 35px;
            margin:0;
        }

        .mynavbar a {
            color: #f1f1f1;
            padding: 0 7px;
            height: 100%;
            float: left;
            display: block;
        }

        .mynavbar a:hover, .mynavbar a:active {
            text-decoration: none;
            background-color: black;
            color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="mynavbar">
    <!--                <a href="" class="topnav-icons fa fa-menu w3-hide-large w3-left w3-bar-item w3-button" title="Menu"></a>-->
    <a href=".." title="Home"><i class="fa fa-home" style="line-height: 35px;"  aria-hidden="true"></i></a>
    <a href="../giaiphau" title="Làm Đề Giải Phẫu">GIẢI PHẪU</a>
    <a href="." title="Làm Đề Sinh Di Truyền">SINH DI TRUYỀN</a>
    <div style="float: right;margin-right:10px;">
        <?php echo $account; ?>
    </div>
</div>

<div class="form-setup" style="border-right: none;"> <!-- Hiện các bộ đề đã làm ở đây -->
    <ol><?php echo $html; ?>
    </ol>
</div>

<div class="content"> <!-- Hiển thị số câu đã làm ở đây -->
    <?php
    if (isset($_SESSION['username'])) {
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
//                $xhtml = '<div class="container-fluid">';
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
<script src="../js/check.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
