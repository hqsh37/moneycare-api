<?php
class Savings extends Database
{
    protected $table = "sotietkiem";

    public static function findAllSavings($idUser)
    {
        $_this = new static();

        $sql = "SELECT `sotietkiem`.* FROM `user` LEFT JOIN `taikhoan` ON `taikhoan`.`id_user` = `user`.`id` LEFT JOIN `sotietkiem` ON `sotietkiem`.`id_taikhoan` = `taikhoan`.`id` WHERE `sotietkiem`.`id` AND `user`.`id` = {$idUser} ORDER BY `sotietkiem`.`trangthai` ASC";
        $query = $_this->conn->query($sql);
        $_this->conn->close();
        while ($row = $query->fetch_object()) {
            $result[] = $row;
        }

        return $result;
    }
}
