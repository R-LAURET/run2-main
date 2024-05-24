<?php
include 'manager/reservationManager.php';
$db= new Database();
$controlleur= new Controller($db);
$reservationManager = new ReservationManager($db);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_reservation = $_POST["idReservation"]; 


    $resultat = $reservationManager->supprimerReservation($id_reservation);

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