<?php
require_once "../connect.php";
session_start();

if (isset($_POST['title'])) {
    $title = $_POST['title'];
    if (!$title) echo "<div class='error'>You can't leave this empty.</div>";
}

if (isset($_POST['sentence'])) {
    $sentence = $_POST['sentence'];
    if (!$sentence) echo "<div class='error'>You can't leave this empty.</div>";
}

if (isset($_POST['question'])) {
    echo $_POST['question'];
}

//echo '<pre>';print_r($_POST);echo '</pre>';
if (isset($_POST['id']) && isset($_POST['type'])) {
//    unset($_SESSION['action']);
    $_SESSION['testid'] = $_POST['id'];

    if ($_POST['type'] == 'wronganswer') {
        //    $query = "";
        $query =    "SELECT b.id, b.name, b.a, b.b, b.c, b.d, b.e, b.f, a.check, a.answerofuser, b.answer
                    from do_question as a, question as b, manage_test as c, test as d
                    where a.question_id = b.id
                    and c.question_id = b.id
                    and c.test_id = d.id
                    and a.user_id = ".$_SESSION['username']."
                    and d.id = ".$_POST['id']." 
                    and a.check = 0";
        $database->query($query);
        $detailofdonesentence = $database->select();

        $xhtml = '';
        if (!empty($detailofdonesentence))
        {
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

                $xhtml .= '<p style="padding-left:10px;display:block;font-size:13px;font-family:Arial;line-height:30px;font-weight: 600;height:30px;color: '.$color.';background:#e9ebee">'.strtoupper($value['answer']).' - Trả lời '.strtoupper($value['answerofuser']).'</p></div>';

            }
//                $xhtml .= '</div>';
        }
        echo $xhtml;
    } else if ($_POST['type'] == 'rightanswer') {
        //    $query = "";
        $query =    "SELECT b.id, b.name, b.a, b.b, b.c, b.d, b.e, b.f, a.check, a.answerofuser, b.answer
                    from do_question as a, question as b, manage_test as c, test as d
                    where a.question_id = b.id
                    and c.question_id = b.id
                    and c.test_id = d.id
                    and a.user_id = ".$_SESSION['username']."
                    and d.id = ".$_POST['id']." 
                    and a.check = 1";
        $database->query($query);
        $detailofdonesentence = $database->select();

        $xhtml = '';
        if (!empty($detailofdonesentence))
        {
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

                $xhtml .= '<p style="padding-left:10px;display:block;font-size:13px;font-family:Arial;line-height:30px;font-weight: 600;height:30px;color: '.$color.';background:#e9ebee">'.strtoupper($value['answer']).' - Trả lời '.strtoupper($value['answerofuser']).'</p></div>';

            }
//                $xhtml .= '</div>';
        }
        echo $xhtml;
    } else if ($_POST['type'] == 'done') {
        $query =    "SELECT b.id, b.name, b.a, b.b, b.c, b.d, b.e, b.f, a.check, a.answerofuser, b.answer
                    from do_question as a, question as b, manage_test as c, test as d
                    where a.question_id = b.id
                    and c.question_id = b.id
                    and c.test_id = d.id
                    and a.user_id = ".$_SESSION['username']."
                    and d.id = ".$_POST['id']."";
        $database->query($query);
        $detailofdonesentence = $database->select();

        $xhtml = '';
        if (!empty($detailofdonesentence))
        {
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

                $xhtml .= '<p style="padding-left:10px;display:block;font-size:13px;font-family:Arial;line-height:30px;font-weight: 600;height:30px;color: '.$color.';background:#e9ebee">'.strtoupper($value['answer']).' - Trả lời '.strtoupper($value['answerofuser']).'</p></div>';

            }
//                $xhtml .= '</div>';
        }
        echo $xhtml;
    }
}

if (isset($_POST['id']) && isset($_POST['action'])) {
//    $_SESSION['action'] = 'dotest';

    $content = '<form method="post" name="form-add" id="form-do-test"><input type="hidden" name="done" value="'.$_POST['id'].'">';
    $id = htmlspecialchars($_POST['id']);
    $id = trim($id);

    $database->table = 'test';

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

    $result = array();
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
        $content .=$temp;

    }
    $content .='</form>';
    echo $content;
    $_SESSION['answer'] = $result; // mảng kết quả
}