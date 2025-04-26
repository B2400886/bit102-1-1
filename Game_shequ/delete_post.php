<?php
session_start();
include 'db.php'; // Include database connection file

if (isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // First, delete all likes related to the post from the likes table
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete all associations related to the post from the bookmarks table
        $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        // Then delete the post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect or display success message
        header('Location: home.php');
        exit;
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $pdo->rollBack();
        echo "Failed to delete post: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
?>
