<!-- comments.php -->
<?php
// Check if the form is submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and trim input data from the form to prevent XSS and unwanted spaces
    $name = htmlspecialchars(trim($_POST['name'])); // Sanitize 'name' input
    $email = htmlspecialchars(trim($_POST['email'])); // Sanitize 'email' input
    $message = htmlspecialchars(trim($_POST['message'])); // Sanitize 'message' input

    // Basic validation: Check if all fields are filled out
    if (!empty($name) && !empty($email) && !empty($message)) {
        
        // Set up email settings
        $to = "info@gitc.com"; // The recipient email address (replace with your own)
        $subject = "New Contact Form Submission from $name"; // Subject of the email
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message"; // Body content of the email
        $headers = "From: $email"; // Set the 'From' header to the user's email

        // Attempt to send the email
        if (mail($to, $subject, $body, $headers)) {
            // If the email was sent successfully, redirect with a success flag
            header("Location: contact.php?success=1");
            exit(); // Ensure no further code is executed after redirect
        } else {
            // If the email failed to send, redirect with an error flag
            header("Location: contact.php?error=1");
            exit(); // Ensure no further code is executed after redirect
        }
    } else {
        // If any required field is empty, redirect with a different error flag
        header("Location: contact.php?error=2");
        exit(); // Ensure no further code is executed after redirect
    }
} else {
    // If the form was not submitted via POST (e.g., direct access to this script), redirect to the contact page
    header("Location: contact.php");
    exit(); // Ensure no further code is executed after redirect
}
?>

