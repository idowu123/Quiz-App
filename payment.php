<?php
// pay.php - Payment page for Cordova app

// === CONFIGURATION ===
$secretKey = "sk_live_8ac9f626996aaed046aba2b1184677efa1109bc4"; // Use test key first
$amount = 26000 * 100; // Amount in kobo (Paystack uses kobo)
$callback_url = "https://yourdomain.com/verify.php"; // Paystack will redirect here after payment

// Collect user info from a form or query parameter
$email = isset($_POST['email']) ? $_POST['email'] : "customer@email.com";

// Generate unique reference
$reference = "SUB_" . time();

// Prepare data to send to Paystack
$data = [
    "email" => $email,
    "amount" => $amount,
    "reference" => $reference,
    "callback_url" => $callback_url
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secretKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

if ($response === false) {
    die("Curl error: " . curl_error($ch));
}

curl_close($ch);

$result = json_decode($response, true);

if (!$result) {
    die("Invalid response from Paystack: " . htmlspecialchars($response));
}

// Check if initialization was successful
if (isset($result['status']) && $result['status'] === true && isset($result['data']['authorization_url'])) {
    // Redirect user to Paystack payment page
    header("Location: " . $result['data']['authorization_url']);
    exit();
} else {
    echo "<h2>Payment initialization failed!</h2>";
    echo "<pre>";
    print_r($result); // Show API response for debugging
    echo "</pre>";
}
?>
