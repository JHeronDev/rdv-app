<?php
require '../includes/db.php';
require '../includes/auth.php';
rediriger_si_non_admin();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_slot'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM plages_horaires WHERE id = ?";
        $requete = $connexion->prepare($sql);
        $requete->execute([$id]);

    } elseif (isset($_POST['delete_rdv'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM rdv WHERE id = ?";
        $requete = $connexion->prepare($sql);
        $requete->execute([$id]);
    } else {
        $date = $_POST['date'];
        $heure = $_POST['heure'];

        $sql = "INSERT INTO plages_horaires (date, heure) VALUES (?, ?)";
        $requete = $connexion->prepare($sql);
        $requete->execute([$date, $heure]);
    }

    header("Location: manage_slots.php");
    exit;


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
    <?php
    $rdvs = $connexion->query("SELECT rdv.*, utilisateurs.nom AS client, services.nom AS service
                           FROM rdv
                           JOIN utilisateurs ON rdv.utilisateur_id = utilisateurs.id
                           JOIN services ON rdv.service_id = services.id
                           ORDER BY date, heure")->fetchAll();
    ?>
    <h2>Gestion des créneaux et rendez-vous</h2>

    <div style="display: flex; gap: 100px; justify-content: center;">
        <!-- Créneaux disponibles -->
        <div>
            <h3>Créneaux existants</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plages as $p): ?>
                        <tr>
                            <td><?= $p['date'] ?></td>
                            <td><?= $p['heure'] ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Supprimer ce créneau ?');">
                                    <input type="hidden" name="delete_slot" value="1">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Rendez-vous réservés -->
        <div>
            <h3>Rendez-vous réservés</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rdvs as $r): ?>
                        <tr>
                            <td><?= $r['date'] ?></td>
                            <td><?= $r['heure'] ?></td>
                            <td><?= htmlspecialchars($r['client']) ?></td>
                            <td><?= htmlspecialchars($r['service']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Supprimer ce rendez-vous ?');">
                                    <input type="hidden" name="delete_rdv" value="1">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <script src="../script.js"></script>
</body>

</html>