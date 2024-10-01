<?php
include "../config.php";
$result = Province::select();

$resultHandle[0] = [
    'id' => null,
    'name' => 'Chọn một Tỉnh/thành phố'
];
foreach($result as $item) {
    $resultHandle[] = [
        'id' => $item->province_id,
        'name' => $item->name,
    ];
}

header('Content-Type: application/json');
echo json_encode($resultHandle);
?>