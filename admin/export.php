<?php
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/fpdf.php';
rediriger_si_non_admin();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, mb_convert_encoding('Liste des rendez-vous', 'Windows-1252', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(5);

// En-tête du tableau
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 200, 200); // gris clair
$pdf->Cell(40, 10, 'Nom', 1, 0, 'C', true);
$pdf->Cell(50, 10, mb_convert_encoding('Service', 'Windows-1252', 'UTF-8'), 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Heure', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Statut', 1, 1, 'C', true);

// Données
$pdf->SetFont('Arial', '', 11);
$rdvs = $connexion->query("
    SELECT r.*, u.nom AS client_nom, s.nom AS service_nom 
    FROM rdv r
    JOIN utilisateurs u ON r.utilisateur_id = u.id 
    JOIN services s ON r.service_id = s.id 
    ORDER BY r.date, r.heure
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rdvs as $rdv) {
    $pdf->Cell(40, 10, mb_convert_encoding($rdv['client_nom'], 'Windows-1252', 'UTF-8'), 1);
    $pdf->Cell(50, 10, mb_convert_encoding($rdv['service_nom'], 'Windows-1252', 'UTF-8'), 1);
    $pdf->Cell(30, 10, $rdv['date'], 1);
    $pdf->Cell(25, 10, substr($rdv['heure'], 0, 5), 1);
    $pdf->Cell(30, 10, ucfirst(mb_convert_encoding($rdv['statut'], 'Windows-1252', 'UTF-8')), 1);
    $pdf->Ln();
}

$pdf->Output('D', 'rendezvous.pdf'); // 'I' pour affichage dans le navigateur
