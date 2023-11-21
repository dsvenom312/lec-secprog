<?php
// session_start();
include '../Controller/connection.php';

$userID = $_SESSION['id'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT id, filename, comment FROM photos WHERE owner_id = ? ORDER BY id DESC LIMIT 5");
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($photoID, $filename, $comment);

while ($stmt->fetch()) {
    echo "<div>";
    echo "<img src='../Pages/uploads/" . $filename . "' alt='Uploaded photo'>";
    echo "<p>" . htmlspecialchars($comment) . "</p>";
    echo "</div>";
}

$stmt->close();
?>
