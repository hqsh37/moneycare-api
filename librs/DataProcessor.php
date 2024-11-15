<?php
class DataProcessor {
    public static function assignIfKeyExists($key, &$variable, $assocArray) {
        if (array_key_exists($key, $assocArray)) {
            $variable = $assocArray[$key];
            return true;
        }
        return false;
    }

    public static function generateTimestampString() {
        return (string) round(microtime(true) * 1000);
    }

    public static function removeDataNull($arr) {
        return array_filter($arr, function ($value) {
            return !is_null($value);
        });
    }

    public static function processData($jsonString, $idUser) {
        $_this = new static();
        $jsonData = json_decode($jsonString, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Lỗi khi giải mã JSON: " . json_last_error_msg());
        }

        $idArrays = [];
        $updates = [
            'category' => false,
            'account' => false,
            'transaction' => false,
        ];

        foreach ($jsonData as $operation) {
            $type = $operation['type'];
            $tbl = $operation['tbl'];
            $id = $operation['id'];
            $data = $operation['data'] ?? null;

            // Thực hiện các hành động
            switch ($type) {
                case 'create':
                    $_this->handleCreate($tbl, $id, $data, $idUser, $idArrays, $updates);
                    break;
                case 'update':
                    $_this->handleUpdate($tbl, $id, $data, $idUser, $idArrays, $updates);
                    break;
                case 'delete':
                    $_this->handleDelete($tbl, $id, $idArrays, $updates);
                    break;
            }
        }

        // Cập nhật thời gian trạng thái
        $_this->updateState($idUser, $updates);

        return true;
    }

    protected function handleCreate($tbl, $id, $data, $idUser, &$idArrays, &$updates) {
        $dataPrepare = [];
        
        switch ($tbl) {
            case 'account':
                $updates['account'] = true;
                $dataPrepare = [
                    "id_user" => $idUser,
                    "tentaikhoan" => $data["name"],
                    "loaitaikhoan" => $data["type"],
                    "sotien" => $data["balance"],
                    "diengiai" => $data["desc"] ?? null
                ];
                if (!is_numeric($id)) {
                    $idArrays[$id] = Account::createResultId($this->removeDataNull($dataPrepare));
                } else {
                    Account::createResultId($this->removeDataNull($dataPrepare));
                }
                break;

            case 'category':
                $updates['category'] = true;
                $dataPrepare = [
                    "id_user" => $idUser,
                    "tenhangmuc" => $data["name"],
                    "icon" => $data["icon"],
                    "iconlib" => $data["iconLib"],
                    "loaihangmuc" => $data["type"],
                    "id_CategoryReplace" => $data["ReplaceId"] ?? null,
                    "hanmuccha" => $data["categoryParentId"] ?? 0,
                    "diengiai" => $data["desc"] ?? null
                ];
                if (!is_numeric($id)) {
                    $idArrays[$id] = Categories::createResultId($this->removeDataNull($dataPrepare));
                } else {
                    Categories::create($this->removeDataNull($dataPrepare));
                }
                break;

            case 'transaction':
                $updates['transaction'] = true;
                $dataPrepare = [
                    "id_taikhoan" => $data["account_id"],
                    "id_hangmuc" => $data["category_id"],
                    "sotien" => $data["amount"],
                    "thoigian" => $data["date"],
                    "hinhanh" => $data["image"] ?? null,
                    "loaigiaodich" => $data["type"],
                    "diengiai" => $data["desc"] ?? null
                ];
                
                // Kiểm tra ID của tài khoản và hạng mục
                $this->assignIfKeyExists($dataPrepare['id_taikhoan'], $dataPrepare['id_taikhoan'], $idArrays);
                $this->assignIfKeyExists($dataPrepare['id_hangmuc'], $dataPrepare['id_hangmuc'], $idArrays);

                if (!is_numeric($id)) {
                    $idArrays[$id] = Transactions::createResultId($this->removeDataNull($dataPrepare));
                } else {
                    Transactions::createResultId($this->removeDataNull($dataPrepare));
                }
                break;
        }
    }

    protected function handleUpdate($tbl, $id, $data, $idUser, &$idArrays, &$updates) {
        $dataPrepare = [];

        switch ($tbl) {
            case 'account':
                $updates['account'] = true;
                $dataPrepare = [
                    "tentaikhoan" => $data["name"],
                    "loaitaikhoan" => $data["type"],
                    "sotien" => $data["balance"],
                    "diengiai" => $data["desc"] ?? null
                ];
                $this->assignIfKeyExists($id, $id, $idArrays);
                Account::update(["id" => $id], $this->removeDataNull($dataPrepare));
                break;

            case 'category':
                $updates['category'] = true;
                $uidUser = Categories::find(["id" => $id], '`id_user`')->id_user;
                if ($uidUser === $idUser) {
                    $dataPrepare = [
                        "tenhangmuc" => $data["name"],
                        "icon" => $data["icon"],
                        "iconlib" => $data["iconLib"],
                        "loaihangmuc" => $data["type"],
                        "id_CategoryReplace" => $data["ReplaceId"] ?? null,
                        "hanmuccha" => $data["categoryParentId"] ?? 0,
                        "diengiai" => $data["desc"] ?? null
                    ];
                    $this->assignIfKeyExists($id, $id, $idArrays);
                    Categories::update(["id" => $id], $this->removeDataNull($dataPrepare));
                    
                } else {
                    $dataPrepare = [
                        "id_user" => $idUser,
                        "tenhangmuc" => $data["name"],
                        "icon" => $data["icon"],
                        "iconlib" => $data["iconLib"],
                        "loaihangmuc" => $data["type"],
                        "id_CategoryReplace" => $id ?? null,
                        "hanmuccha" => $data["categoryParentId"] ?? 0,
                        "diengiai" => $data["desc"] ?? null
                    ];
                    
                    Categories::create($this->removeDataNull($dataPrepare));
                }
                break;

            case 'transaction':
                $updates['transaction'] = true;
                $dataPrepare = [
                    "id_taikhoan" => $data["account_id"],
                    "id_hangmuc" => $data["category_id"],
                    "sotien" => $data["amount"],
                    "thoigian" => $data["date"],
                    "hinhanh" => $data["image"] ?? null,
                    "loaigiaodich" => $data["type"],
                    "diengiai" => $data["desc"] ?? null
                ];
                $this->assignIfKeyExists($dataPrepare['id_taikhoan'], $dataPrepare['id_taikhoan'], $idArrays);
                $this->assignIfKeyExists($dataPrepare['id_hangmuc'], $dataPrepare['id_hangmuc'], $idArrays);
                Transactions::update(["id" => $id], $this->removeDataNull($dataPrepare));
                break;
        }
    }

    protected function handleDelete($tbl, $id, &$idArrays, &$updates) {
        switch ($tbl) {
            case 'account':
                $updates['account'] = true;
                $this->assignIfKeyExists($id, $id, $idArrays);
                Account::delete(["id" => $id]);
                break;

            case 'category':
                $updates['category'] = true;
                $this->assignIfKeyExists($id, $id, $idArrays);
                Categories::delete(["id" => $id]);
                break;

            case 'transaction':
                $updates['transaction'] = true;
                $this->assignIfKeyExists($id, $id, $idArrays);
                Transactions::delete(["id" => $id]);
                break;
        }
    }

    protected function updateState($idUser, $updates) {
        $updateFields = [];
        if ($updates['category']) $updateFields['categoryAt'] = $this->generateTimestampString();
        if ($updates['account']) $updateFields['accountAt'] = $this->generateTimestampString();
        if ($updates['transaction']) $updateFields['transactionAt'] = $this->generateTimestampString();

        if (!empty($updateFields)) {
            Auth::update(["id" => $idUser], $updateFields);
        }
    }
}
