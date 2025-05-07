<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === 'guest') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// Ensure the logged-in user is the owner of the post
$result = $conn->query("SELECT user_id FROM weather_updates WHERE id = $id");
$row = $result->fetch_assoc();
if ($row['user_id'] != $user_id) {
    echo "Unauthorized action.";
    exit();
}

$stmt = $conn->prepare('DELETE FROM weather_updates WHERE id = ?');
$stmt->bind_param('d', $id);
$stmt->execute();
$stmt->close();
$conn->close();
header('Location: dashboard.php?msg=Delete successful');
exit();
?>
