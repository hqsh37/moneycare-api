<?php

// [GET] savings/
if ($this->getmethod() === "GET") {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);
    if ($user) {
        $savings = Savings::findAllSavings($user["uid"]);

        if ($savings) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful",
                "data" => $savings
            ]);
        } else {
            http_response_code(204);
            echo json_encode(
                ["status" => "success", "message" => "No data available"]
            );
        }
    } else {
        http_response_code(401);
        echo json_encode(
            ["status" => "error", "message" => "JWT token not valid"]
        );
    }
}

// [POST] account/
if ($this->getmethod() === "POST") {
    $idTaikhoan = $_REQUEST["idTaikhoan"];
    $sodubandau = $_REQUEST["sodubandau"];
    $tenso = $_REQUEST["tenso"];
    $ngaygui = $_REQUEST["ngaygui"];
    $thoigianbatdau = $_REQUEST["thoigianbatdau"];
    $kyhan = $_REQUEST["kyhan"];
    $laisuat = $_REQUEST["laisuat"];
    $laisuatkhongkyhan = $_REQUEST["laisuatkhongkyhan"];
    $tralai = $_REQUEST["tralai"];
    $tienduocgui = $_REQUEST["tienduocgui"];
    $trangthai = $_REQUEST["trangthai"];

    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);
    if ($user) {
        $data = [
            "id_user" => $user["uid"],
            "tentaikhoan" => $tentaikhoan,
            "loaitaikhoan" => $loaitaikhoan,
            "sotien" => $sotien,
            "diengiai" => $diengiai
        ];
        $account = Account::create($data);

        if ($account) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful"
            ]);
        } else {
            http_response_code(400);
            echo json_encode(
                ["status" => "error", "message" => "Registration failed"]
            );
        }
    } else {
        http_response_code(401);
        echo json_encode(
            ["status" => "error", "message" => "JWT token not valid"]
        );
    }
}
