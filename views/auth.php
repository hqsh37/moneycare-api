<?php

// [POST]auth/me
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "me") {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $userPare = [
        'email' => $user['email'],
        'id' => $user["uid"],
    ];

    $result = Auth::find($userPare, '`firstname`, `lastname`, `email`, `categoryAt`, `accountAt`, `transactionAt`, `savingsAt`');


    if ($result) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful",
            "data" => $result
        ]);
    } else {
        http_response_code(401);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed"]
        );
    }
}

// [POST]auth/register
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "register") {
    $firstname = $_REQUEST["firstname"];
    $lastname = $_REQUEST["lastname"];
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $user = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];

    $checkEmail = Auth::find([
        "email" => $email,
    ]);

    if ($checkEmail) {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Email has exited"]
        );
    } else {
        $result = Auth::create($user);

        if ($result) {
            http_response_code(200);
            echo json_encode(
                ["status" => "success", "message" => "Registration successful"]
            );
        } else {
            http_response_code(400);
            echo json_encode(
                ["status" => "error", "message" => "Registration failed"]
            );
        }
    }
}

// [POST]auth/login
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "login") {
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $user = [
        'email' => $email,
    ];

    $result = Auth::find($user);

    if ($result) {
        if (password_verify(trim($password), $result->password)) {
            $data = [
                'id' => $result->id,
                'email' => $result->email,
            ];

            $jwt = $this->createToken($data);
            Auth::update(["id" => $result->id], ["session" => $jwt]);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                'data' => [
                    'token' => $jwt
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid credentials"
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed {$result}"]
        );
    }
}

// [POST]auth/logout
if ($this->getmethod() === "POST" && count($this->urls) && $this->urls[1] === "logout") {

    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $result = Auth::find($user);

    if ($result) {
        $data = [
            'id' => $result->id,
            'email' => $result->email,
        ];
        $jwt = $this->createToken($data);
        Auth::delete(["id" => $result->id], ["session" => $jwt]);
        http_response_code(200);
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
}

// [POST]auth/forgot
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "forgot") {
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $user = [
        'email' => $email,
    ];

    $result = Auth::find($user);

    if ($result) {
        if (!!!password_verify($password, $result->password)) {
            $data = [
                'id' => $result->id,
                'email' => $result->email,
            ];
            $jwt = $this->createToken($data);
            Auth::update(["id" => $result->id], ["session" => $jwt]);
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful",
                'data' => [
                    'token' => $jwt
                ]
            ]);
        } else {
            http_response_code(201);
            echo json_encode(
                ["status" => "error", "message" => "Invalid credentials"]
            );
        }
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed {$result}"]
        );
    }
}
