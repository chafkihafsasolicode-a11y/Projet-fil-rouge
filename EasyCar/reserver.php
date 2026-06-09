<?php
session_start();
require_once 'config.php';

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: category.php'); 
    exit();
}
if(isset($_GET['id'])){
  $id = $_GET['id'];
  $sql = "SELECT * FROM voiture WHERE id_voiture = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      'id' => $id
  ]);
$voiture = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user'])) {
        header('Location: connexion.php');
        exit();
    }

    $voiture_id   = $_POST['voiture_id'];
    $client_id    = $_SESSION['user']['id'];
    $lieu         = $_POST['lieu'];
    $date_depart  = $_POST['date_depart'];
    $heure_depart = $_POST['heure_depart'];
    $date_retour  = $_POST['date_retour'];
    $heure_retour = $_POST['heure_retour'];
    if(empty($date_depart) || empty($date_retour) || $date_retour < $date_depart){
      echo "Entrer un date valid";
    }else{
      $sql = "INSERT INTO Reservation (id_utilisateur, id_voiture, date_debut, date_fin) 
              VALUES (:id_utilisateur, :id_voiture, :date_debut, :date_fin)";

      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          'id_utilisateur' => $client_id,
          'id_voiture'     => $voiture_id,
          'date_debut'     => $date_depart,
          'date_fin'       => $date_retour
      ]);
        echo "<h1>Réservation effectuée avec succès !</h1>";
        echo "<a href='category.php'>Retour aux voitures</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style_rsv.css">
</head>
<body>
    <header>
        <img src="Logo.png" alt="EasyCar">
        <nav>
            <a href="">Accueil</a>
            <a href="">Nos voitures</a>
            <a href="">Connexion</a>
            <button>Reserver  Maintenant</button>
        </nav>
    </header>
    <main>
        <a href="category.php" class="btn">Retour</a>
            <div class="card">
                <img src="<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>" height="340">
                <h3><?php echo ($voiture['marque']); ?></h3>
                <p><?php echo ($voiture['tarif_journalier']); ?></p>
                <p><?php echo ($voiture['boite_vitesse']); ?></p>
                <p><?php echo ($voiture['type_carburant']); ?></p>
                <p><img src="date.png" alt="" width="14"> <?php echo ($voiture['annee']); ?></p>
                <a href="reserver.php?id=<?= $voiture['id_voiture']; ?>">Détails & Réserver</a>
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
          <li><a href="#">Accueil</a></li>
          <li><a href="#">Nos Voitures</a></li>
          <li><a href="#">Connexion</a></li>
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