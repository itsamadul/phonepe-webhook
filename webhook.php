<?php
// Enable error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read POST data
$merchantId = $_POST['merchantId'] ?? '';
$amount = $_POST['amount'] ?? '';
$transactionId = $_POST['transactionId'] ?? 'TXN_' . uniqid();

// Create payload for PhonePe
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

// PhonePe API keys (Test or Production)
$keyIndex = 1; // Provided by PhonePe
$secretKey = 'your_production_or_test_secret_key'; // Replace with actual key

// X-VERIFY header generation
$base64Body = base64_encode($jsonPayload);
$string = "/pg/v1/pay" . $base64Body . $secretKey;
$xVerify = hash('sha256', $string) . "###" . $keyIndex;

// Make cURL request to PhonePe PG
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.phonepe.com/apis/hermes/pg/v1/pay", // For PROD
  // CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay", // For TEST
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

// Parse response and redirect
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
