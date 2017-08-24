<?php
/*
 * Khi bắt đầu bấm vào nút OK thì tạo 1 session lưa vào time() hiện tại
 * Khi thằng đấy đang làm bài thì thời gian bên trang đấy vẫn đang chạy và click vào đề đó là làm luôn, không hỏi lại nữa
 *
 * Bây giờ phải lấy ra thời gian hiện tại (ở server) khi nó click vào nút yes
 * 2. Lấy thời gian đó + thêm 30 phút rồi truyền vào cho thằng đếm countdown
 *
 * Khi mà hết thời gian thì nó sẽ lưu
 * */
require_once "../class/Database.php";
require_once "../connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header('location: ..');
    exit();
}

if (isset($_POST['done'])) {

    $database->table = 'do_question';

    array_shift($_POST); // Xóa thằng input bị che đi
    $result = $_SESSION['answer'];

//    echo '<pre>';print_r($_SESSION['answer']);echo '</pre>';
    $done = $_POST;
//    echo '<pre>';print_r($done);echo '</pre>';

//    die();
    foreach ($done as $key => $value) {
        $right = $result[$key];
        $check = 0; //Wrong
        if (strtolower($right) == strtolower($value)) { // Right
            $check = 1;
        }
        $insert = array('user_id'=>$_SESSION['id'],'question_id'=>$key,'check'=>$check,'answerofuser'=>$value);
        $database->insert($insert,'single');
    }

    header('location: .');
    exit();
}

$result = array();
$data = array();


$html = '';
$account = '';
if (isset($_SESSION['username'])) { // đăng nhập thành công
    $account = "<a href='../logout.php'>Đăng xuất</a>";
    $account .= ' Chào ' . $_SESSION['username'];

    $database->table = 'test';
    $query = "select a.id,a.name,count(b.question_id) as total from test as a, manage_test as b
              where a.id = b.test_id
              group by a.id";
    $database->query($query);
    $data = $database->select();

    /* Lấy ra các id, tên của bộ đề, số câu của hỏi trong 1 đề
     * Điều kiện để được làm bài test là
     * + Không có câu hỏi nào trong bảng do_question
     * + Không có đề nào trong bảng do_test
     * */
    $donesentence = "select e.id, e.name, count(c.id) as donetotal
                    from user as a, do_question as b, question as c, manage_test as d, test as e, unit, subject
                    where a.id = b.user_id
                    and b.question_id = c.id
                    and d.question_id = c.id
                    and e.id = d.test_id
                    and e.unit_id = unit.id
                    and unit.subject_id = subject.id
                    and a.id = " . $_SESSION['id'] . "
                    and subject.id = 1
                    group by e.id";
    $database->query($donesentence);
    $donesentencelist = $database->select();

//    echo '<pre>';print_r($donesentencelist);echo '</pre>'; die();
    foreach ($data as $key => $value) {
        $idsentence = $value['id']; // tất cả mã đề từ bảng dữ liệu
//        echo '<pre>';print_r($donesentencelist);echo '</pre>';
        $check = false;
        if (!empty($donesentencelist)) {
            foreach ($donesentencelist as $index => $val) { // duyệt qua vòng lặp này để xem id của đề có bằng id của trong bảng do_question hay không
//                echo gettype($val);
//            print_r($val);
                if ($idsentence == $val['id']) { // nếu mã đề đó có câu hỏi trong bảng do_question hoặc là có trong bảng do_test thì đều rơi vào if này
                    // href=".?id=' . $value["id"] . '
//                if (in_array($idsentence,$val)) { // Nếu mã đề đã làm rồi thì khi click vào link sẽ hiển thị các câu đã làm
                    $html .= '<li>
                            <span><a style="cursor: pointer;" onclick="seeResult('.$value['id'].')">' . $value["name"] . '</a></span> 
                            <span style="padding-left:10px;color: yellowgreen">' . $val['donetotal'] . '/' . $value['total'] . '</span>
                            <span style="padding-left:10px;"><a style="cursor: pointer;color: yellowgreen" onclick="doMoreQuestion('.$value['id'].')">Làm tiếp</a></span>
                        </li>';
                    $check = true;
                    break;
                }
            }
        }

        // Nếu ra đây check vẫn bằng false thì kiểm tra 1 lần nữa
        if ($check == false) {
//            $database->table = 'do_test';
            $query = "select * from do_test where test_id = $idsentence and user_id = ".$_SESSION['id']."";
            $database->query($query);
            $temp = $database->select();

//            echo '<pre>';print_r($temp);echo '</pre>';
//            die();
            if (!empty($temp)) // tồn tại đề trong bảng do_test {
            {
                $check=true;
                $html .= '<li>
                            <span><a style="cursor: pointer;" onclick="seeResult('.$value['id'].')">' . $value["name"] . '</a></span> 
                            <span style="padding-left:10px;color: yellowgreen">0/' . $value['total'] . '</span>
                            <span style="padding-left:10px;"><a style="cursor: pointer;color: yellowgreen" onclick="doMoreQuestion('.$value['id'].')">Làm tiếp</a></span>
                        </li>';
            }
        }



        //                 <span style="padding-left:10px;color: yellowgreen">'.$value['total'].'</span>
        if ($check == false) { // Chưa làm 1 câu hỏi nào trong mã đề này
            $html .= '<li>
                    <span><a style="cursor: pointer" data-toggle="modal" data-target="#myModal" onclick="getLink('.$value['id'].')">' . $value["name"] . '</a></span>
                </li>';
//            break;
            // href="?action=dotest&id=' . $value["id"] . '"
            // onclick thì window
        }
    }

//    echo $_SERVER['PHP_SELF'];
//    $test =  parse_url($_SERVER['HTTP_REFERER']);
//    echo '<pre>';print_r($test);echo '</pre>';
//    echo preg_match_all("#.*/(.*)/lambai\.php#",$_SERVER['PHP_SELF'],$link);
//    echo '<pre>';print_r($link);echo '</pre>';
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
            background: lightskyblue;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .form-setup a:hover {
            text-decoration: none;
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
    <script>

    </script>
</head>
<body>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thông báo</h4>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn làm đề này không?</p>
            </div>
            <div class="modal-footer">
                <button id="yes" type="button" class="btn btn-success" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>

    </div>
</div>



<div class="mynavbar">
    <!--                <a href="" class="topnav-icons fa fa-menu w3-hide-large w3-left w3-bar-item w3-button" title="Menu"></a>-->
    <a href=".." title="Home"><i class="fa fa-home" style="line-height: 35px;"  aria-hidden="true"></i></a>
    <a href="." title="Làm Đề Giải Phẫu">GIẢI PHẪU</a>
    <a href="../sinhditruyen" title="Làm Đề Sinh Di Truyền">SINH DI TRUYỀN</a>
    <div style="float: right;margin-right:10px;">
        <?php echo $account; ?>
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
    <ol><?php echo $html; ?>
    </ol>
</div>

<div class="content"> <!-- Hiển thị số câu đã làm ở đây -->
    <div id="ajax-load" style="display: none; height: 100px; width: 160px; margin: auto; margin-top:20px">
        <i class="fa fa-spinner fa-spin" style="font-size: 7em; color: #D9ECFF;"></i>
    </div>
    <div id="time" style="position:fixed;width:100%;display: none;background: lightskyblue; height:40px;border-bottom:1px solid grey;font-size:25px;font-weight: bold;font-family: Arial,sans-serif;color: #ffff80;line-height: 40px;padding-left:10px;"><span>BẮT ĐẦU</span></div>
    <div id="choiceuser"></div>
    <div class="form-add-submit" style="position: fixed;">
        <?php
        // Session này sẽ kéo dài đúng với số thời gian của đề
        // nếu còn thời gian làm bài thì nút nộp bài còn hiển thị
        /*
         * Khi nào ổn định thì cài chỉ cần click vào input là nộp bài, kết quả lưu lại. hết giờ làm bài mới đc xem kết quả
         *
         */
//        if (isset($_SESSION['action']) && $_SESSION['action'] == 'dotest') {
            ?>


            <form method="post" name="form-edit">
                <input type="hidden" id="wrongsentence" name="wrongsentence"
                       value="<?php if (isset($_SESSION['testid'])) echo $_SESSION['testid']; ?>">
                <button type="button" id="wrong-button">Bài làm sai</button>
                <button type="button" id="right-button">Bài làm đúng</button>
                <button type="button" id="submit-button">Nộp bài</button>
                <a href=".">Quay lại</a>
            </form>
    </div>
</div>
<script>
    $(function(){
        $("input").click(function(){
            console.log('ngu');
            id = $(this).attr("class");
            temp = 'div#'+id;
            x = 'input'+'.'+id;

//            $("input."+id).prop('disabled','true');
            alert(id);

            setTimeout(function(){
                $(temp).hide();
            },200);
        });
    });
</script>
<script src="check.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
