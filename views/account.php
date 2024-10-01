<?php
if($this->getmethod() === "GET") {
    $account = Account::select();
    echo json_encode($account);
}