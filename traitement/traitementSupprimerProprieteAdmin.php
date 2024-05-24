<?php 
include 'manager/proprieteManager.php';
$db= new DataBase();
$proprieteManager = new ProprieteManager($db);

if (isset($_GET['id'])){
    $idProprio = $_GET['id'];

    $resultat = $proprieteManager->SupprimerPropriete($idProprio);


    if ($resultat){
        header('Location: index.php?action=AfficherModerationPropriete&&message=SuppressionAvecSucess');
    }else{
        header('Location: index.php?action=AfficherModerationPropriete&&message=SuppressionEchec');
    }
}