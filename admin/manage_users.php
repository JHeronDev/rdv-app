<?php
require '../includes/db.php';
require '../includes/auth.php';
rediriger_si_non_admin();


// admin/manage_users.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer']) && is_numeric($_POST['supprimer'])) {
    $id = (int) $_POST['supprimer'];
    if ($id !== $_SESSION['utilisateur_id']) {
        $stmt = $connexion->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
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
                            <form method="POST" style="display:inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                <input type="hidden" name="supprimer" value="<?= $utilisateur['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>

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