<?php
/*

 * * TODO:
 * 2. Trang xem tổng hợp các đề chỉ có tài khoản mới vào được.
 * 3. Có 1 danh sách các đề ở bên trái giống như w3schools.com hiển thị số % làm bài
 */
echo '<hr>';
require_once "connect.php";
require_once "functions.php";
session_start();
session_unset();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $path = "./files/$id.ini";
    if (!parse_ini_file($path)) {
        header("location: .");
        exit();
    }
}

if (isset($_SESSION['title'])) unset($_SESSION['title']);
?>

<html>
<head>
    <title>LunVjp</title>
    <meta charset="utf-8">
    <link href="./css/index-css.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>
<body>

<!--<div class="chucnang">-->
    <ul>
        <li id="sum-sentence"><?php
            $count = parse_ini_file('./notes/count.ini');
            $count = $count['count'];

            $x = glob('./files/*.ini');
            foreach ($x as $key => $value) {
                $temp = parse_ini_file($value);
                $count += count($temp)-1;
            }

            echo "TỔNG: $count câu";

            ?></li>
        <a href="themde.php"><li>Thêm đề mới</li></a>
    </ul>
<!--</div>-->
<!--<div class="form-setup">-->
    <ol>

    </ol>
<!--</div>-->

<!--<div class="content"> <!-- Hiển thị tất cả câu hỏi có trong CSDL -->-->
    <?php
//    echo 'Chỉ số xuất hiện cuối cùng của'.strripos('@','LunVjp@').'<br>';
    $database->table = 'question';

    //----------------------------------------------------------------------------------
    // Tạo bộ đề gồm có 40 câu hỏi trong đề.
    // Mỗi bộ đề lấy các câu hỏi từ bảng question
    $query[] = "select `id`";
    $query[] = "from `question`";
    $query = implode('',$query);
    $database->query($query);
    $ketqua = $database->select();

    $count = 0;
//    echo '<pre>';print_r($ketqua);echo '</pre>';
    $index = 619; // 378 // CHANGE
    // Vị trí cuối cùng của câu trong đề 2 là 61
    $database->table = 'manage_test'; // Quản lí đề
    foreach($ketqua as $key => $value) {
        $unitID = substr($value,0,20);

        if ($unitID == 'w9GxbSivuoKZl6D0ykr2') { // 121 câu
            if ($key>$index && $key <=$index+40) {
                $unitID = substr($value,0,20);

                    $array = array('question_id'=>$value,'test_id'=>18); // CHANGE 12
//                  echo '<pre>';print_r($array);echo '</pre>';
                    $database->insert($array,'single');
                    $count++;
                }
        }
        if ($count>=33) { // CHANGE
            $index = $key; // vị trí cuối cùng của câu hỏi
            break;
        }
    }

     echo '<hr>';
    echo 'INDEX = '.$index;
    echo 'COUNT = '.$count.'<br>';
    echo 'Error: '.$database->showErrors();
    echo 'TODO: '.$database->showRows();

//function taoQuestion() {
//    $file = './files/*';
//    $data = glob($file);
//
//    echo '<pre>';print_r($data);echo '</pre>';
//
//    $item = $data[7];
//    $daucham = strripos($item,'.');
//    $dauhuyen = strripos($item,'/');
//    $filename = substr($item,$dauhuyen+1,$daucham-$dauhuyen-1);
//
//
//    echo $filename;
//    $ketqua = parse_ini_file($item);
////    echo '<pre>';print_r($ketqua);echo '</pre>';
//    foreach($ketqua as $key => $value) {
//
//        if (is_int($key)) {
//            $id = $filename.$key;
//            $temp = explode('|',$value);
////        echo '<pre>';print_r($temp);echo '</pre>';
//            $e = '';
//            $f = '';
//
//            $temp = array_unique($temp);
//            $name = $temp[0];
//            $a = $temp[1];
//            $b = $temp[2];
//            $c = $temp[3];
//            $d = $temp[4];
//            if (count($temp) >=6) $e = $temp[5];
//            if (count($temp) == 7) $f = $temp[6];
//
//            $dulieu = array();
//
//            $dapan = '';
//
////            if ($key>=81) {
////                $chiso = stripos($name,'<');
////                $name = str_split($name,1);
////                echo $dapan = $name[$chiso-1];
////                unset($name[$chiso-1]);
////                $name=implode('',$name);
//////                echo $name;
//////                die();
////            }
//
//            if (strripos($a,'@')) {
//                $dapan='a';
//            } else if (strripos($b,'@')) {
//                $dapan='b';
//            }else if (strripos($c,'@')) {
//                $dapan='c';
//            }else if (strripos($d,'@')) {
//                $dapan='d';
//            }else if (strripos($e,'@')) {
//                $dapan='e';
//            }else if (strripos($f,'@')) {
//                $dapan='f';
//            }
//            if (!$dapan) die('DAP AN CHUA CO');
//            $a = str_replace('@','',$a);
//            $b = str_replace('@','',$b);
//            $c = str_replace('@','',$c);
//            $d = str_replace('@','',$d);
//            $e = str_replace('@','',$e);
//            $f = str_replace('@','',$f);
//
//            $dulieu['id'] = $id;
//            $dulieu['name'] = $name;
//            $dulieu['a'] = $a;
//            $dulieu['b'] = $b;
//            $dulieu['c'] = $c;
//            $dulieu['d'] = $d;
//            $dulieu['e'] = $e;
//            $dulieu['f'] = $f;
//            $dulieu['answer'] = $dapan;
////            $dulieu = array('id'=>$id,'name'=>$name,'a'=>$a,'b'=>$b,'c'=>$c,'d'=>$d,'answer'=>$dapan);
//            $database->insert($dulieu); // insert từng dòng 1
////            echo '<pre>';print_r($dulieu);echo '</pre>';
//
//        }
//    }
//
//    echo $database->showRows();
//    echo $database->showErrors();
//}


    ?>

    <?php
//    if (isset($_GET['id'])) {
//        $id = trim($_GET['id']);
//        $filename = "./files/$id.ini";
//        $data = @parse_ini_file($filename);
//
//        foreach ($data as $key => $value) {
//            if (is_int($key)) {
//                $array = explode('|', $value);
//                $cauhoi = $array[0];
//                $caua = $array[1];
//                $caub = $array[2];
//                $cauc = $array[3];
//                $caud = $array[4];
//                $caue = $array[5];
//
//                echo '<div class="question">
//                        <div class="item">
//                            <p class="title">Câu ' . $key . '.</p>
//                            <p class="title-content">' . $cauhoi . '</p>
//                        </div>
//                        <div class="item">
//                            <p class="answer">A.</p>
//                            <p>' . $caua . '</p>
//                        </div>
//                        <div class="item">
//                            <p class="answer">B.</p>
//                            <p>' . $caub . '</p>
//                        </div>
//                        <div class="item">
//                            <p class="answer">C.</p>
//                            <p>' . $cauc . '</p>
//                        </div>
//                        <div class="item">
//                            <p class="answer">D.</p>
//                            <p>' . $caud . '</p>
//                        </div>
//                        <div class="item">
//                            <p class="answer">E.</p>
//                            <p>' . $caue . '</p>
//                        </div>
//                        <hr>
//                    </div>';
//            }
//        }
//    }
    ?>
    <div class="form-add-submit">
        <!--        <button id="submit-button" type="button" name="submit">Thêm dữ liệu</button>-->
        <!--        <button><a href=".">Quay lại</a></button>-->
    </div>
<!--</div>-->
</body>
</html>