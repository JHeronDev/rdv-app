<?php
require '../includes/db.php';
require '../includes/auth.php';
rediriger_si_non_admin();


// Suppression utilisateur
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];

    // On empêche l’admin de se supprimer lui-même
    if ($id !== $_SESSION['utilisateur_id']) {
        $requete = $connexion->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $requete->execute([$id]);
    }
}

// Liste utilisateurs
$utilisateurs = $connexion->query("SELECT * FROM utilisateurs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
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


    <h2>Gestion des utilisateurs</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td><?= $utilisateur['id'] ?></td>
                    <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                    <td><?= $utilisateur['role'] ?></td>
                    <td>
                        <?php if ($utilisateur['id'] !== $_SESSION['utilisateur_id']): ?>
                            <a href="?supprimer=<?= $utilisateur['id'] ?>"
                                onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                        <?php else: ?>
                            (vous)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="../script.js"></script>
</body>

</html>