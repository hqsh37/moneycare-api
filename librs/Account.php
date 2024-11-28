<?php
class Account extends Database
{
    protected $table = "taikhoan";

    public static function updateAmount($id, $amount)
    {
        $_this = new static();
        $sql = "UPDATE `taikhoan` SET `taikhoan`.`sotien` = `taikhoan`.`sotien` + '{$amount}' WHERE id = '{$id}';";
        $query = $_this->conn->query($sql);
        $_this->conn->close();
        return $query;
    }
}
