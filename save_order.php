<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data['name'];
$phone   = $data['phone'];
$address = $data['address'];
$total   = $data['total'];
$items   = $data['items'];

$stmt = $conn->prepare(
    "INSERT INTO orders (customer_name, phone, address, total_amount)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $name, $phone, $address, $total);
$stmt->execute();

$order_id = $stmt->insert_id;

$itemStmt = $conn->prepare(
    "INSERT INTO order_items (order_id, item_name, price, quantity)
     VALUES (?, ?, ?, ?)"
);

foreach ($items as $item) {
    $itemStmt->bind_param(
        "isii",
        $order_id,
        $item['name'],
        $item['price'],
        $item['qty']
    );
    $itemStmt->execute();
}

echo json_encode(["status" => "success"]);
?>
