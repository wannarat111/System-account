<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if($conn->connect_error){

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);

    exit;

}

$sql = "

SELECT
    website_id,
    website_name,
    website_code,
    website_status,
    created_at

FROM websites

WHERE website_status = 'ACTIVE'

ORDER BY website_name ASC

";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;

}

echo json_encode([
    "success" => true,
    "data" => $data
]);

?>