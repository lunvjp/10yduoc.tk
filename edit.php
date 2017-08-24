<?php
/*
 * 1. Trên trang navbar hiển thị tất cả các môn, khi kích vào thì hiện thị danh sách các đề của môn đó bên cột bên trái
 * 2. Khi kích vào thì hiển thị các câu hỏi và câu trả lời bên phải cho phép admin chỉnh sửa
 * 3. Thanh công cụ ở dưới cùng cho phép cập nhật dữ liệu cũng như cập nhật trong cơ sở dữ liệu luôn.
 */
require_once "connect.php";
require_once "functions.php";
session_start();

// CẬP NHẬT VÀO CƠ SỞ DỮ LIỆU TẠI ĐÂY
$success = '';
if (isset($_POST['question'])) {
//    $sentenceList = $_SESSION['question'];

    $database->table='question';
    $check = false;
    foreach($_POST['question'] as $key => $value) {
        $set['name'] = trim(ucfirst($_POST['name'][$key]));
        $set['a'] = trim(ucfirst($_POST['A'][$key]));
        $set['b'] = trim(ucfirst($_POST['B'][$key]));
        $set['c'] = trim(ucfirst($_POST['C'][$key]));
        $set['d'] = trim(ucfirst($_POST['D'][$key]));
        $set['e'] = trim(ucfirst($_POST['E'][$key]));
        $set['f'] = trim(ucfirst($_POST['F'][$key]));
        $set['g'] = trim(ucfirst($_POST['G'][$key]));
        $set['answer'] = trim(strtoupper($_POST['answer'][$key]));
        $set['detail_answer'] = trim(ucfirst($_POST['detailofanswer'][$key]));

        $database->update($set,array('id'=>$value));
        if ($database->showRows()==1) $check = true;
    }

    if ($check) {
        $_SESSION['success-update'] = '<strong>Success!</strong> Cập nhật thành công';
        header("location: edit.php?subid=".$_GET['subid']."");
        exit();
    }
}

$account = '';
if (isset($_SESSION['username'])) {
    $account = "<a href='logout.php'>Đăng xuất</a>";
    $account .= ' Chào ' . $_SESSION['username'];
}

// Lấy ra các bộ đề có trong CSDL
$html = '';
if (isset($_GET['subid'])) { // Truyền vào id của một môn lấy ra
    $query = "select a.id,a.name,count(b.question_id) as total from test as a, manage_test as b, unit, subject
                    where a.id = b.test_id
                    and a.unit_id = unit.id
                    and unit.subject_id = subject.id
                    and subject.id = ".$_GET['subid']."
                    group by a.id";
    $database->query($query);
    $data = $database->select();

//    echo '<pre>';print_r($donesentencelist);echo '</pre>';
    foreach ($data as $key => $value) {
        $style = '';
        if (isset($_GET['testid']) && ($_GET['testid'] == $value['id'])) {
            $style = "style='background-color: lightskyblue'";
        }
        $html .= '<li>
                    <span><a '.$style.' href="edit.php?subid='.$_GET['subid'].'&testid=' . $value["id"] . '">' . $value["name"] . '</a></span> 
                    <span style="padding-left:10px;color: yellowgreen">'. $value['total'] . ' Câu</span>
                </li>';
    }
}
?>
<html>
<head>
    <title>Thêm đề</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


    <style>

        /* INDEX.PHP */
        * {
            border: 0;
            padding: 0;
            margin: 0;
        }

        body {
            background: lightskyblue;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .row {
            font-size: 1em;
            border-top: 1px solid;
            padding: 20px;
        }


        /*.button {*/
            /*display: block;*/
            /*width: 70px;*/
            /*margin: 0 auto;*/
            /*background: blue;*/
            /*cursor: hand;*/
            /*padding: 10px;*/
            /*border-radius: 10px;*/
            /*color: white;*/
        /*}*/

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

        .mynavbar a:hover {
            text-decoration: none;
            background-color: black;
        }

        .chucnang {
            width: 29%;
        }

        .chucnang a {
            text-decoration: none;
            color: black;
        }

        .chucnang a:hover {
            color: red;
        }

        .chucnang li {
            background: white;
            border-radius: 10px;
            color: black;
            font-size: 1.4em;
            padding: 3px;
            padding-left: 10px;
            font-weight: bold;
            margin-right: 4px;
            box-sizing: border-box;
        }

        .chucnang li:hover {
            background-color: lightgrey;
        }

        .chucnang #sum-sentence {
            background-color: aqua;
        }

        .error {
            color: red;
        }

        .form-setup {
            /*border: 1px solid grey;*/
            border: none;
            height: 100%;
            width: 30%;
            padding: 8px;
            box-sizing: border-box;
            position: fixed;
            overflow-x: hidden;
            overflow-y: scroll;
            background-color: #f1f1f1;
        }

        .form-setup span {
            font-weight: normal;
            font-size: 1em;
        }

        .form-setup ol li {
            display: list-item;
            list-style-position: inside;
            list-style-type: decimal;
        }

        .form-setup a {
            text-decoration: none;
            color: black;
        }

        .form-setup a:hover {
            background-color: #cccccc;
        }

        .content {
            border: 1px solid grey;
            padding:0;
            box-sizing: border-box;
            position: fixed;
            top: 35px;
            bottom: 30px;
            left: 30%;
            width: 70%;
            overflow-x: hidden;
            overflow-y: auto;
            border-top: none;
            padding-top:5px;
        }

        .content .question {
            font-family: "Times New Roman", sans-serif;
        }

        .content .question hr {
            width: 100%;
            height: 1px;
            background-color: grey;
            margin: 10px 0 20px 0;
        }

        .content .form-add-submit {
            background: #017ebe;
            position: fixed;
            z-index: 100;
            bottom: 0;
            left: 30%;
            height: 40px;
            width: 100%;
            border-color: grey;
            color: black;
            vertical-align: middle;
        }


        .content .form-add-submit button, .content .form-add-submit a {
            vertical-align: middle;
            padding: 0 10px;
            border: 1px solid;
            font-weight: bold;
            height: 100%;
            cursor: pointer;
        }

        .content .form-add-submit a {
            color: black;
            text-decoration: none;
            display: inline-block;
            background: lightgray;
            line-height: 38px;
        }

        .content .form-add-submit button:hover, .content .form-add-submit a:hover {
            background: darkgray;
        }

        .content .item {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .content .item p {
            display: table-cell;
        }

        .content .item .answer {
            width: 20px;
            vertical-align: middle;
        }

        .content .item .title {
            width: 45px;
            vertical-align: top;
            font-weight: bold;
        }

        .content .item input, .content .item textarea {
            width: 100%;
            border: 1px solid;
            padding: 2px;
        }
    </style>
</head>
<body>
<div class="mynavbar">
    <!--                <a href="" class="topnav-icons fa fa-menu w3-hide-large w3-left w3-bar-item w3-button" title="Menu"></a>-->
    <a href="." title="Home"><i class="fa fa-home" style="line-height: 35px;"  aria-hidden="true"></i></a>
    <a href="edit.php?subid=1" title="Làm Đề Giải Phẫu">GIẢI PHẪU</a>
    <a href="edit.php?subid=2" title="Làm Đề Giải Phẫu">SINH DI TRUYỀN</a>
    <div style="float: right;margin-right:10px;">
        <?php
        if (isset($_SESSION['username']) && $_SESSION['username'] == 'momabz6') echo "<a href='edit.php'>CHỈNH SỬA ĐỀ</a>";
        echo $account;
        ?>
    </div>
</div>

<div class="form-setup" style="border-right: none;"> <!-- Hiện các bộ đề trong CSDL ở đây -->
    <ol><?php echo $html; ?>
    </ol>
</div>

<div class="content"> <!-- Hiển thị số câu trong 1 bộ test ở đây -->
    <?php if (isset($_SESSION['success-update'])) {
        echo "<div style='margin:10px;box-sizing: border-box' class='alert alert-success'>".$_SESSION['success-update']."</div>";
        unset($_SESSION['success-update']);
} ?>
    <form method="post" name="update-form" id="update-form">
    <div id="choiceuser">
        <?php

        if (isset($_GET['testid'])) {
            $query = "select question.id, question.name, question.a, question.b, question.c, question.d, question.e, question.f, question.g, question.answer, question.detail_answer 
                    from question, manage_test, test
                    where question.id = manage_test.question_id
                    and test.id = manage_test.test_id
                    and test.id = ".$_GET['testid']."";
            $database->query($query);
            $sentenceList = $database->select();
//            $_SESSION['question'] = $sentenceList;

            $xhtml = '';
            if (!empty($sentenceList)) {
                foreach ($sentenceList as $key => $value) {
                    $xhtml .= "<div class='question' style='padding-left: 10px'>
                                    <input type='hidden' name='question[]' value=".$value['id'].">
                                    <div class='item'>
                                        <p class='title'>Câu ".($key + 1)."</p>
                                    <p><textarea rows='3' id='textarea' name='name[]'>".$value['name']."</textarea></p>
                                    </div>";

                    $temp['A'] = $value['a'];
                    $temp['B'] = $value['b'];
                    $temp['C'] = $value['c'];
                    $temp['D'] = $value['d'];
//                    if ($value['e']) $temp['E'] = $value['e'];
//                    if ($value['f']) $temp['F'] = $value['f'];
                    $temp['E'] = $value['e'];
                    $temp['F'] = $value['f'];
                    $temp['G'] = $value['g'];

                    foreach ($temp as $key2 => $value2) {
                        $style = '';
                        if (!($value2&&$value2!='NULL')) $style='style="display:none"';

                        $xhtml .= '<div class="item" '.$style.'>
                                        <p class="answer">'.$key2.'.</p>
                                        <p><input type="text" name="'.$key2.'[]" value="'.$value2.'"></p>
                                    </div>';
                    }

                    $xhtml .= "<div class='item'>
                                    <p class='answer' style='width:40px;vertical-align: top;'>ĐS<input style='width:80%' type='text' name='answer[]' value=".$value['answer']."></p>
                                    <p>Lời giải<textarea rows='2' id='textarea' name='detailofanswer[]'>".$value['detail_answer']."</textarea></p>
                                </div>";
                    $xhtml .= "<hr></div>";
                }
            } else $xhtml = 'Dữ liệu không tồn tại';
            echo $xhtml;
        }
        ?>
    </div>
    </form>

    <div class="form-add-submit" style="position: fixed;"> <!-- Nút cập nhật tại đây -->
        <form method="post" name="form-edit">
            <input hidden id="test" name="test"
                   value="<?php if (isset($_GET['testid'])) echo $_GET['testid']; ?>">
            <button type="button" id="update-button">Cập nhật</button>
        </form>
    </div>
</div>
<script>
    $(function () {
        $("#update-button").click(function () {
            $("#update-form").submit();
        });
    });
</script>
<script src="./js/check.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>

