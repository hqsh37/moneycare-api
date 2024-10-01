<?php
include "../config.php";
if(isset($_GET['districtId'])) {
    $districtId = $_GET['districtId'];
    
    $result = Ward::finds([
        'district_id' => $districtId,
    ]);
    $resultHandle[0] = [
        'id' => null,
        'name' => 'Chọn một xã/phường'
    ];
    foreach($result as $item) {
        $resultHandle[] = [
            'id' => $item->wards_id,
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