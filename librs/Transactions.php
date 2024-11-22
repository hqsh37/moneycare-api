<?php
class Transactions extends Database
{
    protected $table = "khoanthuchi";

    public static function finds($data)
    {
        $_this = new static();
        $result = [];
        $rule = "";
        foreach ($data as $key => $value) {
            $rule .= "$key = '$value' AND ";
        }
        $rule = rtrim($rule, "AND ");
        $sql = "SELECT `khoanthuchi`.* FROM `{$_this->table}` LEFT JOIN `taikhoan` ON `khoanthuchi`.`id_taikhoan` = `taikhoan`.`id` WHERE {$rule} ORDER BY `khoanthuchi`.`thoigian` DESC;";
        $query = $_this->conn->query($sql);
        $_this->conn->close();
        while ($row = $query->fetch_object()) {
            $result[] = $row;
        }
        return $result;
    }
}
