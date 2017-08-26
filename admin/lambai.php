<?php
require_once "connect.php";
session_start();


$result = array();
$data = array();
$html = '';
$key = '';
if (isset($_GET['id'])) { // Lấy đề từ CSDL ở đây
    // Lấy những câu không có trong bảng do_question từ bảng question


    $id = htmlspecialchars($_GET['id']);
    $id = trim($id);

    $database->table = 'test';


//    $query[] = "select a.name,q.name as question,q.id,q.a,q.b,q.c,q.d,q.e,q.f,q.answer";
//    $query[] = "from test as a, manage_test as b, question as q";
//    $query[] = "where a.id = b.test_id";
//    $query[] = "and b.question_id = q.id";
//    $query[] = "and a.id = $id";
//    $query = implode(' ', $query);

    $query = "select a.id,a.name as question,a.a,a.b,a.c,a.d,a.e,a.f,a.answer FROM question as a, manage_test as b, test
            where a.id = b.question_id
            and b.test_id = test.id
            and test.id = $id
            and a.id not in (
                select do_question.question_id from do_question
                where user_id = ".$_SESSION['id']."
            )";
    $database->query($query);
    $data = $database->select();

//    echo '<pre>';print_r($data);echo '</pre>';

    foreach ($data as $key => $value) {
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
        $temp .='<hr>
                    </div>';
        $html.=$temp;

    }

    $_SESSION['answer'] = $result;
}

if (isset($_POST['done'])) {
    $database->table = 'do_question';

    array_shift($_POST);
    $result = $_SESSION['answer'];

//    echo '<pre>';print_r($_SESSION['answer']);echo '</pre>';
    $done = $_POST;
//    echo '<pre>';print_r($done);echo '</pre>';

    foreach ($done as $key => $value) {
        $right = $result[$key];
        $check = 0; //Wrong
        if ($right == $value) { // Right
            $check = 1;
        }
        $insert = array('user_id'=>$_SESSION['id'],'question_id'=>$key,'check'=>$check,'answerofuser'=>$value);
        $database->insert($insert,'single');
    }

//    echo 'DO = '.$database->showRows(); echo '<hr>';
//    echo preg_match_all("#.*/(.*)/lambai\.php#",$_SERVER['PHP_SELF'],$link);
//    echo '<pre>';print_r($link);echo '</pre>';
//    die();
    header('location: .');
    exit();
}
?>

<html>
<head>
    <title>LunVjp</title>
    <meta charset="utf-8">
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<div class="form-setup"> <!-- TOP các user làm đề đó vừa làm vừa xem -->

</div>

<div class="content">
    <form action="." method="post" name="form-add" id="form-do-test">
        <input type="hidden" name="done">
        <?php echo $html; ?>
    </form>
    <div class="form-add-submit">
        <button id="submit-button" type="button" name="nopbai">Nộp bài</button>
        <a href=".">Quay lại</a>
    </div>
</div>
<script>
    $(function(){
        $("#submit-button").click(function(){
            $("#form-do-test").submit();
        });
        $("input").click(function(){
            id = $(this).attr("class");
            temp = 'div#'+id;
            x = 'input'+'.'+id;

            setTimeout(function(){
                $(temp).hide();
            },200);
        });
    });

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>