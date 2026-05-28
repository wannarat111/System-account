<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if ($conn->connect_error) {

    die(json_encode([
        "success" => false,
        "message" => "เชื่อมต่อฐานข้อมูลไม่สำเร็จ"
    ]));

}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    $data =
    json_decode(file_get_contents("php://input"), true);

    $website_id =
    $data['website_id'] ?? '';

    $from_bank_id =
    $data['from_bank_id'] ?? '';

    $to_bank_id =
    $data['to_bank_id'] ?? '';

    $transfer_type =
    $data['transfer_type'] ?? '';

    $amount =
    $data['amount'] ?? 0;

    $note =
    $data['note'] ?? '';

    $transfer_status =
    $data['transfer_status'] ?? 'สำเร็จ';

    $user_id =
    $data['user_id'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO transactions
        (
            website_id,
            from_bank_id,
            to_bank_id,
            transfer_type,
            amount,
            note,
            transfer_status,
            user_id
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssdsss",
        $website_id,
        $from_bank_id,
        $to_bank_id,
        $transfer_type,
        $amount,
        $note,
        $transfer_status,
        $user_id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "บันทึกรายการสำเร็จ"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => $stmt->error
        ]);

    }

}
?>