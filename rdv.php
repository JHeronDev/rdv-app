<?php
require 'includes/db.php';
require 'includes/auth.php';
rediriger_si_non_connecte();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'dates_disponibles') {
    $stmt = $connexion->query("SELECT DISTINCT date FROM plages_horaires WHERE id NOT IN (
        SELECT plages_horaires.id FROM plages_horaires
        JOIN rdv ON rdv.date = plages_horaires.date AND rdv.heure = plages_horaires.heure
    )");
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    header('Content-Type: application/json');
    echo json_encode($dates);
    exit;
}


// Traitement AJAX pour les créneaux
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'reservation') {
        $date = $_POST['date'];
        $heure = $_POST['heure'];
        $service_id = $_POST['service_id'];
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Vérifier si le créneau est déjà pris
        $stmt = $connexion->prepare("SELECT COUNT(*) FROM rdv WHERE date = ? AND heure = ?");
        $stmt->execute([$date, $heure]);
        if ($stmt->fetchColumn() > 0) {
            echo "❌ Ce créneau est déjà réservé.";
            exit;
        }

        // Enregistrer le rendez-vous
        $stmt = $connexion->prepare("INSERT INTO rdv (utilisateur_id, service_id, date, heure, statut) VALUES (?, ?, ?, ?, 'confirmé')");
        $stmt->execute([$utilisateur_id, $service_id, $date, $heure]);

        echo "✅ Rendez-vous réservé pour le $date à $heure.";
        exit;

    } elseif (isset($_POST['date'])) {
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

    <h3>Choisissez un service :</h3>
    <form id="form-rdv">
        <select id="service-select" name="service_id" required>
            <option value="">-- Sélectionner un service --</option>
            <?php
            $services = $connexion->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($services as $service) {
                echo "<option value=\"{$service['id']}\">{$service['nom']} ({$service['duree']} min - {$service['prix']}€)</option>";
            }
            ?>
        </select>
    </form>

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

    <h3>Créneaux disponibles pour le <span id="jour-selectionne">...</span> :</h3>

    <div id="liste-creneaux" class="zone-creneaux">Veuillez selectionner un jour</div>

    <script src="script.js"></script>
</body>

</html>