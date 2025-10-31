<?php
require '../database.php'; // Adjust path if needed

// Check if required parameters are provided
if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    $allowedTypes = ['notes', 'questions', 'solutions'];

    if (!in_array($type, $allowedTypes)) {
        echo "❌ Invalid type provided.";
        exit;
    }

    // Fetch the file path first
    $sql = "SELECT file_path FROM `$type` WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_path);
        $stmt->fetch();

        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $stmt->close();

        // Now delete the record from the database
        $delete_sql = "DELETE FROM `$type` WHERE id = ?";
        $delete_stmt = $con->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);

        if ($delete_stmt->execute()) {
            echo "<script>alert('✅ Deleted successfully.'); window.location.href='delete.php';</script>";
        } else {
            echo "❌ Error deleting record: " . $delete_stmt->error;
        }

        $delete_stmt->close();
    } else {
        echo "❌ Record not found.";
    }
} else {
    echo "❌ Invalid request. Parameters missing.";
}
?>
