<meta charset="utf-8">
<?php
function checkString($value) {
    if (isset($value)&&trim($value)!="") return true;
    return false;
}

function designString($value,$length=40) {
    $array = explode(' ',$value);
    $temp = '';
    $count = 0;
    foreach ($array as $key => $value) {
        if ($count>$length) break;
        if (trim($value)) {
            $temp .= $value.' ';
            $count+=strlen($value);
        }
    }
    return $temp;
}

function checkLength($value,$min) {
    if (strlen($value)<$min) return false;
    return true;
}

function randomString($length=5) {
    // Tạo ra 1 mảng các kí tự từ A-Z,a-z,0-9
    $charList = array_merge(range('A','Z'),range('a','z'),range(0,9));
    // Nối mảng lại tạo ra 1 chuỗi
    $str = implode($charList,'');
    // random chuỗi: random các kí tự trong chuỗi
    $str = str_shuffle($str);
    // lấy $length kí tự đầu tiên
    $result = substr($str,0,$length);
    // trả về chuỗi đó là xong
    return $result;
}

function checkExist($val,$index,$fileName,$delimiter) { // $fileName = files/*txt
    $listFile = glob($fileName);
    foreach ($listFile as $key => $value) {
        $temp = file_get_contents($value);
        $temp = explode($delimiter, $temp);
        $item = $temp[$index];
        if ($item == trim($val)) {
            return true;
        }
    }
    return false;
}

function setSize($size,$length) {
    $listSize = array('B','KB','MB','GB','TB');

    $behind = "";
    foreach($listSize as $key => $value ) {
        if ($size >=1024) {
            $size/=1024;
        } else {
            $behind = $value;
            break;
        }
    }
    return round($size,$length).' '.$behind;
}

?>