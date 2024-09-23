<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campaign_id = $_POST['campaign_id'];
    $amount = $_POST['amount'];

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO donations (campaign_id, user_id, amount) VALUES (?, ?, ?)");
    if ($stmt->execute([$campaign_id, $_SESSION['user_id'], $amount])) {
        header("Location: campaign.php?id=$campaign_id");
    } else {
        echo "Error donating.";
    }
}
?>
