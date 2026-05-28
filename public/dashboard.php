<?php
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "root", "", "database1");

if ($conn->connect_error) {
    echo json_encode(["success"=>false,"message"=>$conn->connect_error]);
    exit();
}

$conn->set_charset("utf8");

$sql = "
SELECT 
    t.log_id,
    t.amount,
    t.transfer_type,
    t.transfer_status,
    t.created_at,

    fb.bank_name AS from_bank_name,
    tb.bank_name AS to_bank_name

FROM transactions t
LEFT JOIN bank_accounts fb ON t.from_bank_id = fb.bank_id
LEFT JOIN bank_accounts tb ON t.to_bank_id = tb.bank_id

ORDER BY t.log_id DESC
LIMIT 100
";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "log_id" => $row["log_id"],
        "from_account" => $row["from_bank_name"],
        "to_account" => $row["to_bank_name"],
        "amount" => $row["amount"],
        "transfer_type" => $row["transfer_type"],
        "transfer_status" => $row["transfer_status"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $data
], JSON_UNESCAPED_UNICODE);