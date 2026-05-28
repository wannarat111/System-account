<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// ========================= 
// DATABASE CONNECTION
// ========================= 

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "database1"
);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "message" => "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

// ========================= 
// GET - FETCH ALL BANK ACCOUNTS
// ========================= 

if ($method == "GET") {
    
    try {
        
        $sql = "SELECT 
                    bank_id,
                    bank_name,
                    bank_number,
                    account_name,
                    bank_status,
                    created_at
                FROM bank_accounts 
                ORDER BY bank_id DESC";

        $query = $conn->query($sql);

        if (!$query) {
            throw new Exception("Query error: " . $conn->error);
        }

        $data = [];

        while ($row = $query->fetch_assoc()) {
            $data[] = $row;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "data" => $data,
            "count" => count($data)
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit();
}

// ========================= 
// POST - CREATE BANK ACCOUNT
// ========================= 

if ($method == "POST") {
    
    try {
        
        $input = json_decode(file_get_contents("php://input"), true);

        // Validate input
        if (!isset($input["bank_name"]) || empty($input["bank_name"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ชื่อธนาคารไม่สามารถว่างได้"
            ]);
            exit();
        }

        if (!isset($input["bank_number"]) || empty($input["bank_number"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "เลขบัญชีไม่สามารถว่างได้"
            ]);
            exit();
        }

        if (!isset($input["account_name"]) || empty($input["account_name"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ชื่อบัญชีไม่สามารถว่างได้"
            ]);
            exit();
        }

        $bank_id = isset($input["bank_id"]) ? $input["bank_id"] : "";
        $bank_name = $conn->real_escape_string($input["bank_name"]);
        $bank_number = $conn->real_escape_string($input["bank_number"]);
        $account_name = $conn->real_escape_string($input["account_name"]);
        $bank_status = isset($input["bank_status"]) ? $conn->real_escape_string($input["bank_status"]) : "ACTIVE";

        // INSERT (new record)
        if ($bank_id == "" || $bank_id == null) {
            
            // Check if bank_number already exists
            $check_sql = "SELECT bank_id FROM bank_accounts WHERE bank_number = '$bank_number'";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows > 0) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "เลขบัญชีนี้มีอยู่แล้ว"
                ]);
                exit();
            }

            $sql = "INSERT INTO bank_accounts
                    (bank_name, bank_number, account_name, bank_status, created_at)
                    VALUES
                    ('$bank_name', '$bank_number', '$account_name', '$bank_status', NOW())";

            if ($conn->query($sql)) {
                $new_id = $conn->insert_id;
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "บันทึกบัญชีสำเร็จ",
                    "bank_id" => $new_id
                ]);
            } else {
                throw new Exception("Insert error: " . $conn->error);
            }

        } else {
            // UPDATE (existing record)
            
            $bank_id = intval($bank_id);

            // Verify bank account exists
            $verify_sql = "SELECT bank_id FROM bank_accounts WHERE bank_id = $bank_id";
            $verify_result = $conn->query($verify_sql);
            
            if ($verify_result->num_rows == 0) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "ไม่พบบัญชีที่ต้องการแก้ไข"
                ]);
                exit();
            }

            $sql = "UPDATE bank_accounts SET
                    bank_name = '$bank_name',
                    bank_number = '$bank_number',
                    account_name = '$account_name',
                    bank_status = '$bank_status'
                    WHERE bank_id = $bank_id";

            if ($conn->query($sql)) {
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "อัปเดตบัญชีสำเร็จ",
                    "bank_id" => $bank_id
                ]);
            } else {
                throw new Exception("Update error: " . $conn->error);
            }
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit();
}

// ========================= 
// PUT - UPDATE BANK ACCOUNT
// ========================= 

if ($method == "PUT") {
    
    try {
        
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["bank_id"]) || empty($input["bank_id"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "bank_id ไม่สามารถว่างได้"
            ]);
            exit();
        }

        if (!isset($input["bank_name"]) || empty($input["bank_name"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ชื่อธนาคารไม่สามารถว่างได้"
            ]);
            exit();
        }

        $bank_id = intval($input["bank_id"]);
        $bank_name = $conn->real_escape_string($input["bank_name"]);
        $bank_number = isset($input["bank_number"]) ? $conn->real_escape_string($input["bank_number"]) : "";
        $account_name = isset($input["account_name"]) ? $conn->real_escape_string($input["account_name"]) : "";
        $bank_status = isset($input["bank_status"]) ? $conn->real_escape_string($input["bank_status"]) : "ACTIVE";

        // Verify bank account exists
        $verify_sql = "SELECT bank_id FROM bank_accounts WHERE bank_id = $bank_id";
        $verify_result = $conn->query($verify_sql);
        
        if ($verify_result->num_rows == 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "ไม่พบบัญชีที่ต้องการแก้ไข"
            ]);
            exit();
        }

        // Build update query
        $update_fields = "bank_name = '$bank_name'";
        
        if (!empty($bank_number)) {
            $update_fields .= ", bank_number = '$bank_number'";
        }
        
        if (!empty($account_name)) {
            $update_fields .= ", account_name = '$account_name'";
        }
        
        $update_fields .= ", bank_status = '$bank_status'";

        $sql = "UPDATE bank_accounts SET $update_fields WHERE bank_id = $bank_id";

        if ($conn->query($sql)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "อัปเดตบัญชีสำเร็จ"
            ]);
        } else {
            throw new Exception("Update error: " . $conn->error);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit();
}

// ========================= 
// DELETE - DELETE BANK ACCOUNT
// ========================= 

if ($method == "DELETE") {
    
    try {
        
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["bank_id"]) || empty($input["bank_id"])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "bank_id ไม่สามารถว่างได้"
            ]);
            exit();
        }

        $bank_id = intval($input["bank_id"]);

        // Verify bank account exists
        $verify_sql = "SELECT bank_id FROM bank_accounts WHERE bank_id = $bank_id";
        $verify_result = $conn->query($verify_sql);
        
        if ($verify_result->num_rows == 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "ไม่พบบัญชีที่ต้องการลบ"
            ]);
            exit();
        }

        $sql = "DELETE FROM bank_accounts WHERE bank_id = $bank_id";

        if ($conn->query($sql)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "ลบบัญชีสำเร็จ"
            ]);
        } else {
            throw new Exception("Delete error: " . $conn->error);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }
    
    $conn->close();
    exit();
}

// ========================= 
// INVALID METHOD
// ========================= 

http_response_code(405);
echo json_encode([
    "success" => false,
    "message" => "Method not allowed"
]);

$conn->close();

?>