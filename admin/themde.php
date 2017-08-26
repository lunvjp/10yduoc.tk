<?php
require_once "connect.php";
require_once "functions.php";
session_start();

// Form Add question
if (isset($_POST['question']) && isset($_POST['a']) && isset($_POST['b']) && isset($_POST['c']) && isset($_POST['d']) && isset($_POST['e'])) {
    $questions = $_POST['question'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $e = $_POST['e'];

    $index = null;
    $title = $_SESSION['title'];
    if (isset($_SESSION['id'])) { // Thêm vào đề
        $data = '';
        $index = $_SESSION['idx'];
    } else { // Thêm mới    1 đề
        $data = "title=\"$title\"\n";
        $index = 1;
    }

    $check = false;
    foreach ($questions as $key => $value) {
        $temp = array($value, $a[$key], $b[$key], $c[$key], $d[$key], $e[$key]);
        $info = implode('|', $temp);
        if ($value && $a[$key] && $b[$key] && $c[$key] && $d[$key]) // nếu tất cả khác rỗng thì mới thêm vào
        {
            $cauhoi = trim($value);
            $caua = trim($a[$key]);
            $caub = trim($b[$key]);
            $cauc = trim($c[$key]);
            $caud = trim($d[$key]);
            $caue = trim($e[$key]);
            $data .= "$index=\"$cauhoi|$caua|$caub|$cauc|$caud|$caue\"\n";
            $index++;
            $check = true;
        }
    }
    if ($check == true) {
        if (isset($_SESSION['id'])) {
            $name = $_SESSION['id'];
            $filename = './files/' . $name . '.ini';
            if (file_put_contents($filename, $data, FILE_APPEND)) {
                $_SESSION['ok'] = 'Successfully';
                $_SESSION['idx'] = $index;
            }
        } else { // Tạo mới
            $name = randomString(20);
            $filename = './files/' . $name . '.ini';
            if (file_put_contents($filename, $data, FILE_APPEND)) {
                $_SESSION['ok'] = 'Successfully';
                unset($_SESSION['title']);
            }
        }
    }
}

if (isset($_SESSION['ok'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['ok'] . "</div>";
    unset($_SESSION['ok']);
}

$tieude = '';
$sentences = '';
$errorTitle = '';
$errorSentence = '';
if (isset($_POST['sentence']) && isset($_POST['title'])) {
    $tieude = $_POST['title'];
    $sentences = $_POST['sentence'];

    if (!$tieude) {
        $errorTitle = "<div class='error'>You can't leave this empty.</div>";
    }
    if (!$sentences) {
        $errorSentence = "<div class='error'>You can't leave this empty.</div>";
    }

    if (!$errorTitle && !$errorSentence) { // Thêm vào đầy đủ
        $_SESSION['title'] = $tieude;
        $_SESSION['sentences'] = $sentences;
    }
}

if (isset($_GET['id']) && isset($_GET['t'])) {
    $_SESSION['id'] = $_GET['id'];
}
?>
<html>
<head>
    <title>Thêm đề</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!--    <link href="./css/themde-style.css" rel="stylesheet">-->
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        * {
            border:0;
            padding:0;
            margin:0;
        }

        body {
            background:lightskyblue;
            font-family: Arial,'Lato',sans-serif;
        }

        .row {
            font-size:1em;
            border-top:1px solid;
            padding:20px 20px;
        }

        .tieude {
            text-align:center;
            font-size:2em;
            font-weight:bold;
            color:blue;
        }

        .button {
            display:block;
            width:70px;
            margin:0 auto;
            background:blue;
            cursor: hand;
            padding:10px;
            border-radius:10px;
            color:white;
        }

        .error {
            color: red;
        }

        .form-setup {
            border: 1px solid grey;
            height: 100%;
            width: 15%;
            padding: 8px;
        }

        .form-setup span {
            font-size: 1.2em;
            font-weight: bold;
        }

        .form-setup input {
            width: 100%;
            margin-bottom: 5px;
            padding: 4px;
        }

        .form-setup input#add {
            border: 1px solid;
        }

        .form-setup input#add:hover {
            background: darkgray;
            border: 1px solid;
        }

        .content {
            border: 1px solid grey;
            padding: 10px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 15%;
            width: 85%;
            background-color: white;
            overflow-x: hidden;
            overflow-y: scroll;
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

        .content .form-add-submit {
            background: #017ebe;
            position: fixed;
            z-index: 100;
            bottom: 0;
            left: 15%;
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

        .alert {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="form-setup">
    <form action="" method="post" name="setup" id="form">
        <span>Tên đề</span>
        <input type="text" <?php if (isset($_GET['t']) || isset($_SESSION['title'])) echo 'readonly'; ?> name="title" id="title" value="<?php
        if (isset($_GET['t'])) {
            $_SESSION['title'] = $_GET['t'];
            echo $_GET['t'];
        } else if (isset($_SESSION['title'])) {
            echo $_SESSION['title'];
        } else echo $tieude;
         ?>">
        <?php echo $errorTitle; ?>
        <span>Số câu muốn tạo</span>
        <input type="number" name="sentence" id="sentence" value="<?php echo $sentences; ?>">
        <?php echo $errorSentence; ?>
        <input type="submit" value="Tạo" id="add">
    </form>
</div>
<div class="content">
    <form method="post" name="form-add" id="form-add">
        <?php
        if (isset($_SESSION['sentences'])) {
            for ($i = 1; $i <= $_SESSION['sentences']; $i++) {
                $html = '<div class="ques">
            <div class="item">
                <p class="title">Câu ' . $i . '.</p>
                <p><textarea rows="2" name="question[]" placeholder="Nhập câu ' . $i . '"></textarea></p>
            </div>
            <div class="item">
                <p class="answer">A.</p>
                <p><input type="text" name="a[]" placeholder="Nhập câu A"></p>
            </div>
            <div class="item">
                <p class="answer">B.</p>
                <p><input type="text" name="b[]" placeholder="Nhập câu B"></p>
            </div>
            <div class="item">
                <p class="answer">C.</p>
                <p><input type="text" name="c[]" placeholder="Nhập câu C"></p>
            </div>
            <div class="item">
                <p class="answer">D.</p>
                <p><input type="text" name="d[]" placeholder="Nhập câu D"></p>
            </div>
            <div class="item">
                <p class="answer">E.</p>
                <p><input type="text" name="e[]" placeholder="Nhập câu E"></p>
            </div>
            <hr>
        </div>';
                echo $html;
            }
            unset($_SESSION['sentences']);
        }
        ?>
    </form>
    <div class="form-add-submit">
        <button id="submit-button" type="button" name="submit">Thêm dữ liệu</button>
        <a href="<?php
        if (isset($_SESSION['id']))  {
            $x = $_SESSION['id'];
            echo "edit.php?id=$x";
        } else {
            echo '.';
        }
        ?>">Quay lại</a>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#submit-button").click(function () {
            $("#form-add").submit();
        });
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="./js/check.js"></script>
</body>
</html>
