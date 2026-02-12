<?php
// verify.php - Paystack payment verification

$secretKey =" sk_live_8ac9f626996aaed046aba2b1184677efa1109bc4 "; // Replace with your Paystack secret key

if (!isset($_GET['reference'])) {
    die("No payment reference supplied.");
}

$reference = $_GET['reference'];

// Initialize cURL to verify payment
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/$reference");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secretKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Check if verification succeeded
if (!$result || !isset($result['status'])) {
    die("Unable to verify payment. Try again.");
}

if ($result['status'] === true && $result['data']['status'] === 'success') {
    $email = $result['data']['customer']['email'];
    $amount = $result['data']['amount'] / 100; // Convert kobo to naira

    // Generate a passkey for the user (example: 6-digit random number)
    $passkey = rand(100000, 999999);

    // Display payment success details
    echo "<h2>Payment Successful ✅</h2>";
    echo "<p>Email: $email</p>";
    echo "<p>Amount Paid: ₦$amount</p>";
    echo "<p>Your Passkey: $passkey</p>";
    echo "<p>Please use this passkey to login in your app.</p>";

    // TODO: Optionally send passkey via email or WhatsApp here
    // TODO: Optionally redirect back to your app
} else {
    echo "<h2>Payment verification failed ❌</h2>";
    echo "<p>Payment status: " . ($result['data']['status'] ?? 'unknown') . "</p>";
}
?>