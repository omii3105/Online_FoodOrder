<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare(
    "INSERT INTO reviews (customer_name, rating, review)
     VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "sis",
    $data['name'],
    $data['rating'],
    $data['review']
);

$stmt->execute();

echo json_encode(["status" => "success"]);
?>
