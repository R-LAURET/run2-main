<?php

class Controller {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }


    /**
     * Connecte un utilisateur en vérifiant les informations d'identification dans la base de données.
     *
     * Cette méthode permet de connecter un utilisateur en vérifiant si l'e-mail correspond à un utilisateur existant dans la base de données et si le mot de passe fourni correspond au mot de passe haché stocké dans la base de données.
     *
     * @param string $email L'adresse e-mail de l'utilisateur à connecter.
     * @param string $mdp Le mot de passe de l'utilisateur à connecter (non haché).
     * @return array|false Retourne un tableau associatif représentant l'utilisateur connecté si les informations d'identification sont valides, sinon false.
     */

    private function Connecter($email, $mdp) {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $params = array(':email' => $email);
        $resultat = $this->database->executer($sql, $params);
        $utilisateur = $resultat->fetch(PDO::FETCH_ASSOC);
    
        if ($utilisateur) {
            // Utilisation de password_verify pour vérifier le mot de passe
            if (password_verify($mdp, $utilisateur['mdp'])) {
                session_start();
                $_SESSION['utilisateur'] = $utilisateur;
                $_SESSION['id'] = $utilisateur['idUtilisateur'];
                return $utilisateur;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Crée un nouvel utilisateur dans la base de données.
     *
     * Cette méthode permet de créer un nouvel utilisateur en insérant ses informations dans la table des utilisateurs de la base de données.
     *
     * @param string $nom Le nom de l'utilisateur.
     * @param string $prenom Le prénom de l'utilisateur.
     * @param int $age L'âge de l'utilisateur.
     * @param string $email L'adresse e-mail de l'utilisateur.
     * @param string $mdp Le mot de passe de l'utilisateur (non haché).
     * @return bool Retourne true si la création de l'utilisateur est réussie, sinon false.
     */
    public function createUser($nom, $prenom, $age, $email, $mdp) {

        $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO utilisateurs (nom, prenom, age, email, mdp) VALUES (:nom, :prenom, :age, :email, :mdp)";
        $params = array(
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':age' => $age,
            ':email' => $email,
            ':mdp' => $hashedPassword
        );
        
        $resultat = $this->database->executer($sql, $params);
        
        return $resultat;
    }



    /**
     * Modifie les informations d'une propriété dans la base de données.
     *
     * Cette méthode permet de mettre à jour les informations d'une propriété existante dans la base de données, telles que le nom, la description, l'adresse, le nombre de chambres et le tarif.
     *
     * @param int $id L'identifiant de la propriété à modifier.
     * @param string $nom Le nouveau nom de la propriété.
     * @param string $description La nouvelle description de la propriété.
     * @param string $adresse La nouvelle adresse de la propriété.
     * @param int $nombreChambre Le nouveau nombre de chambres de la propriété.
     * @param float $tarif Le nouveau tarif de la propriété.
     * @return void
     */
    function modifierPropriete($id, $nom, $description, $adresse, $nombreChambre, $tarif) {
        
        try {
    
            // Mise à jour des données dans la base de données
            $sql = "UPDATE propriete SET nom = :nom, description = :description, adresse = :adresse, nombreChambre = :nombreChambre, tarif = :tarif WHERE idProprio = :id";
            $stmt = $this->database->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':nombreChambre', $nombreChambre);
            $stmt->bindParam(':tarif', $tarif);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            // Redirection vers une page de succès ou affichage d'un message de succès
            header("Location: index.php?action=AfficherMonCompte&&message=sucess");
            exit();
        } catch (PDOException $e) {
            // Gérer les erreurs de base de données
            echo "Erreur lors de la modification de la propriété: " . $e->getMessage();
        }
    }


    /**
     * Modifie une réservation dans la base de données.
     *
     * Cette méthode permet de mettre à jour les dates de début et de fin d'une réservation existante dans la base de données.
     *
     * @param int $id L'identifiant de la réservation à modifier.
     * @param string $dateDebut La nouvelle date de début de la réservation.
     * @param string $dateFin La nouvelle date de fin de la réservation.
     * @return void
     */
    function modifierReservation($id, $dateDebut, $dateFin) {

        try {

            // Mise à jour des données dans la base de données
            $sql = "UPDATE reservation SET dateDebut = :dateDebut, dateFin = :dateFin WHERE idReservation = :id";
            $stmt = $this->database->prepare($sql);
            $stmt->bindParam(':dateDebut', $dateDebut);
            $stmt->bindParam(':dateFin', $dateFin);
            $stmt->bindParam(':id', $id);
            $resultat = $stmt->execute();

            if($resultat) {
                // Redirection vers une page de succès ou affichage d'un message de succès
                header("Location: index.php?action=AfficherReservationAdmin&&message=sucess");
                exit();

            }else{
                header("Location: index.php?action=AfficherReservationAdmin&&message=fail");
                exit();
            }


            } catch (PDOException $e) {
                // Gérer les erreurs de base de données
                echo "Erreur lors de la modification de la reservation: " . $e->getMessage();
            }
    }

    public function handleRequest() {
        // Récupérer l'action à partir de la requête utilisateur
        $action = isset($_GET['action']) ? $_GET['action'] : 'AfficherAccueil';

        // Exécuter l'action appropriée
        switch ($action) {
            case 'AfficherAccueil':
                $this->AfficherAccueil();
                break;
            case 'AfficherAccueilUtilisateur':
                $this->AfficherAccueilUtilisateur();
                break;
            case 'AfficherUnePropriete':
                $this->AfficherUnePropriete();
                break;
            case 'AfficherUneProprieteAdmin':
                $this->AfficherUneProprieteAdmin();
                break;
            case 'AfficherConnexion':
                $this->AfficherConnexion();
                break;
            case 'AfficherInscription':
                $this->AfficherInscription();
                break;
            case 'InscriptionUtilisateur':
                $this->InscriptionUtilisateur();
                break;
            case 'ConnecterUtilisateur':
                $this->ConnecterUtilisateur();
                break;
            case 'AfficherLesProprietes':
                $this->AfficherLesPropriete();
                break;
            case 'AfficherReservation':
                $this->AfficherReservation();
                break;
            case 'TraiterReservation':
                $this->TraiterReservation();
                break;
            case 'AfficherMonCompte':
                $this->AfficherMonCompte();
                break;
            case 'AfficherModifCompte':
                $this->AfficherModifCompte();
                break;
            case 'AfficherModifProprioAdmin':
                $this->AfficherModifProprioAdmin();
                break;
            case 'ModifierCompte':
                $this->ModifierCompte();
                break;
            case 'ModifierCompteAdmin':
                $this->ModifierCompteAdmin();
                break;
            case 'AfficherPublicationProprio':
                $this->CreerPropriete();
                break;
            case 'InsererProprio':
                $this->InsererProprio();
                break;
            case 'ModificationImage':
                $this->ModificationImage();
                break;
            case 'ModifierImageProprio':
                $this->ModifierImageProprio();
                break;
            case 'SupprimerPropriete':
                $this->SupprimerPropriete();
                break;
            case 'SupprimerProprieteAdmin':
                $this->SupprimerProprieteAdmin();
                break;
            case 'AfficherAdministrationGlobale':
                $this->AfficherAdministrationGlobale();
                break;
            case 'AfficherModerationAvis':
                $this->AfficherModerationAvis();
                break;
            case 'AfficherModerationPropriete':
                $this->AfficherModerationPropriete();
                break;
            case 'moderationAvis':
                $this->moderationAvis();
                break;
            case 'AfficherModificationInfosUtilisateur':
                $this->AfficherModificationInfosUtilisateur();
                break;
            case 'ModifierInfosUtilisateur':
                $this->ModifierInfosUtilisateur();
                break;
            case'AfficherModificationMDP':
                $this->AfficherModificationMDP();
                break;
            case 'traiterNouveauMDP':
                $this->traiterNouveauMDP();
                break;
            case'AfficherInsererAvis':
                $this->AfficherInsererAvis();
                break;
            case 'deconnexion':
                session_start();
                session_destroy();
                header("Location: index.php?action=AfficherAccueil");
                exit(); 
                break;
            case 'insererAvis':
                $this->insererAvis();
                break;
            case 'AfficherReservationAdmin':
                $this->AfficherReservationAdmin();
                break;
            case 'ReservationModifAdmin':
                $this->ReservationModifAdmin();
                break;
            case 'traitementReservationAdmin':
                $this->traitementReservationAdmin();
                break;
            case 'ReservationSupprimerAdmin':
                $this->ReservationSupprimerAdmin();
                break;
            case 'AfficherMention':
                $this->AfficherMention();
                break;
            case 'AfficherPolitique':
                $this->AfficherPolitique();
                break;
            default:
                $this->AfficherAccueil();
                break;
        }
    }

    // Méthodes pour chaque action

    private function AfficherAccueil() {
        include 'views/accueil.php';
    }
    private function AfficherAccueilUtilisateur() {
        include 'views/accueilUtilisateur.php';
    }
    private function AfficherUnePropriete() {
        include 'views/voirPlusPropriete.php';
    }
    private function AfficherUneProprieteAdmin() {
        include 'views/voirPlusProprieteAdmin.php';
    }
    private function AfficherConnexion() {
        include 'views/connexion.php';
    }
    private function AfficherInscription() {
        include 'views/inscription.php';
    }
    private function InscriptionUtilisateur() {
        include 'traitement/traitementInscription.php';
    }
    private function ConnecterUtilisateur() {
        include 'traitement/traitementConnexion.php';
    }
    private function AfficherLesPropriete() {
        include 'views/vueDesProprietes.php';
    }
    private function AfficherModerationPropriete() {
        include 'views/moderationPropriete.php';
    }
    private function AfficherReservation() {
        include 'views/vueReservation.php';
    }
    private function TraiterReservation() {
        include 'traitement/traitementReservation.php';
    }
    private function AfficherMonCompte() {
        include 'views/vueMonCompte.php';
    }
    private function AfficherModifCompte() {
        include 'views/modificationCompte.php';
    }
    private function AfficherModifProprioAdmin() {
        include 'views/modificationProprioAdmin.php';
    }
    private function ModifierCompte() {
        include 'traitement/traitementModification.php';
    }
    private function ModifierCompteAdmin() {
        include 'traitement/traitementModifProprioAdmin.php';
    }
    private function CreerPropriete() {
        include 'views/vueCreerPropriete.php';
    }
    private function InsererProprio() {
        include 'traitement/traitementPropriete.php';
    }
    private function ModificationImage() {
        include 'views/modificationImage.php';
    }
    private function ModifierImageProprio() {
        include 'traitement/traitementModificationImage.php';
    }
    private function AfficherAdministrationGlobale() {
        include 'views/AdministrationGlobale.php';
    }
    private function AfficherModerationAvis() {
        include 'views/moderationAvis.php';
    }
    private function moderationAvis() {
        include 'traitement/traitementModerationAvis.php';
    }
    private function SupprimerPropriete() {
        include 'traitement/traitementSupprimerPropriete.php';
    }
    private function SupprimerProprieteAdmin() {
        include 'traitement/traitementSupprimerProprieteAdmin.php';
    }
    private function AfficherModificationInfosUtilisateur() {
        include 'views/modificationInfosUtilisateur.php';
    }
    private function ModifierInfosUtilisateur() {
        include 'traitement/traitementModificationInfosUtilisateur.php';
    }
    private function AfficherModificationMDP() {
        include 'views/modificationMDP.php';
    }
    private function traiterNouveauMDP() {
        include 'traitement/traitementModificationMDP.php';
    }
    private function AfficherInsererAvis(){
        include 'views/insererAvis.php';
    }
    private function insererAvis(){
        include 'traitement/traitementInsererAvis.php';
    }
    private function AfficherReservationAdmin (){
        include 'views/reservationAdmin.php';
    }
    private function ReservationModifAdmin (){
        include 'views/ReservationModifAdmin.php';
    }
    private function traitementReservationAdmin() {
        include 'traitement/traitementReservationAdmin.php';
    }
    private function ReservationSupprimerAdmin() {
        include 'traitement/traitementSuppResAdmin.php';
    }
    private function AfficherMention() {
        include 'views/mention.php';
    }
    private function AfficherPolitique() {
        include 'views/politique.php';
    }

}   
?>
