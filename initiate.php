<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$merchantId = $_POST['merchantId'] ?? '';
$amount = $_POST['amount'] ?? '';
$transactionId = $_POST['transactionId'] ?? 'TXN_' . uniqid();

$payload = [
  "merchantId" => $merchantId,
  "transactionId" => $transactionId,
  "amount" => (int)$amount,
  "merchantUserId" => "EMADUL_USER_001",
  "redirectUrl" => "https://www.emadulai.in/p/thank-you.html",
  "redirectMode" => "POST",
  "paymentInstrument" => [
    "type" => "PAY_PAGE"
  ]
];

$jsonPayload = json_encode($payload);

$keyIndex = 1;
$secretKey = 'NTMwMzNmOTYtYjkxNC00OTkzLWE5MjUtYzhlNjNjMzA0NmVj'; // Test Secret Key

$base64Body = base64_encode($jsonPayload);
$string = "/pg/v1/pay" . $base64Body . $secretKey;
$xVerify = hash('sha256', $string) . "###" . $keyIndex;

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode(["request" => $base64Body]),
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "X-VERIFY: $xVerify",
    "X-MERCHANT-ID: $merchantId"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error: $err";
} else {
  $data = json_decode($response, true);
  if (isset($data['data']['instrumentResponse']['redirectInfo']['url'])) {
    $redirectUrl = $data['data']['instrumentResponse']['redirectInfo']['url'];
    header("Location: $redirectUrl");
    exit;
  } else {
    echo "<h3>Error Initiating Payment</h3><pre>" . print_r($data, true) . "</pre>";
  }
}
?>
