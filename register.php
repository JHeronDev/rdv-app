<?php

require 'includes/db.php';
require 'includes/auth.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)";
    $requete = $connexion->prepare($sql);
    $requete->execute([$nom, $email, $mot_de_passe]);
    header('Location: login.php');
    exit();
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

    </nav>

    <form method="POST">
        <input type="text" name="nom" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Créer mon compte</button>
    </form>
    <script src="script.js"></script>
</body>

</html>