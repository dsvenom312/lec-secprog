<?php
session_start();
require "../Controller/connection.php";
require "../Controller/session_handler.php";

if ($_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit();
}

$query = "SELECT id, username FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$recipients = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $recipientId = $_POST['recipient'];
    $message = $_POST['message'];

    if (strlen($message) > 200) {
        $_SESSION["error_message"] = "Message should be up to 200 characters.";
    } else {
        $randomNumber = mt_rand(1000, 9999);
        $messageId = 'ms' . $randomNumber;

        // Insert message data into the database
        $insertQuery = "INSERT INTO message (id, sender_id, recipient_id, message) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssss", $messageId, $_SESSION['id'], $recipientId, $message);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {
            $_SESSION["success_message"] = "Message sent successfully.";
        } else {
            $_SESSION["error_message"] = "Failed to send message. Please try again.";
        }
        $insertStmt->close();
    }
    header("Location: message.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
</head>
<body>
    <h1>Send Message</h1>
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['success_message'])) {
        echo '<p style="color: green;">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }
    ?>
    <form action="message.php" method="post">
        <label for="recipient">Choose recipient:</label>
        <select name="recipient" id="recipient" required>
            <?php foreach ($recipients as $recipient) : ?>
                <option value="<?= $recipient['id']; ?>"><?= $recipient['username']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="message">Message (up to 200 characters):</label><br>
        <textarea name="message" id="message" rows="5" maxlength="200" required></textarea><br><br>

        <input type="submit" value="Send Message">
    </form>
</body>
</html>
