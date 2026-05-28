<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
/* =========================
   CONNECT DATABASE
========================= */

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if ($conn->connect_error) {

    echo json_encode([
        "success" => false,
        "message" => "Connect Failed : " . $conn->connect_error
    ]);

    exit();
}

$conn->set_charset("utf8");

/* =========================
   GET ALL TRANSACTIONS
========================= */

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    try {

        $sql = "
            SELECT
                t.log_id,
                t.amount,
                t.transfer_type,
                t.transfer_status,
                t.note,
                t.created_at,

                w.website_name,

                fb.bank_name AS from_bank_name,
                fb.bank_number AS from_bank_number,

                tb.bank_name AS to_bank_name,
                tb.bank_number AS to_bank_number,

                u.full_name

            FROM transactions t

            LEFT JOIN websites w
            ON t.website_id = w.website_id

            LEFT JOIN bank_accounts fb
            ON t.from_bank_id = fb.bank_id

            LEFT JOIN bank_accounts tb
            ON t.to_bank_id = tb.bank_id

            LEFT JOIN users u
            ON t.user_id = u.user_id

            ORDER BY t.log_id DESC
        ";

        $query = $conn->query($sql);

        if (!$query) {

            throw new Exception($conn->error);
        }

        $data = [];

        while ($row = $query->fetch_assoc()) {

            $data[] = [
                "log_id"      => $row["log_id"],
                "website_name"     => $row["website_name"],
                "from_bank"        => $row["from_bank_name"] . " (" . $row["from_bank_number"] . ")",
                "to_bank"          => $row["to_bank_name"] . " (" . $row["to_bank_number"] . ")",
                "transfer_type"    => $row["transfer_type"],
                "amount"           => $row["amount"],
                "transfer_status"  => $row["transfer_status"],
                "note"             => $row["note"],
                "full_name"        => $row["full_name"],
                "created_at"       => $row["created_at"]
            ];
        }

        echo json_encode([
            "success" => true,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    exit();
}

/* =========================
   INSERT TRANSACTION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $website_id = intval($input["website_id"]);
    $from_bank_id = intval($input["from_bank_id"]);
    $to_bank_id = intval($input["to_bank_id"]);
    $user_id = intval($input["user_id"]);

    $amount = floatval($input["amount"]);

    $transfer_type =
    $conn->real_escape_string(
        $input["transfer_type"]
    );

    $transfer_status =
    $conn->real_escape_string(
        $input["transfer_status"] ?? "สำเร็จ"
    );

    $note =
    $conn->real_escape_string(
        $input["note"] ?? ""
    );

    $sql = "
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
        (
            '$website_id',
            '$from_bank_id',
            '$to_bank_id',
            '$transfer_type',
            '$amount',
            '$note',
            '$transfer_status',
            '$user_id'
        )
    ";

    if ($conn->query($sql)) {

        echo json_encode([
            "success" => true,
            "message" => "บันทึกข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);

    } else {

        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ], JSON_UNESCAPED_UNICODE);
    }

    exit();
}

/* =========================
   UPDATE TRANSACTION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $log_id =
    intval($input["log_id"]);

    $note =
    $conn->real_escape_string(
        $input["note"]
    );

    $transfer_status =
    $conn->real_escape_string(
        $input["transfer_status"]
    );

    $sql = "
        UPDATE transactions
        SET
            note = '$note',
            transfer_status = '$transfer_status'
        WHERE log_id = '$log_id'
    ";

    if ($conn->query($sql)) {

        echo json_encode([
            "success" => true,
            "message" => "อัปเดตข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);

    } else {

        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ], JSON_UNESCAPED_UNICODE);
    }

    exit();
}

/* =========================
   DELETE TRANSACTION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $log_id =
    intval($input["log_id"]);

    $sql = "
        DELETE FROM transactions
        WHERE log_id = '$log_id'
    ";

    if ($conn->query($sql)) {

        echo json_encode([
            "success" => true,
            "message" => "ลบข้อมูลสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);

    } else {

        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ], JSON_UNESCAPED_UNICODE);
    }

    exit();
}

?>