<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/functions.php';
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
                <a href="rdv.php">Réserver</a>
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


    <main>
        <section class="intro">
            <h2>Réservez vos rendez-vous en ligne</h2>
            <p>Simple. Rapide. Efficace.</p>

            <h3>3 étapes pour réserver :</h3>
            <ul>
                <p>📝 Créez un compte</p>
                <p>📅 Choisissez un créneau</p>
                <p>✅ Réservez en 1 clic</p>
            </ul>
        </section>

    </main>

    <script src="script.js"></script>
</body>



</html>