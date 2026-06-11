<?php
// Démarrage de la session pour accéder aux données de l'utilisateur connecté
session_start();
// Inclusion du fichier de configuration contenant la connexion PDO à la base de données
require_once 'config.php';
// Contrôle d'accès : si l'utilisateur n'est pas connecté, redirection immédiate vers la page de connexion
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: connexion.php');
    exit();
}
// Récupération de l'identifiant unique de l'utilisateur stocké en session
$client_id = $_SESSION['user']['id'];
// Récupération des informations à jour du profil de l'utilisateur depuis la base de données
$sql_user = "SELECT nom, prenom, email, role FROM Utilisateur WHERE id_utilisateur = :id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['id' => $client_id]);
$current_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Sécurité : si le compte utilisateur n'existe plus dans la base de données, destruction de la session et redirection
if (!$current_user) {
    session_destroy();
    header('Location: connexion.php');
    exit();
}
// Requête SQL avec jointure interne (INNER JOIN) pour récupérer l'historique des réservations de l'utilisateur ainsi que les détails du véhicule associé
$sql_res = "SELECT r.id_reservation, r.date_debut, r.date_fin, r.statut_reservation, 
                   v.marque, v.modele, v.tarif_journalier
            FROM Reservation r
            INNER JOIN Voiture v ON r.id_voiture = v.id_voiture
            WHERE r.id_utilisateur = :id
            ORDER BY r.id_reservation DESC"; // Classement par ID décroissant (les réservations récentes en premier)

$stmt_res = $pdo->prepare($sql_res);
$stmt_res->execute(['id' => $client_id]);
$reservations = $stmt_res->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyCar - Mon Profil</title>
    <link rel="stylesheet" href="Style CSS/style_profil.css">
</head>
<body>

    <header>
        <nav>
            <a href="deconnexion.php">Déconnexion</a>
            <?php
            // Condition d'affichage : si l'utilisateur a le rôle d'administrateur, un lien vers le tableau de bord apparaît
            if($current_user['role'] == 'admin'){
                echo "<a href='admin/admin.php'>← Dasboard</a>";
            }
        ?>
        </nav>
    </header>

    <main>
        <h1>Mon Profil - EasyCar</h1>
        <p><a href="category.php">← Retour au catalogue des voitures</a></p>

        <section>
            <h2>Mes Informations Personnelles</h2>
            <ul>
                <li><strong>Nom :</strong> <?= $current_user['nom']; ?></li>
                <li><strong>Prénom :</strong> <?= $current_user['prenom']; ?></li>
                <li><strong>Adresse Email :</strong> <?= $current_user['email']; ?></li>
                <li><strong>Rôle :</strong> <?= $current_user['role']; ?></li>
            </ul>
        </section>

        <hr>

        <section>
            <h2>Mes Réservations</h2>

            <?php 
            // Si le tableau des réservations est vide, affichage d'un message indicatif
            if (empty($reservations)){ ?>
                <p>Vous n'avez effectué aucune réservation pour le moment. <a href="category.php">Cliquez ici pour réserver une voiture</a>.</p>
            <?php }else{ ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Réservation</th>
                            <th>Véhicule</th>
                            <th>Date de Début</th>
                            <th>Date de Fin</th>
                            <th>Durée</th>
                            <th>Prix Total Estimé</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Parcours du tableau des réservations récupérées
                        foreach ($reservations as $res): ?>
                            <?php 
                                // Calcul de l'intervalle de temps entre la date de début et la date de fin
                                $start = new DateTime($res['date_debut']);
                                $end = new DateTime($res['date_fin']);
                                $diff = $start->diff($end);
                                $days = $diff->days;
                                // Règle métier : si la réservation est sur le même jour (0 jour de différence), on compte au moins 1 jour de location
                                if ($days == 0) $days = 1; 
                                // Calcul du coût financier de la réservation (Nombre de jours * Tarif par jour)
                                $total_price = $days * $res['tarif_journalier'];
                            ?>
                            <tr>
                                <td>#<?= $res['id_reservation']; ?></td>
                                <td><?= $res['marque'] . " " . $res['modele']; ?></td>
                                <td><?= $res['date_debut']; ?></td>
                                <td><?= $res['date_fin']; ?></td>
                                <td><?= $days; ?> Jour(s)</td>
                                <td><strong><?= $total_price, 2; ?> DH</strong></td>
                                <td>
                                    <strong><?= $res['statut_reservation']; ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php }; ?>
        </section>
    </main>

    <footer>
    <div class="footer-grid">

      <div class="footer-brand">
        <h2><span>Easy</span>Car</h2>
        <p>La solution numérique centralisée pour<br>gérer et réserver votre véhicule à<br>Tanger en toute sécurité et simplicité.</p>
      </div>

      <div class="footer-col">
        <h3>Liens Rapides</h3>
        <ul>
          <li><a href="accueil.php">Accueil</a></li>
          <li><a href="category.php">Nos Voitures</a></li>
          <li><a href="connexion.php">Connexion</a></li>
        </ul>
      </div>

      <div class="footer-col footer-contact">
        <h3>Contact d'agence</h3>
        <p>Avenue Mohamed V,<br>Tanger, Maroc</p>
        <p>+212 600-000-000</p>
        <p>contact@easycar.ma</p>
        <div class="social-icons">
          <div class="social-icon fb">f</div>
          <div class="social-icon wa">W</div>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      © 2026 EasyCar. Tous droits réservés.
    </div>
  </footer>

</body>
</html>