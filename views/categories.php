<?php

// [GET] accounts/
if ($this->getmethod() === "GET" && count($this->urls) == 2) {
    if ($this->urls[1] === "default") {
        $categorys = Categories::finds(["id_user" => 0]);

        if ($categorys) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful",
                "data" => $categorys
            ]);
        } else {
            http_response_code(204);
            echo json_encode(
                ["status" => "success", "message" => "No data available"]
            );
        }
    } else {
        http_response_code(404);
        echo json_encode(
            ["status" => "error", "message" => "Not found"]
        );
    }
} else if ($this->getmethod() === "GET" && count($this->urls) == 1) {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);
    if ($user) {

        $categorys = Categories::finds(["id_user" => $user["uid"]]);

        if ($categorys) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful",
                "data" => $categorys
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

// [POST] accounts/
if ($this->getmethod() === "POST") {
    $tentaikhoan = $_REQUEST["tentaikhoan"];
    $loaitaikhoan = $_REQUEST["loaitaikhoan"];
    $sotien = $_REQUEST["sotien"];
    $diengiai = $_REQUEST["diengiai"];

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

// [PUT] /accounts/{id}
if ($this->getmethod() === "PUT" && count($this->urls) == 2) {
    $idAccount = $this->urls[1];

    $jsonBody = file_get_contents('php://input');
    $data = json_decode($jsonBody, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON format']);
        exit();
    }

    $tentaikhoan = $data['tentaikhoan'] ?? null;
    $loaitaikhoan = $data['loaitaikhoan'] ?? null;
    $sotien = $data['sotien'] ?? null;
    $diengiai = $data['diengiai'] ?? null;

    if (!$tentaikhoan || !$loaitaikhoan || !$sotien) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $dataUpdate = [
        "tentaikhoan" => $tentaikhoan,
        "loaitaikhoan" => $loaitaikhoan,
        "sotien" => $sotien,
        "diengiai" => $diengiai
    ];

    $account = Account::update(["id" => $idAccount], $dataUpdate);

    if ($account) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Update successful"
        ]);
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Update failed"]
        );
    }
}

// [DELETE] /accounts/{id}
if ($this->getmethod() === "DELETE" && count($this->urls) == 2) {
    $idAccount = $this->urls[1];
    $account = Account::delete(["id" => $idAccount]);

    if ($account) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Delete successful"
        ]);
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Delete failed"]
        );
    }
}
