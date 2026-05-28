<?php

error_reporting(0);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if ($conn->connect_error) {

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);

    exit;
}

$conn->set_charset("utf8");

$sql = "
SELECT
    user_id,
    full_name
FROM users
ORDER BY full_name ASC
";

$result = $conn->query($sql);

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);

    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = $row;

}

echo json_encode([
    "success" => true,
    "data" => $data
]);

exit;