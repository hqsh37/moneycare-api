<?php
echo "<pre />";
class DataProcessor {
  // $key: Biến cần kiểm tra
  // $variable: Biến sẽ gán giá trị nếu key tồn tại
  public static function assignIfKeyExists($key, &$variable, $assocArray) {
    if (array_key_exists($key, $assocArray)) {
        // Gán giá trị của key vào biến
        $variable = $assocArray[$key];
        return true;
    } else {
        return false;
    }
  }
  public static function processData($jsonString, $idUser) {
    $idUser = $idUser;
    // Giải mã chuỗi JSON thành mảng
    $jsonData = json_decode($jsonString, true);

    // Kiểm tra nếu dữ liệu JSON bị lỗi
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Lỗi khi giải mã JSON: " . json_last_error_msg());
    }

    $idArrays = [];
    $staticArray = [];

    foreach ($jsonData as $operation) {
        $type = $operation['type'];
        $tbl = $operation['tbl'];
        $id = $operation['id'];
        $data = isset($operation['data']) ? $operation['data'] : null;

        switch ($type) {
          case 'create':
            switch($tbl) {
                case 'account':
                  $id_user = $idUser;
                  $tentaikhoan = $data["name"];
                  $loaitaikhoan = $data["type"];
                  $sotien = $data["balance"];
                  $diengiai = $data["desc"] ?? null;

                  $dataPrepare = [
                    "id_user" => $id_user,
                    "tentaikhoan" => $tentaikhoan,
                    "loaitaikhoan" => $loaitaikhoan,
                    "sotien" => $sotien,
                    "diengiai" => $diengiai
                  ];
                  if (!is_numeric($id)) {
                      $idArrays[$id] = Account::createResultId($dataPrepare);
                  } else {
                    Account::createResultId($dataPrepare);
                  }
                  break;

                case 'category':
                    $id_user = $idUser;
                    $tenhangmuc = $data["name"];
                    $icon = $data["icon"];
                    $iconlib = $data["iconLib"];
                    $loaihangmuc = $data["type"];
                    $categoryReplaceId = $data["ReplaceId"] ?? null;
                    $diengiai = $data["desc"] ?? null;

                    $dataPrepare = [
                        "id_user" => $id_user,
                        "tenhangmuc" => $tenhangmuc,
                        "icon" => $icon,
                        "iconlib" => $iconlib,
                        "loaihangmuc" => $loaihangmuc,
                        "categoryReplaceId" => $categoryReplaceId,
                        "diengiai" => $diengiai,
                      ];
                    
                    if (!is_numeric($id)) {
                        $idArrays[$id] = Categories::createResultId($dataPrepare);
                    } else {
                        Categories::createResultId($dataPrepare);
                    }
                    break;

                case 'transaction':
                    $id_taikhoan = $data["account_id"];
                    $id_hangmuc = $data["category_id"];
                    $sotien = $data["amount"];
                    $thoigian = $data["date"];
                    $hinhanh = $data["image"] ?? null;
                    $loaigiaodich = $data["type"];
                    $diengiai = $data["desc"] ?? null;

                    if(!is_numeric($id_taikhoan)) {
                        $_this = new static();
                        $_this->assignIfKeyExists($id_taikhoan, $id_taikhoan, $idArrays);
                    }
                    
                    if(!is_numeric($id_hangmuc)) {
                        $_this = new static();
                        $_this->assignIfKeyExists($id_hangmuc, $id_hangmuc, $idArrays);
                    }

                    $dataPrepare = [
                        "id_taikhoan" => $id_taikhoan,
                        "id_hangmuc" => $id_hangmuc,
                        "sotien" => $sotien,
                        "thoigian" => $thoigian,
                        "hinhanh" => $hinhanh,
                        "loaigiaodich" => $loaigiaodich,
                        "diengiai" => $diengiai, 
                    ];
                    
                    if (!is_numeric($id)) {
                          $idArrays[$id] = Transactions::createResultId($dataPrepare);
                    } else {
                        Transactions::createResultId($dataPrepare);
                    }
                    break;
                }
                break;

          case 'update':
            switch($tbl) {
              case 'category':
                $staticArray[$tbl][$id] = $data;
                break;
              case 'account':
                $staticArray[$tbl][$id] = $data;
                break;
            }
            break;

          case 'delete':
            if (isset($staticArray[$tbl][$id])) {
                unset($staticArray[$tbl][$id]);
            }
            switch($tbl) {
              case 'category':
                $staticArray[$tbl][$id] = $data;
                break;
              case 'account':
                $staticArray[$tbl][$id] = $data;
                break;
            }
            break;
        }
    }

    return $idArrays;
  }
}