<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/functions.php';


$id_utilisateur = $_SESSION['utilisateur_id'];


// Récupération des infos utilisateur
$requete = $connexion->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$requete->execute([$id_utilisateur]);
$utilisateur = $requete->fetch();

// Mise à jour des infos personnelles
if (isset($_POST['modifier_infos'])) {
    $nouveau_nom = $_POST['nom'];
    $nouvel_email = $_POST['email'];

    $update = $connexion->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
    $update->execute([$nouveau_nom, $nouvel_email, $id_utilisateur]);
    header("Location: profile.php");
    exit();
}

// Changement de mot de passe
if (isset($_POST['changer_mot_de_passe'])) {
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];

    if (password_verify($ancien_mdp, $utilisateur['mot_de_passe'])) {
        $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        $update = $connexion->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        $update->execute([$hash, $id_utilisateur]);
        $message = "Mot de passe modifié avec succès.";
    } else {
        $erreur = "Ancien mot de passe incorrect.";
    }
}
// Annulation de rendez-vous
if (isset($_POST['annuler_rdv']) && isset($_POST['rdv_id'])) {
    $rdv_id = $_POST['rdv_id'];

    // On vérifie que le rdv appartient bien à l'utilisateur
    $verif = $connexion->prepare("SELECT * FROM rdv WHERE id = ? AND utilisateur_id = ?");
    $verif->execute([$rdv_id, $id_utilisateur]);

    if ($verif->fetch()) {
        // On supprime
        $delete = $connexion->prepare("DELETE FROM rdv WHERE id = ?");
        $delete->execute([$rdv_id]);
        $message = "Rendez-vous annulé.";
    } else {
        $erreur = "Action non autorisée.";
    }
}

// Historique des rendez-vous
$rdv = $connexion->prepare("SELECT r.*, s.nom AS service_nom FROM rdv r JOIN services s ON r.service_id = s.id WHERE r.utilisateur_id = ? ORDER BY date DESC, heure DESC");
$rdv->execute([$id_utilisateur]);
$liste_rdv = $rdv->fetchAll(PDO::FETCH_ASSOC);
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


    <h2>Mon profil</h2>

    <form method="POST">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" required><br>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" required><br>

        <button type="submit" name="modifier_infos">Enregistrer</button>
    </form>

    <h3>Changer le mot de passe</h3>
    <form method="POST">
        <input type="password" name="ancien_mdp" placeholder="Ancien mot de passe" required><br>
        <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required><br>
        <button type="submit" name="changer_mot_de_passe">Changer</button>
    </form>

    <?php if (isset($message))
        echo "<p style='color:green;'>$message</p>"; ?>
    <?php if (isset($erreur))
        echo "<p style='color:red;'>$erreur</p>"; ?>

    <h3>Mes rendez-vous</h3>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Service</th>
                <th>Statut</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($liste_rdv as $rdv): ?>
                <tr>
                    <td><?= $rdv['date'] ?></td>
                    <td><?= $rdv['heure'] ?></td>
                    <td><?= htmlspecialchars($rdv['service_nom']) ?></td>
                    <td><?= $rdv['statut'] ?></td>
                    <td>
                        <?php if (strtotime($rdv['date'] . ' ' . $rdv['heure']) > time()): ?>
                            <form method="POST" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                                <input type="hidden" name="annuler_rdv" value="1">
                                <input type="hidden" name="rdv_id" value="<?= $rdv['id'] ?>">
                                <button type="submit">Annuler</button>
                            </form>
                        <?php else: ?>
                            <em>Passé</em>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="script.js"></script>
</body>

</html>