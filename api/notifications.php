<?php
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['idKH'])) {
        $idKH = $data['idKH'];

        Notification::update([
            "idKH" => $idKH,
            "trangThai" => "not-seen",
        ],[
            "trangThai" => "watched",
        ]);

        echo json_encode([
            'success' => true,
        ]);
    } else {
        echo json_encode([
            'success' => false,
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
    ]);
}
?>