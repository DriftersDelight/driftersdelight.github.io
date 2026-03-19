<?php
// process-order.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submissions
    $email_to = 'rudkocassidy@gmail.com';
    $subject = 'New Order Submission';

    // Collect order details
    $order_details = '';
    foreach ($_POST as $key => $value) {
        $order_details .= "$key: $value\n";
    }

    // Handle image uploads
    $attachments = array();
    foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
        $file_path = 'uploads/' . basename($_FILES['images']['name'][$index]);
        if (move_uploaded_file($tmp_name, $file_path)) {
            $attachments[] = $file_path;
        }
    }

    // Prepare email
    $headers = "From: noreply@driftersdelight.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";

    $email_body = "--boundary\r\n";
    $email_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $email_body .= $order_details . "\r\n";

    // Attach images
    foreach ($attachments as $file) {
        $email_body .= "--boundary\r\n";
        $email_body .= "Content-Type: image/jpeg; name=\"" . basename($file) . "\"\r\n";
        $email_body .= "Content-Disposition: attachment; filename=\"" . basename($file) . "\"\r\n";
        $email_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $email_body .= chunk_split(base64_encode(file_get_contents($file))) . "\r\n";
    }
    $email_body .= "--boundary--";

    // Send email
    if (mail($email_to, $subject, $email_body, $headers)) {
        echo 'Order submitted successfully!';
    } else {
        echo 'Failed to send order details.';
    }
}
?>