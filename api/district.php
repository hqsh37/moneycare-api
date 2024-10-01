<?php
include "../config.php";
if(isset($_GET['provinceId'])) {
    $provinceId = $_GET['provinceId'];
    
    $result = District::finds([
        'province_id' => $provinceId,
    ]);
    $resultHandle[0] = [
        'id' => null,
        'name' => 'Chọn một Quận/huyện'
    ];
    foreach($result as $item) {
        $resultHandle[] = [
            'id' => $item->district_id,
            'name' => $item->name,
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($resultHandle);
    return;
} else {
    echo json_encode(false);
    return;
}
?>