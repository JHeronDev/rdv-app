<?php
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $utilisateur = obtenir_utilisateur_par_email($email, $connexion);
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['role'] = $utilisateur['role'];
        header('Location: ' . ($utilisateur['role'] === 'admin' ? 'admin/dashboard.php' : 'profile.php'));
        exit();
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
function obtenir_utilisateur_par_email($email, $connexion)
{
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $requete = $connexion->prepare($sql);
    $requete->execute([$email]);
    return $requete->fetch(PDO::FETCH_ASSOC);
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
        <input type="email" name="email" placeholder="Votre email" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Connexion</button>
        <?php if (isset($erreur))
            echo "<p>$erreur</p>"; ?>
    </form>
    <script src="script.js"></script>
</body>

</html>