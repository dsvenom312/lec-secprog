<<<<<<< Updated upstream
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
=======
<?php
// session_start();
include '../Controller/connection.php';

$userID = $_SESSION['id'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT id, filename, comment FROM photos WHERE owner_id = ? ORDER BY id ");
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($photoID, $filename, $comment);

while ($stmt->fetch()) {
    echo "<div class='photo-container'>";
    echo "<img src='../Pages/uploads/" . $filename . "' alt='" . htmlspecialchars($comment) . "'>";
    echo "<p id='comment_{$photoID}'>" . htmlspecialchars($comment) . "</p>";
    echo "<button onclick='toggleEditForm({$photoID})'>Edit</button>";

    // Edit form
    echo "<form id='editForm_{$photoID}' style='display:none;' method='post' action='./edit_comment.php'>";
    echo "<input type='hidden' name='photoID' value='{$photoID}'>";
    echo "<label for='editedComment'>Edit comment:</label>";
    echo "<input type='text' name='editedComment' value='" . htmlspecialchars($comment) . "' required>";
    echo "<input type='submit' value='Save'>";
    echo "</form>";

    // Delete form
    echo "<form method='post' action='./delete_photo.php' onsubmit='return confirm(\"Are you sure you want to delete this photo?\");'>";
    echo "<input type='hidden' name='photoID' value='{$photoID}'>";
    echo "<input type='submit' value='Delete'>";
    echo "</form>";

    echo "</div>";
}

$stmt->close();
?>
>>>>>>> Stashed changes
