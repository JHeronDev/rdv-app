<?php
require '../includes/db.php';
require '../includes/auth.php';
rediriger_si_non_admin();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $heure = $_POST['heure'];

    $sql = "INSERT INTO plages_horaires (date, heure) VALUES (?, ?)";
    $requete = $connexion->prepare($sql);
    $requete->execute([$date, $heure]);
}
$plages = $connexion->query("SELECT * FROM plages_horaires ORDER BY date, heure")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil - RDV App</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <nav class="navbar">

        <div class="logo">
            <a href="../index.php">RDV App</a>
        </div>

        <div class="nav-links">

            <?php if (est_connecte()): ?>
                <a href="../rdv.php">Réserver</a>
                <a href="../profile.php">Mon profil</a>
                <?php if (est_admin()): ?>
                    <a href="dashboard.php">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <?php if (est_connecte()): ?>
                <button onclick="deconnecter()">Déconnexion</button>
            <?php endif; ?>
        </div>

    </nav>

    <h2>Ajouter un créneau</h2>
    <form method="POST">
        <input type="date" name="date" required>
        <input type="time" name="heure" required>
        <button type="submit">Ajouter</button>
    </form>

    <h2>Créneaux existants</h2>
    <ul>
        <?php foreach ($plages as $p): ?>
            <li><?= $p['date'] ?> à <?= $p['heure'] ?></li>
        <?php endforeach; ?>
    </ul>

    <script src="../script.js"></script>
</body>

</html>