<?php

echo $this->createToken(["id" => 1, "email" => "hqsh37@gmail.com"]);



$user = $this->validateToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI0ZjFnMjNhMTJhYSIsInVpZCI6IjQiLCJlbWFpbCI6InNhbmcxMjNAZ21haWwuY29tIn0.ZkzcG7JY57BexZ5yxBWD4S_U5DI2asdvNXwr_ka8hV4");

echo "<br />";

echo json_encode($user);