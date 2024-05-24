<?php
include 'models/Reservation.php';

class ReservationManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    /**
     * Méthode pour obtenir toutes les réservations.
     * 
     * Cette méthode récupère toutes les réservations enregistrées dans la base de données.
     * 
     * @return array Un tableau d'objets Reservation contenant toutes les réservations.
     */
    public function getAllReservations() {
        $reservations = array();

        $query = "SELECT * FROM reservation";

        $stmt = $this->db->prepare($query);

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Création d'un nouvel objet Reservation avec les données récupérées de la base de données
            $reservation = new Reservation($row['idReservation'], $row['idUtilisateur'], $row['idProprio'], $row['dateDebut'], $row['dateFin'], $row['montant']);
            $reservations[] = $reservation;
        }

        // Retourner le tableau de réservations
        return $reservations;
    }

    /**
     * Méthode pour obtenir les réservations par propriété.
     * 
     * Cette méthode récupère toutes les réservations associées à une propriété spécifique identifiée par son ID de propriétaire.
     * 
     * @param int $idProprio L'identifiant de la propriété pour laquelle récupérer les réservations.
     * @return array Un tableau d'objets Reservation contenant les réservations associées à la propriété spécifiée.
     */
    public function getReservationsByPropriete($idProprio) {
        $reservations = array();

        $sql = "SELECT * FROM reservation WHERE idProprio = :idProprio";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":idProprio", $idProprio);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $reservation = new Reservation(
                $row['idReservation'],
                $row['idUtilisateur'],
                $row['idProprio'],
                $row['dateDebut'],
                $row['dateFin'],
                $row['montant']
            );
            $reservations[] = $reservation;
        }

        return $reservations;
    }

    /**
     * Méthode pour obtenir une réservation par son identifiant.
     * 
     * Cette méthode permet de récupérer les détails d'une réservation en fonction de son identifiant unique.
     * 
     * @param int $idReservation L'identifiant de la réservation à récupérer.
     * @return Reservation|null Un objet Reservation contenant les détails de la réservation si elle existe, sinon null.
     */
    public function getReservationById($idReservation) {
        $query = "SELECT * FROM reservation WHERE idReservation = :idReservation";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':idReservation', $idReservation, PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $reservation = new Reservation($row['idReservation'], $row['idUtilisateur'], $row['idProprio'], $row['dateDebut'], $row['dateFin'], $row['montant']);
            return $reservation;
        } else {
            return null; 
        }
    }


    /**
     * Méthode pour insérer une nouvelle réservation dans la base de données.
     * 
     * Cette méthode permet d'insérer une nouvelle réservation dans la base de données avec les détails spécifiés.
     * 
     * @param int $idUtilisateur L'identifiant de l'utilisateur effectuant la réservation.
     * @param Reservation $reservation L'objet Reservation contenant les détails de la réservation à insérer.
     * @param float $montantPropriete Le montant de la propriété pour la période de réservation.
     * @return bool Retourne true si l'insertion de la réservation est réussie, sinon false.
     */
    public function insertReservation($idUtilisateur, Reservation $reservation, $montantPropriete) {
        $idProprio = $reservation->getIdProprio();
        $dateDebut = $reservation->getDateDebut();
        $dateFin = $reservation->getDateFin();
    
        try {
            // Préparation de la requête SQL
            $sql = "INSERT INTO reservation (idUtilisateur, idProprio, dateDebut, dateFin, montant) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
    
            // Exécution de la requête avec les valeurs liées
            $stmt->execute([$idUtilisateur, $idProprio, $dateDebut, $dateFin, $montantPropriete]);
    
            // Retourner true si l'insertion a réussi, sinon false
            return true;
        } catch (PDOException $e) {
            // Gérer les erreurs PDO si nécessaire
            echo "Erreur lors de l'insertion de la réservation : " . $e->getMessage();
            return false;
        }
    }


    /**
     * Méthode pour calculer le montant total d'une réservation en fonction du tarif par nuit.
     * 
     * Cette méthode calcule le montant total d'une réservation en fonction du tarif par nuit de la propriété
     * et de la période de réservation spécifiée.
     * 
     * @param int $idProprio L'identifiant de la propriété pour laquelle calculer le montant de la réservation.
     * @param int $idReservation L'identifiant de la réservation.
     * @param string $dateDebut La date de début de la réservation au format Y-m-d.
     * @param string $dateFin La date de fin de la réservation au format Y-m-d.
     * @return float|null Le montant total de la réservation en cas de succès, sinon null.
     */
    public function getMontantReservationByNuit($idProprio, $idReservation, $dateDebut, $dateFin) {
        
        try{

            include ('manager/ProprieteManager.php');
            $db = new Database();
            $reservationManager = new ReservationManager($db);
    
            // Récupérer le montant de la propriété
            $proprieteManager = new ProprieteManager($db);
            $tarifParNuit = $proprieteManager->getMontantProprieteById($idProprio);
    
            if ($tarifParNuit !== null) {
                // Calculer le nombre de jours de réservation
                $dateDebutObj = new DateTime($dateDebut);
                $dateFinObj = new DateTime($dateFin);
                $difference = $dateFinObj->diff($dateDebutObj);
                $nombreJours = $difference->days;
    
                // Calculer le montant total
                $montantTotal = $nombreJours * $tarifParNuit;
    
                return $montantTotal;
            } else {
                echo "le tarif n'existe pas";
            }
        } catch (PDOException $e) {
            // Gérer les erreurs PDO si nécessaire
            echo "Erreur lors de la récupération du montant de la propriété : " . $e->getMessage();
            return null;
        }

    }


    /**
     * Méthode pour modifier une réservation avec un nouveau montant.
     * 
     * Cette méthode permet de mettre à jour les détails d'une réservation spécifique, y compris son montant, dans la base de données.
     * 
     * @param int $id_reservation L'identifiant de la réservation à modifier.
     * @param int $idUtilisateur L'identifiant de l'utilisateur associé à la réservation.
     * @param int $idProprio L'identifiant du propriétaire de la propriété réservée.
     * @param string $dateDebutTempo La nouvelle date de début de la réservation.
     * @param string $dateFinTempo La nouvelle date de fin de la réservation.
     * @param float $montant Le nouveau montant de la réservation.
     * @return bool Retourne true si la modification de la réservation est réussie, sinon false.
     */
    public function modifierReservationAvecMontant($id_reservation, $idUtilisateur, $idProprio, $dateDebutTempo, $dateFinTempo, $montant) {
        $sql = "UPDATE reservation 
                SET idUtilisateur = :idUtilisateur, 
                    idProprio = :idProprio, 
                    dateDebut = :dateDebutTempo, 
                    dateFin = :dateFinTempo, 
                    montant = :montant 
                WHERE idReservation = :idReservation";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idReservation', $id_reservation);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt->bindParam(':idProprio', $idProprio);
        $stmt->bindParam(':dateDebutTempo', $dateDebutTempo);
        $stmt->bindParam(':dateFinTempo', $dateFinTempo);
        $stmt->bindParam(':montant', $montant);
    
        $resultat = $stmt->execute();
        return $resultat; 
    }


    /**
     * Méthode pour supprimer une réservation.
     * 
     * Cette méthode permet de supprimer une réservation spécifique de la base de données.
     * 
     * @param int $id_reservation L'identifiant de la réservation à supprimer.
     * @return bool Retourne true si la suppression de la réservation est réussie, sinon false.
     */
    public function supprimerReservation($id_reservation) {
        try {
            $sql = "DELETE FROM reservation WHERE idReservation = :idReservation";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idReservation', $id_reservation);
            $resultat = $stmt->execute();

            if ($resultat) {
                return true;
            }else{
                return false;
            }

        } catch (PDOException $e) {
            echo "Erreur lors de la suppression de la réservation : " . $e->getMessage();
            return false;
        }
    }
    
}

?>

