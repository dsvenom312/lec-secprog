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
    echo "<form id='editForm_{$photoID}' style='display:none;' method='post' action='../Controller/edit_handler.php'>";
    echo "<input type='hidden' name='photoID' value='{$photoID}'>";
    echo "<label for='editedComment'>Edit comment:</label>";
    echo "<input type='text' name='editedComment' value='" . htmlspecialchars($comment) . "' required>";
    echo "<input type='submit' value='Save'>";
    echo "</form>";

    echo "<form method='post' action='../Controller/delete_handler.php'>";
    echo "<input type='hidden' name='photoID' value='{$photoID}'>";
    echo "<input type='submit' value='Delete' onclick='return confirmDelete()'>";
    echo "</form>";

    echo "</div>";
}

$stmt->close();
?>