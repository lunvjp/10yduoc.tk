<?php
require_once "../connect.php";
session_start();

$xhtml = '';
// Lấy ra thông tin câu hỏi và ô comment facebook của câu hỏi này trong 1 trang full màn hình giống như youtube.
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT b.id, b.name, b.a, b.b, b.c, b.d, b.e, b.f, a.check, a.answerofuser, b.answer
                    from do_question as a
                    inner join question as b on a.question_id = b.id
                    and a.user_id = ".$_SESSION['id']."
                    and b.id = $id";
    $database->query($query);
    $detailofdonesentence = $database->select();

    echo '<pre>';print_r($detailofdonesentence);echo '</pre>';

    if (!empty($detailofdonesentence)) {
//                $xhtml = '<div class="container-fluid">';
        foreach($detailofdonesentence as $key => $value) {
            $xhtml .='<div class="question">
                        <div class="item">
                            <p class="title">Câu ' . ($key+1) . '.</p>
                            <p class="title-content">' . $value['name'] . '</p>
                        </div>';
            $temp['A'] = $value['a'];
            $temp['B'] = $value['b'];
            $temp['C'] = $value['c'];
            $temp['D'] = $value['d'];
            if ($value['e']) $temp['E'] = $value['e'];
            if ($value['f']) $temp['F'] = $value['f'];

            foreach($temp as $key2 => $value2) {
                $xhtml .= '<div class="item">
                                <p class="answer">'.$key2.'.</p>
                                <p>' . $value2 . '</p>
                            </div>';
            }

            $color = $value['check']==0 ? 'red':'blue';

            $xhtml .= '<p style="padding-left:10px;display:block;font-size:13px;font-family:Arial,sans-serif;line-height:30px;font-weight: 600;height:30px;color: '.$color.';background:#e9ebee">'.strtoupper($value['answer']).' - Trả lời '.strtoupper($value['answerofuser']).'</p></div>';

        }
//                $xhtml .= '</div>';
    } else {
        echo 'Dữ liệu không tồn tại';
    }

}
?>
<html>
<head>
    <link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
echo $xhtml;
?>
</body>
</html>
