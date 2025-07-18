<?php
// Log webhook response to file (log.txt)
$data = file_get_contents("php://input");
file_put_contents("log.txt", $data . PHP_EOL, FILE_APPEND);

// Optional: Respond to PhonePe
echo json_encode(["success" => true]);
?>
