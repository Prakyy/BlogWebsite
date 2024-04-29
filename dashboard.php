<?php
session_start();
require_once 'functions.php';

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = getUserIdFromUsername($_SESSION["username"], $conn);
    $content = $_POST["content"];
    $image_path = "";

    if (!empty($_FILES["image"]["tmp_name"])) {
        $image_path = uploadImage($_FILES["image"]);
    }

    // Prepare and execute the SQL statement to insert the post
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $content, $image_path);
    $stmt->execute();
    $stmt->close();
}

// Retrieve and display the user's posts
$user_id = getUserIdFromUsername($_SESSION["username"], $conn);
$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>
    <div class="post-input">
        <form method="post" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" rows="3"></textarea>
            <input type="file" name="image">
            <input type="submit" value="Post">
        </form>
    </div>
    <div class="posts-container">
        <h2>Your Posts</h2>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='post-container'>";
            echo "<p>" . $row["content"] . "</p>";
            if (!empty($row["image_path"])) {
                echo "<img class='post-image' src='" . $row["image_path"] . "' alt='Post Image'>";
            }
            echo "</div>";
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>