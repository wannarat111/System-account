<?php

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: POST");

header("Access-Control-Allow-Headers: Content-Type");

// =========================
// DATABASE
// =========================

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if($conn->connect_error){

    die(json_encode([
        "success" => false,
        "message" => "Database Error"
    ]));

}

$conn->set_charset("utf8");

// =========================
// GET JSON
// =========================

$json = json_decode(
    file_get_contents("php://input"),
    true
);

$username =
$json['username'] ?? "";

$password =
$json['password'] ?? "";

// =========================
// CHECK USER
// =========================

$sql = "

SELECT
    user_id,
    username,
    password,
    full_name,
    role,
    status

FROM users

WHERE username = ?
AND password = ?

LIMIT 1

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $username,
    $password
);

$stmt->execute();

$result =
$stmt->get_result();

if($result->num_rows > 0){

    $user =
    $result->fetch_assoc();

    // CHECK STATUS

    if($user['status'] != 'ACTIVE'){

        echo json_encode([

            "success" => false,

            "message" =>
            "บัญชีถูกปิดใช้งาน"

        ]);

        exit();

    }

    echo json_encode([

        "success" => true,

        "message" =>
        "Login Success",

        "user" => [

            "user_id" =>
            $user['user_id'],

            "username" =>
            $user['username'],

            "full_name" =>
            $user['full_name'],

            "role" =>
            $user['role']

        ]

    ]);

}else{

    echo json_encode([

        "success" => false,

        "message" =>
        "Username หรือ Password ไม่ถูกต้อง"

    ]);

}

$conn->close();

?>