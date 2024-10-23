<?php
// auth/me
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "me") {
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(401);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed"]
        );
    }
}

// auth/register
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "register") {
    $firstname = $_REQUEST["firstname"];
    $lastname = $_REQUEST["lastname"];
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $user = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => $password
    ];

    $result = Auth::create($user);

    if($result) {
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

// auth/login
if ($this->getmethod() === "POST" && count($this->urls) == 2 && $this->urls[1] === "login") {
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $user = [
        'email' => $email,
        'password' => $password
    ];

    $result = Auth::find($user);

    if($result) {
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
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed {$result}"]
        );
    }

}

// auth/logout
if ($this->getmethod() === "POST" && count($this->urls) && $this->urls[1] === "logout") {
    
    $jwt = $this->getHeaderAuthorization();
    $user = $this->validateToken($jwt);

    $result = Auth::find($user);

    if($result) {
        $data = [
            'id' => $result->id,
            'email' => $result->email,
        ];
        $jwt = $this->createToken($data);
        Auth::delete(["id" => $result->id], ["session" => $jwt]);
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful"]);
    } else {
        http_response_code(400);
        echo json_encode(
            ["status" => "error", "message" => "Registration failed"]
        );
    }

}