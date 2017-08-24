<?php
require_once "connect.php";
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
if (isset($_POST['id']) && $_POST['type'] == 'wronganswer') {
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
}

if (isset($_POST['id']) && $_POST['type'] == 'rightanswer') {
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
}



if (isset($_POST['id']) && $_POST['type'] == 'done') {
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

            $xhtml .= '<p style="display:block;font-size:13px;font-family:Arial;line-height:30px;font-weight: 600;height:30px;color: '.$color.';background:#e9ebee">'.strtoupper($value['answer']).' - Trả lời '.strtoupper($value['answerofuser']).'</p></div>';

        }
//                $xhtml .= '</div>';
    }
    echo $xhtml;
}