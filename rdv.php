<?php
require 'includes/db.php';
require 'includes/auth.php';
rediriger_si_non_connecte();


// Traitement AJAX pour les créneaux
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    require 'includes/db.php';

    $date = $_POST['date'];

    $stmt = $connexion->prepare("SELECT heure FROM plages_horaires WHERE date = ? AND id NOT IN (
        SELECT plages_horaires.id FROM plages_horaires
        JOIN rdv ON rdv.date = plages_horaires.date AND rdv.heure = plages_horaires.heure
        WHERE rdv.date = ?
    ) ORDER BY heure ASC");

    $stmt->execute([$date, $date]);
    $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($creneaux);
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil - RDV App</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">

        <div class="logo">
            <a href="index.php">RDV App</a>
        </div>

        <div class="nav-links">

            <?php if (est_connecte()): ?>

                <a href="profile.php">Mon profil</a>
                <?php if (est_admin()): ?>
                    <a href="admin/dashboard.php">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="nav-links">
            <?php if (est_connecte()): ?>
                <button onclick="deconnecter()">Déconnexion</button>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
            <?php endif; ?>
        </div>

    </nav>

    <link rel="stylesheet" href="public/css/calendrier.css">
    <h2>Réserver un rendez-vous</h2>
    <div id="calendrier">
        <div class="cal-header">
            <button id="mois-precedent">←</button>
            <span id="mois-annee"></span>
            <button id="mois-suivant">→</button>
        </div>
        <div class="cal-jours">
            <div>Lun</div>
            <div>Mar</div>
            <div>Mer</div>
            <div>Jeu</div>
            <div>Ven</div>
            <div>Sam</div>
            <div>Dim</div>
        </div>
        <div class="cal-dates" id="cal-dates"></div>
    </div>

    <h3>Créneaux disponibles :</h3>
    <div id="liste-creneaux" class="zone-creneaux"></div>

    <script src="script.js"></script>
</body>

</html>

