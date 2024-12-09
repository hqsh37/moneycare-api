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

if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "register") {
    // Sanitize and validate inputs
    $firstname = trim($_REQUEST["firstname"] ?? '');
    $lastname = trim($_REQUEST["lastname"] ?? '');
    $email = trim($_REQUEST["email"] ?? '');
    $password = $_REQUEST["password"] ?? '';

    // Basic input validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required."
        ]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email format."
        ]);
        exit;
    }

    // Validate password complexity (minimum 8 characters, for example)
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 6 characters long."
        ]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmail = Auth::find([
        "email" => $email,
    ]);

    if ($checkEmail) {
        http_response_code(409);  // Conflict
        echo json_encode([
            "status" => "error",
            "message" => "Email already exists."
        ]);
    } else {
        // Register new user
        $user = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $hashedPassword
        ];

        $result = Auth::create($user);

        if ($result) {
            http_response_code(201);  // Created
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful"
            ]);
        } else {
            http_response_code(500);  // Internal Server Error
            echo json_encode([
                "status" => "error",
                "message" => "Registration failed. Please try again later."
            ]);
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

// [POST]auth/change-password
if ($this->getMethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "change-password") {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $password = trim($_REQUEST["password"] ?? '');
    $passwordNew = trim($_REQUEST["passwordNew"] ?? '');

    // Validate input
    if (empty($password) || empty($passwordNew)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Both current and new passwords are required.",
        ]);
        exit;
    }

    // Find the user
    $userParams = [
        'email' => $user['email'],
        'id' => $user["uid"],
    ];
    $result = Auth::find($userParams);

    if ($result) {
        if (password_verify($password . "", $result->password)) {
            $data = [
                'id' => $result->id,
                'email' => $result->email,
            ];

            $jwt = $this->createToken($data);

            // Update the password
            Auth::update(
                ["id" => $result->id],
                ['password' => password_hash($passwordNew, PASSWORD_DEFAULT)]
            );

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Password changed successfully.",
                'data' => [
                    'token' => $jwt
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid current password.",
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "User not found.",
        ]);
    }
}

// [POST]auth/logout
if ($this->getmethod() === "POST" && count($this->urls) && $this->urls[1] === "logout") {

    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $result = Auth::find($user);

    if ($result) {
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
    $email = $_REQUEST["email"] ?? null;
    $user = [
        'email' => $email,
    ];

    $result = Auth::find($user);


    if ($result) {
        $randomNumbers = $this->generateRandomNumbers(6);

        $content_mail = '<!DOCTYPE html>
        <html>
        <head>
            <title>Yêu Cầu Đặt Lại Mật Khẩu</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #007bff;
                    color: #ffffff;
                    padding: 10px;
                    text-align: center;
                }
                .content {
                    padding: 20px;
                    text-align: center;
                }
                .content h1 {
                    font-size: 24px;
                }
                .content h4 {
                    font-size: 25px;
                }
                .content p {
                    font-size: 16px;
                    color: #333333;
                }
                .content button {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #ffffff;
                    text-decoration: none;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 14px;
                    color: #999999;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Đặt Lại Mật Khẩu</h2>
                </div>
                <div class="content">
                    <h1>Xin chào, ' . $result->lastname . '</h1>
                    <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                    <p>Mã đặt lại mật khẩu là:</p>
                    <h4>' . $randomNumbers . '</h4>
                    <p>Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ đội ngũ hỗ trợ của chúng tôi.</p>
                </div>
                <div class="footer">
                    <p>&copy; 2024 Money Care.</p>
                </div>
            </div>
        </body>
        ';

        $otp = Auth::update(["id" => $result->id], ["otp" => $randomNumbers]);
        if ($otp) {
            $this->sendmail($result->email, $result->lastname, 'Đặt lại mật khẩu', $content_mail);
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful",
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Registration failed."
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed {$result}"]
        );
    }
}

// [POST]auth/otp
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "otp") {
    $email = $_REQUEST["email"] ?? "";
    $otp = $_REQUEST["otp"] ?? "";

    $user = [
        'email' => $email,
        'otp' => $otp
    ];

    $result = Auth::find($user);

    if ($result && $otp) {
        $otpRemove = Auth::update(["id" => $result->id], ["otp" => ""]);

        if ($otpRemove) {
            $data = [
                'id' => $result->id,
                'email' => $result->email,
            ];
            $jwt = $this->createToken($data);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "otp confirmed successfully",
                'data' => [
                    'token' => $jwt
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "otp not confirmed"
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed {$result}"]
        );
    }
}


// [POST]auth/reset-password
if ($this->getMethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "reset-password") {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $passwordNew = trim($_REQUEST["password"] ?? '');

    // Validate input
    if (empty($passwordNew)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Both current and new passwords are required.",
        ]);
        exit;
    }

    // Find the user
    $userParams = [
        'email' => $user['email'],
        'id' => $user["uid"],
    ];
    $result = Auth::find($userParams);

    if ($result) {
        // Update the password
        Auth::update(
            ["id" => $result->id],
            ['password' => password_hash($passwordNew, PASSWORD_DEFAULT)]
        );

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Password changed successfully.",
            'data' => [
                'token' => $jwt
            ]
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "User not found.",
        ]);
    }
}
