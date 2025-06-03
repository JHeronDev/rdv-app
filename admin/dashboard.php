<?php
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/functions.php';
rediriger_si_non_admin();


// Stats simples
$nb_utilisateurs = $connexion->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nb_rdv = $connexion->query("SELECT COUNT(*) FROM rdv")->fetchColumn();
$nb_services = $connexion->query("SELECT COUNT(*) FROM services")->fetchColumn();
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

            <?php endif; ?>
        </div>

        <div class="nav-links">
            <?php if (est_connecte()): ?>
                <button onclick="deconnecter()">Déconnexion</button>
            <?php endif; ?>
        </div>

    </nav>


    <h1>Tableau de bord Admin</h1>

    <p>Utilisateurs inscrits : <?= $nb_utilisateurs ?></p>
    <p>Rendez-vous pris : <?= $nb_rdv ?></p>
    <p>Services proposés : <?= $nb_services ?></p>

    <h2>Actions administratives</h2>
    <div class="admin-links">
        <a href="manage_slots.php">Gérer les créneaux</a>
        <a href="manage_users.php">Gérer les utilisateurs</a>
        <a href="services.php">Gérer les services</a>
        <a href="export.php" target="_blank">Exporter les rendez-vous (pdf)</a>
    </div>

    <script src="../script.js"></script>
</body>

</html>