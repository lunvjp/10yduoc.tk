<?php

class Database {
    private $connect;
    private $database;
    private $table;
    private $queryResult;

    public function __construct($data) {
        $link = @mysql_connect($data['server'],$data['username'],$data['password']);
        if (!$link) die('<h3>Connect fail: </h3>'.mysql_error());
        $this->connect = $link;
        $this->database = $data['database'];
        mysql_select_db($this->database);
        $this->table = $data['table'];
        mysql_set_charset('utf8',$this->connect);
    }

    public function __destruct() {
        mysql_close($this->connect);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name,$value) {
        return $this->$name = $value;
    }

    public function createInsertSql($data) {
        if (!empty($data)) {
            $array = array('cols'=>'','vals'=>'');
            foreach($data as $key => $value) {
                $array['cols'] .= "`$key`,";
                $array['vals'] .= "'$value',";
            }
            $array['cols'] = rtrim($array['cols'],',');
            $array['vals'] = rtrim($array['vals'],',');
            return $array;
        }
    }

    public function insert($dulieu, $type = 'single') {
        if ($type == 'single') { // Thêm 1 dòng
            $data = $this->createInsertSql($dulieu);
            $query = "INSERT INTO `$this->table` (".$data['cols'].") VALUES (".$data['vals'].")";
            $this->query($query);
        } else if ($type == 'multi') { // Thêm nhiều dòng
            foreach ($dulieu as $key => $value) {
                $data = $this->createInsertSql($value);
                $query = "INSERT INTO `$this->table` (".$data['cols'].") VALUES (".$data['vals'].")";
                $this->query($query);
            }
        }
    }

    public function update($data, $condition = null) {
        if (!empty($data)) {
            $giatri = '';
            foreach($data as $key => $value) {
                $giatri .= "`$key`='$value',";
            }
            $giatri = rtrim($giatri,',');

            if ($condition != null) {
                $where = array();
                foreach ($condition as $key => $value) {
                    $where[] = "`$key`='$value'";
                }
                $where = implode('and',$where);
                $query = "UPDATE `$this->table` SET $giatri WHERE $where";
            } else $query = "UPDATE `$this->table` SET $giatri";

            $this->query($query);
        }
    }

    public function delete($name,$ids) {
        if (!empty($ids)) {
            $ids = "'".implode("','",$ids)."'";
            $query = "delete from `$this->table` where `$name` in ($ids)";
            $this->query($query);
        }
    }

    public function query($query) {
        return $this->resultQuery = @mysql_query($query,$this->connect);
    }

    public function showRows() {
        return mysql_affected_rows();
    }

    public function showErrors() {
        return mysql_error($this->connect);
    }

    public function select() {
        $result = array();
        $resultQuery = $this->resultQuery;
//        if (mysql_affected_rows() == 1) { // Có 1 dòng
//            return mysql_fetch_assoc($resultQuery);
//        }
        if (mysql_affected_rows() > 0) { // Có nhiều dòng
            while($row = mysql_fetch_assoc($resultQuery)) {
//                if (count($row)==1) { // nếu mảng có 1 phần tử
//                    $result[] = array_values($row)[0];
//                 }
//                else $result[] = $row;
                $result[] = $row;
            }
        }
        return $result;
    }
}