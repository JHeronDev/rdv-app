<?php
require '../includes/db.php';
require '../includes/auth.php';
rediriger_si_non_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout ou modification
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'ajouter') {
            $stmt = $connexion->prepare("INSERT INTO services (nom, duree, prix) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['nom'], $_POST['duree'], $_POST['prix']]);
        } elseif ($_POST['action'] === 'modifier') {
            $stmt = $connexion->prepare("UPDATE services SET nom = ?, duree = ?, prix = ? WHERE id = ?");
            $stmt->execute([$_POST['nom'], $_POST['duree'], $_POST['prix'], $_POST['id']]);
        } elseif ($_POST['action'] === 'supprimer') {
            $stmt = $connexion->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$_POST['id']]);
        }
    }
}

$services = $connexion->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
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
    <h2>Gestion des services</h2>

    <table border="1">
        <tr>
            <th>Nom</th>
            <th>Durée (min)</th>
            <th>Prix (€)</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($services as $s): ?>
            <tr>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <td><input type="text" name="nom" value="<?= htmlspecialchars($s['nom']) ?>"></td>
                    <td><input type="number" name="duree" value="<?= $s['duree'] ?>"></td>
                    <td><input type="number" name="prix" value="<?= $s['prix'] ?>"></td>
                    <td>
                        <button name="action" value="modifier">Modifier</button>
                        <button name="action" value="supprimer" onclick="return confirm('Supprimer ?')">Supprimer</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
        <tr>
            <form method="POST">
                <td><input type="text" name="nom" required></td>
                <td><input type="number" name="duree" required></td>
                <td><input type="number" name="prix" required></td>
                <td><button name="action" value="ajouter">Ajouter</button></td>
            </form>
        </tr>
    </table>

    <script src="../script.js"></script>
</body>

</html>