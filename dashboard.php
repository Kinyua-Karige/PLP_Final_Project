<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $goal_amount = $_POST['goal_amount'];

    $stmt = $pdo->prepare("INSERT INTO campaigns (user_id, title, description, goal_amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $goal_amount]);
    echo "Campaign created!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Create a New Campaign</h2>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Campaign Title" required><br>
        <textarea name="description" placeholder="Campaign Description" required></textarea><br>
        <input type="number" name="goal_amount" placeholder="Goal Amount" required><br>
        <button type="submit">Create Campaign</button>
    </form>
</body>
</html>
