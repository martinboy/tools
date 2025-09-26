<?php
// Configuration: Set your test recipient email here
$to = 'inbox@example.com';  // Replace with your actual email
$from = 'out@example.com';       // Optional: Sender email (helps with deliverability)
$subject = 'PHP Mail Test - ' . date('Y-m-d H:i:s');  // Dynamic subject for easy identification

// Helper function to send email and report success/failure
function sendTestEmail($to, $subject, $message, $headers, $testName) {
	global $from;
    $result = mail($to, $subject, $message, $headers, '-f '.$from);
    if ($result) {
        echo "✓ $testName: Email sent successfully!\n";
    } else {
        echo "✗ $testName: Failed to send email. Check server logs or PHP error reporting.\n";
    }
    return $result;
}

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "==========================================\n";
echo "PHP Mail Function Testing Script\n";
echo "==========================================\n\n";

// Test Case 1: Plain Text Only
echo "Test Case 1: Plain Text Only Email\n";
echo "-----------------------------------\n";
echo "This sends a simple plain text message. No special headers needed.\n\n";

$textMessage = "Hello from PHP!\n\nThis is a plain text only email test.\n\nSent on: " . date('Y-m-d H:i:s') . "\nBest regards,\nYour Server";

$headersText = "From: $from\r\n";  // Basic from header
sendTestEmail($to, $subject . ' (Text Only)', $textMessage, $headersText, 'Text Only');

echo "\n";

// Test Case 2: HTML Only
echo "Test Case 2: HTML Only Email\n";
echo "-----------------------------\n";
echo "This sends an HTML-formatted message. We'll set the Content-Type header to text/html.\n\n";

$htmlMessage = "
<html>
<head>
    <title>PHP Mail Test</title>
</head>
<body>
    <h1>Hello from PHP!</h1>
    <p>This is an <strong>HTML only</strong> email test.</p>
    <p style='color: blue; font-size: 14px;'>It includes formatting like <em>italics</em> and <a href='https://example.com'>links</a>.</p>
    <p>Sent on: " . date('Y-m-d H:i:s') . "</p>
    <p>Best regards,<br>Your Server</p>
</body>
</html>
";

$headersHtml = "From: $from\r\n";
$headersHtml .= "Content-Type: text/html; charset=UTF-8\r\n";  // Key header for HTML
sendTestEmail($to, $subject . ' (HTML Only)', $htmlMessage, $headersHtml, 'HTML Only');

echo "\n";

// Test Case 3: Multipart (Text + HTML Alternative)
echo "Test Case 3: Multipart Email (Text + HTML)\n";
echo "-------------------------------------------\n";
echo "This sends a MIME multipart email with both plain text and HTML versions.\n";
echo "Email clients will prefer HTML if supported, falling back to text.\n\n";

// Plain text part (same as Test Case 1)
$textPart = "Hello from PHP!\n\nThis is the plain text fallback for the multipart email test.\n\nSent on: " . date('Y-m-d H:i:s') . "\nBest regards,\nYour Server";

// HTML part (same as Test Case 2)
$htmlPart = "
<html>
<head>
    <title>PHP Mail Test</title>
</head>
<body>
    <h1>Hello from PHP!</h1>
    <p>This is the <strong>HTML version</strong> in a multipart email test.</p>
    <p style='color: blue; font-size: 14px;'>It includes formatting like <em>italics</em> and <a href='https://example.com'>links</a>.</p>
    <p>Sent on: " . date('Y-m-d H:i:s') . "</p>
    <p>Best regards,<br>Your Server</p>
</body>
</html>
";

// Boundary for separating parts (unique string)
$boundary = md5(time());  // Simple unique boundary

// Construct the full multipart message body
$multipartMessage = "--$boundary\r\n";
$multipartMessage .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$multipartMessage .= $textPart . "\r\n";
$multipartMessage .= "--$boundary\r\n";
$multipartMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$multipartMessage .= $htmlPart . "\r\n";
$multipartMessage .= "--$boundary--\r\n";  // End boundary

// Headers for multipart
$headersMultipart = "From: $from\r\n";
$headersMultipart .= "MIME-Version: 1.0\r\n";
$headersMultipart .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

sendTestEmail($to, $subject . ' (Multipart)', $multipartMessage, $headersMultipart, 'Multipart (Text + HTML)');

echo "\n==========================================\n";
echo "All Tests Complete!\n";
echo "==========================================\n\n";
echo "Check your inbox (and spam folder) at $to for the emails.\n";
echo "If any fail, verify your server's mail setup (e.g., php.ini's sendmail_path)\n";
echo "or use error_get_last() for more details.\n\n";
echo "For advanced testing, consider libraries like PHPMailer, which handle\n";
echo "attachments, authentication, and more reliably.\n\n";

// Optional: Enhanced version with additional headers
echo "Enhanced Version with Additional Headers (Optional)\n";
echo "==================================================\n\n";

// Test Case 4: Enhanced Multipart with quoted-printable and inline disposition
echo "Test Case 4: Enhanced Multipart with Additional Headers\n";
echo "-------------------------------------------------------\n";
echo "This includes Content-Transfer-Encoding: quoted-printable and Content-Disposition: inline\n\n";

// Enhanced multipart message with additional headers
$enhancedMultipartMessage = "--$boundary\r\n";
$enhancedMultipartMessage .= "Content-Type: text/plain; charset=UTF-8\r\n";
$enhancedMultipartMessage .= "Content-Disposition: inline\r\n\r\n";
$enhancedMultipartMessage .= $textPart . "\r\n";
$enhancedMultipartMessage .= "--$boundary\r\n";
$enhancedMultipartMessage .= "Content-Type: text/html; charset=UTF-8\r\n";
$enhancedMultipartMessage .= "Content-Transfer-Encoding: quoted-printable\r\n";
$enhancedMultipartMessage .= "Content-Disposition: inline\r\n\r\n";
$enhancedMultipartMessage .= quoted_printable_encode($htmlPart) . "\r\n";
$enhancedMultipartMessage .= "--$boundary--\r\n";

// Use same headers as regular multipart
sendTestEmail($to, $subject . ' (Enhanced)', $enhancedMultipartMessage, $headersMultipart, 'Enhanced Multipart');

echo "\nScript execution completed. Run with: php " . basename(__FILE__) . "\n";
?>
