<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data['name'];
$phone   = $data['phone'];
$address = $data['address'];
$total   = $data['total'];
$items   = $data['items'];

/* 1️⃣ Save order */
$stmt = $conn->prepare(
    "INSERT INTO orders (customer_name, phone, address, total_amount)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $name, $phone, $address, $total);
$stmt->execute();

$order_id = $stmt->insert_id;

/* 2️⃣ Save items */
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

/* 3️⃣ Create WhatsApp message */
$orderText = "";
foreach ($items as $i) {
    $orderText .= "• {$i['name']} ({$i['qty']} x ₹{$i['price']})\n";
}

$message =
"*NEW ORDER*\n\n".
"*Name:* $name\n".
"*Phone:* $phone\n".
"*Address:* $address\n\n".
"*Items:*\n$orderText\n".
"*Total:* ₹$total";

echo json_encode([
    "status" => "success",
    "message" => urlencode($message)
]);
