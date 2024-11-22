<?php
// [POST]async-cloud/
if ($this->getmethod() === "POST") {
  $jwt = $this->getHeaderAuthorization();
  $user = $this->validateToken($jwt);

  $jsonString = file_get_contents('php://input');

  if (DataProcessor::processData($jsonString, $user["uid"])) {
    $user = Auth::find(["id" => 2], $select = "`categoryAt`, `accountAt`, `transactionAt`, `savingsAt`");
    http_response_code(200);
    echo json_encode([
      "status" => "success",
      "message" => "Registration successful",
      "data" => $user,
    ]);
  } else {
    http_response_code(400);
    echo json_encode(
      ["status" => "error", "message" => "Registration failed"]
    );
  }
}
