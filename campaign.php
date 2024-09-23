<?php
include 'includes/db.php';

$campaign_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
$stmt->execute([$campaign_id]);
$campaign = $stmt->fetch();

$stmt_donations = $pdo->prepare("SELECT SUM(amount) AS total_donations FROM donations WHERE campaign_id = ?");
$stmt_donations->execute([$campaign_id]);
$donations = $stmt_donations->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $campaign['title'] ?></title>
</head>
<body>
    <h1><?= $campaign['title'] ?></h1>
    <p><?= $campaign['description'] ?></p>
    <p>Goal: $<?= $campaign['goal_amount'] ?></p>
    <p>Total Raised: $<?= $donations['total_donations'] ?? 0 ?></p>

    <form method="POST" action="donate.php">
        <input type="hidden" name="campaign_id" value="<?= $campaign['id'] ?>">
        <input type="number" name="amount" placeholder="Donation Amount" required>
        <button type="submit">Donate</button>
    </form>
</body>
</html>
