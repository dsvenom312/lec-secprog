<?php
include '../Controller/connection.php';

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT filename, comment FROM photos ORDER BY id DESC LIMIT 5");
$stmt->execute();
$stmt->bind_result($filename, $comment);

while ($stmt->fetch()) {
    echo "<div>";
    echo "<img src='" . $filename . "' alt='Uploaded photo'>";
    echo "<p>" . htmlspecialchars($comment) . "</p>";
    echo "</div>";
}

$stmt->close();
?>
