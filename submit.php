<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postmarkApiKey = "9fab92ff-e3cf-466d-b213-5f7baab1a6ce";
    $from = "info@saboura.net";
   $adminEmails = "hajerboukhari2018@gmail.com"; 
   // $adminEmails = "ihebbenmonsef@gmail.com";// Admin email to notify when user completes form

    // Extract data from the POST request
    $data = json_decode(file_get_contents('php://input'), true);
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($data['phone'], FILTER_SANITIZE_STRING); // Récupérer le numéro de téléphone
    $message = filter_var($data['message'], FILTER_SANITIZE_STRING);


    // Prepare the email content
    $subject = "New Message from $name";
    $htmlBody = "<p>You have received a new message from:</p>";
    $htmlBody .= "<ul>";
    $htmlBody .= "<li><strong>Name:</strong> $name</li>";
    $htmlBody .= "<li><strong>Email:</strong> $email</li>";
    $htmlBody .= "<li><strong>Phone:</strong> $phone</li>"; 
    $htmlBody .= "</ul>";
    $htmlBody .= "<p><strong>Message:</strong></p>";
    $htmlBody .= "<p>$message</p>";

    $requestBody = [
        "From" => $from,
        "To" => $adminEmails,
        "Subject" => $subject,
        "HtmlBody" => $htmlBody
    ];

    // Send the email using Postmark API
    $ch = curl_init("https://api.postmarkapp.com/email");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "X-Postmark-Server-Token: $postmarkApiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email. Response: " . $response;
    }
} else {
    echo "Invalid request.";
}
?>