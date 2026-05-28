<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if($conn->connect_error){
    die("Connection failed");
}

$method = $_SERVER['REQUEST_METHOD'];

if($method == "GET"){

    $sql =
    "SELECT * FROM users ORDER BY user_id DESC";

    $result =
    $conn->query($sql);

    $data = [];

    while($row = $result->fetch_assoc()){

        $data[] = $row;

    }

    echo json_encode($data);

}

/* SAVE */

if($method == "POST"){

    $input =
    json_decode(file_get_contents("php://input"),true);

    $user_id =
    $input["user_id"];

    $username =
    $input["username"];

    $password =
    $input["password"];

    $full_name =
    $input["full_name"];

    $role =
    $input["role"];

    $status =
    $input["status"];

    if($user_id == ""){

        $sql =
        "INSERT INTO users
        (
          username,
          password,
          full_name,
          role,
          status
        )
        VALUES
        (
          '$username',
          '$password',
          '$full_name',
          '$role',
          '$status'
        )";

    }else{

        $sql =
        "UPDATE users SET

        username='$username',
        password='$password',
        full_name='$full_name',
        role='$role',
        status='$status'

        WHERE user_id='$user_id'";

    }

    $conn->query($sql);

    echo json_encode([
      "status"=>"success"
    ]);

}

/* DELETE */

if($method == "DELETE"){

    $input =
    json_decode(file_get_contents("php://input"),true);

    $user_id =
    $input["user_id"];

    $sql =
    "DELETE FROM users
    WHERE user_id='$user_id'";

    $conn->query($sql);

    echo json_encode([
      "status"=>"deleted"
    ]);

}

?>