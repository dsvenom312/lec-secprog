<?php
session_start();
require "./connection.php";

// Validate the photoID
if (!isset($_POST['photoID']) || !filter_var($_POST['photoID'], FILTER_VALIDATE_INT)) {
    $_SESSION["error_message"] = "Invalid photo ID.";
    header("Location: ../Pages/home.php");
    exit();
}

$photoID = $_POST['photoID'];
$userID = $_SESSION['id'];

// Check if the user owns the photo
$stmt = $conn->prepare("SELECT owner_id, filename FROM photos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $photoID);
$stmt->execute();
$stmt->bind_result($ownerID, $filename);
$stmt->fetch();
$stmt->close();

if ($ownerID !== $userID) {
    $_SESSION["error_message"] = "You do not have permission to delete this photo.";
    header("Location: ../Pages/home.php");
    exit();
}

// Check if the delete confirmation is submitted
if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
    // Delete the photo from the database
    $stmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
    $stmt->bind_param("i", $photoID);
    $stmt->execute();
    $stmt->close();

    // Delete the photo file from the server
    $filePath = __DIR__ . "/uploads/" . $filename;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $_SESSION["success_message"] = "Photo deleted successfully.";
    header("Location: ../Pages/home.php");
    exit();
}
?>

<!-- Add a confirmation form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Photo Confirmation</title>
</head>
<body>
    <h2>Confirm Deletion</h2>
    <p>Are you sure you want to delete this photo?</p>
    <form action="" method="post">
        <input type="hidden" name="photoID" value="<?php echo $photoID; ?>">
        <input type="hidden" name="confirm_delete" value="yes">
        <button type="submit">Yes, Delete</button>
        <a href="../Pages/home.php">Cancel</a>
    </form>
</body>
</html>
