<?php
include 'includes/db.php';

$stmt = $pdo->query("SELECT * FROM campaigns");
$campaigns = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Campaigns</title>
</head>
<body>
    <h1>Available Campaigns</h1>
    <?php foreach ($campaigns as $campaign): ?>
        <h3><a href="campaign.php?id=<?= $campaign['id'] ?>"><?= $campaign['title'] ?></a></h3>
        <p><?= $campaign['description'] ?></p>
        <p>Goal: $<?= $campaign['goal_amount'] ?></p>
    <?php endforeach; ?>
</body>
</html>
