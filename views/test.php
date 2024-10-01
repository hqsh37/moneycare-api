<?php
echo $this->createToken(["id" => 123, "email" => "hqsh37@gmail.com"]);

$user = $this->validateToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI0ZjFnMjNhMTJhYSIsInVpZCI6MTIzLCJlbWFpbCI6Imhxc2gzN0BnbWFpbC5jb20ifQ.xG3lDIm7iF2t3egwNQhMta4PkbeH1FPgQLFvE4Jt4A4");
echo "<br />";
echo json_encode($user);