<?php
// Démarrage de la session pour gérer les messages d'état (succès/erreur) et vérifier l'authentification
session_start();
// Inclusion du fichier de configuration pour la connexion PDO à la base de données
require_once 'config.php';
// Vérification de sécurité : si le paramètre 'id' est absent ou vide dans l'URL, redirection vers le catalogue
if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: category.php'); 
    exit();
}
// Traitement initial du paramètre 'id' reçu par l'URL
if(isset($_GET['id'])){
  $id = $_GET['id'];
  // Récupération des informations de la voiture sélectionnée
  $sql = "SELECT * FROM voiture WHERE id_voiture = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $id]);
  $voiture = $stmt->fetch();

// ← AJOUT : Vérification si la voiture fait déjà l'objet d'une réservation active (non annulée et non expirée)
  $sql_check = "SELECT COUNT(*) FROM Reservation 
                WHERE id_voiture = :id 
                AND statut_reservation != 'Annulée'
                AND date_fin >= CURDATE()";
  $stmt_check = $pdo->prepare($sql_check);
  $stmt_check->execute(['id' => $id]);
  // Contient vrai (true) si le compteur est supérieur à 0
  $deja_reserve = $stmt_check->fetchColumn() > 0;
}
// Deuxième bloc d'origine (identique) effectuant la même récupération de la voiture
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $sql = "SELECT * FROM voiture WHERE id_voiture = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      'id' => $id
  ]);
$voiture = $stmt->fetch();
}
// Traitement de la soumission du formulaire de réservation (Méthode POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Si l'utilisateur n'est pas connecté, redirection immédiate vers la page de connexion
    if (!isset($_SESSION['user'])) {
        header('Location: connexion.php');
        exit();
    }
    // Récupération des données envoyées par le formulaire
    $voiture_id   = $_POST['voiture_id'];
    $client_id    = $_SESSION['user']['id'];
    $lieu         = $_POST['lieu'];
    $date_depart  = $_POST['date_depart'];
    $heure_depart = $_POST['heure_depart'];
    $date_retour  = $_POST['date_retour'];
    $heure_retour = $_POST['heure_retour'];
    // Validation des dates : elles ne doivent pas être vides et la date de retour doit être postérieure ou égale à celle de départ
    if(empty($date_depart) || empty($date_retour) || $date_retour < $date_depart){
      echo "Entrer un date valid";
  } else {
  
      // ← AJOUT : Double vérification de sécurité au moment de la soumission pour éviter les conflits
      $sql_check2 = "SELECT COUNT(*) FROM Reservation 
                     WHERE id_voiture = :id 
                     AND statut_reservation != 'Annulée'
                     AND date_fin >= CURDATE()";
      $stmt_check2 = $pdo->prepare($sql_check2);
      $stmt_check2->execute(['id' => $voiture_id]);
      $already_taken = $stmt_check2->fetchColumn() > 0;
  // Si la voiture a été réservée entre-temps par un autre utilisateur
      if ($already_taken) {
          $_SESSION['error'] = "Cette voiture est déjà réservée.";
          header("Location: reserver.php?id=" . $voiture_id);
          exit();
      }
  // Insertion de la nouvelle réservation dans la table 'Reservation'
      $sql = "INSERT INTO Reservation (date_debut, date_fin, id_utilisateur, id_voiture) 
              VALUES (:date_debut, :date_fin, :id_utilisateur, :id_voiture)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          'date_debut'     => $date_depart,
          'date_fin'       => $date_retour,
          'id_utilisateur' => $client_id,
          'id_voiture'     => $voiture_id
      ]);
      // Stockage du message de succès en session et rechargement de la page
      $_SESSION['success'] = "Votre réservation a été confirmée avec succès !";
      header("Location: reserver.php?id=" . $voiture_id);
      exit();
  }
  
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Style CSS/style_rsv.css">
</head>
<body>
    <header>
        <img src="assets/Logo.png" alt="EasyCar">
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="category.php">Nos voitures</a>
            <a href="connexion.php">Connexion</a>
            <?php 
            // Affichage dynamique selon l'état de connexion de l'utilisateur
                if(isset($_SESSION['user'])){
                    $user = $_SESSION['user'];
                    echo "<a href='profil.php' class='reserve'>" .$user['name'] . "</a>";
                }else{
                    echo "<a href='category.php' class='reserve'>Reserver  Maintenant</a>";
                }
            ?>
        </nav>
    </header>
    <main>
      <?php if (isset($_SESSION['success'])): ?>
          <div class="alert-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 13.01 9 10.01"/>
              </svg>
              <?= $_SESSION['success'] ?>

          </div>
          <?php unset($_SESSION['success']); ?>
      <?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?>
      <div class="alert-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
        <a href="category.php" class="btn">Retour</a>
            <div class="card">
                <img src="assets/<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>" height="340">
                <h3><?php echo ($voiture['marque']); ?> <?php echo ($voiture['modele']); ?></h3>
                <p><?php echo ($voiture['tarif_journalier']); ?></p>
                <div class="card-badges">
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22V8l9-6 9 6v14"/><path d="M10 14v8"/><path d="M3 12h18"/></svg>
                        <?php echo ($voiture['type_carburant']); ?>
                    </span>
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        <?php echo ($voiture['boite_vitesse']); ?>
                    </span>
                    <span class="card-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <?php echo ($voiture['annee']); ?>
                    </span>
                </div>
            </div>
            <section class="reservation-form">
              <h2>Details</h2>
              <form method="POST">
                  <input type="hidden" name="voiture_id" value="<?= $voiture['id_voiture']; ?>">

                  <label>Lieu de départ</label>
                  <select name="lieu">
                      <option value="Centre Ville">Centre Ville</option>
                  </select>

                  <div class="date-time">
                      <label>Date Départ</label>
                      <input type="date" name="date_depart" required>
                      <label>Heure</label>
                      <input type="time" name="heure_depart" required>
                  </div>

                  <div class="date-time">
                      <label>Date Retour</label>
                      <input type="date" name="date_retour" required>
                      <label>Heure</label>
                      <input type="time" name="heure_retour" required>
                  </div>

                  <button type="submit" class="btn-reserver">Reserver</button>
              </form>
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