<?php
include 'manager/reservationManager.php';
$db= new Database();
$controlleur= new Controller($db);
$reservationManager = new ReservationManager($db);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProprio= $_POST["idProprio"];
    $id_reservation = $_POST["id_reservation"]; 
    $idUtilisateur = $_POST["idUtilisateur"];
    $dateDebutTempo = $_POST["dateDebut"];
    $dateFinTempo= $_POST["dateFin"];


    $montant = $reservationManager -> getMontantReservationByNuit($idProprio, $id_reservation,$dateDebutTempo,$dateFinTempo);
    //renvoie le montant de la reservation 
    $resultat = $reservationManager -> modifierReservationAvecMontant($id_reservation,$idUtilisateur,$idProprio,$dateDebutTempo,$dateFinTempo,$montant);

    var_dump($idUtilisateur);

    if ($resultat){
        header("Location: index.php?action=AfficherReservationAdmin");
        exit();
    }else{
        header("Location: page_de_modificationddkdk.php");
        exit();
    }

} else {
    header("Location: page_de_modification.php");
    exit();
}
?>