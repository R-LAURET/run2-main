<?php

include ('manager/proprieteManager.php');

$db= new Database();
$proprieteManager = new ProprieteManager($db);

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_propriete = $_POST['idProprio']; 
    $nom = $_POST["nom"];
    $description = $_POST["description"];
    $adresse = $_POST["adresse"];
    $nombreChambre = $_POST["nombreChambre"];
    $tarif = $_POST["tarif"];

    // Appel de la fonction pour modifier la propriété
    $resultat = $proprieteManager->modifierPropriete($id_propriete, $nom, $description, $adresse, $nombreChambre, $tarif);

    if($resultat) {
        // Redirection vers une page de succès ou affichage d'un message de succès
        header("Location: index.php?action=AfficherModerationPropriete&&message=sucess");
        exit();
    } else {
        header("Location: index.php?action=AfficherModifProprioAdmin&&message=fail");
        exit();
    }

} else {
    header("Location: page_de_modification.php");
    exit();
}
?>
