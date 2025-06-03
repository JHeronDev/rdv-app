<?php

function obtenir_utilisateur_par_email($email, $connexion)
{
    $sql = "SELECT * FROM utilisateurs WHERE email = ?";
    $requete = $connexion->prepare($sql);
    $requete->execute([$email]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function enregistrer_rdv($utilisateur_id, $service_id, $date, $heure, $connexion)
{
    $sql = "INSERT INTO rdv (utilisateur_id, service_id, date, heure) VALUES (?, ?, ?, ?)";
    $requete = $connexion->prepare($sql);
    return $requete->execute([$utilisateur_id, $service_id, $date, $heure]);
}

function lister_creneaux_disponibles($connexion)
{
    $sql = "SELECT * FROM plages_horaires WHERE id NOT IN (
        SELECT id FROM rdv WHERE date = plages_horaires.date AND heure = plages_horaires.heure
    ) ORDER BY date, heure";
    $requete = $connexion->query($sql);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

?>