<?php
require_once 'config.php';

$sql = "SELECT * FROM voiture WHERE disponibilite = 'disponible'";
$params = [];

if (!empty($_GET['type'])) {
    $sql .= " AND marque = :type";
    $params['type'] = $_GET['type'];
}
if (!empty($_GET['carburant'])) {
    $sql .= " AND type_carburant = :carburant";
    $params['carburant'] = $_GET['carburant'];
}
if (!empty($_GET['boite_vitesse'])) {
    $sql .= " AND boite_vitesse = :boite_vitesse";
    $params['boite_vitesse'] = $_GET['boite_vitesse'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style_cat.css">
</head>
<body>
    <header>
        <img src="Logo.png" alt="EasyCar">
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="category.php">Nos voitures</a>
            <a href="connexion.php">Connexion</a>
            <button>Reserver  Maintenant</button>
        </nav>
    </header>
    <main>
        <div>
            <h1>Nos Voitures</h1>
            <p>Découvrez notre voitures disponibles à la location</p>
        </div>

        <form action="category.php" method="get" class="filter">
            <label for="type">Type de voiture</label>
            <select name="type" id="type">
                <option value="">--</option>
                <option value="Citadines">Citadines</option>
                <option value="Berlines">Berlines</option>
                <option value="SUV">SUV</option>
                <option value="LUX">LUX</option>
            </select>
            <label for="carburant">Carburant</label>
            <select name="carburant" id="carburant">
                <option value="">--</option>
                <option value="Essence">Essence</option>
                <option value="Diesel">Diesel</option>
            </select>
            <label for="boite_vitesse">Boite de vitesse</label>
            <select name="boite_vitesse" id="boite_vitesse">
                <option value="">--</option>
                <option value="Automatique">Automatique</option>
                <option value="Manuelle">Manuelle</option>
            </select>
            <button>Rechercher</button>
        </form>

        <div class="cards">
          <?php foreach($voitures as $voiture){?>
            <div class="card">
                <img src="<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>" height="340">
                <h3><?php echo ($voiture['marque']); ?></h3>
                <p><?php echo ($voiture['tarif_journalier']); ?></p>
                <p><?php echo ($voiture['boite_vitesse']); ?></p>
                <p><?php echo ($voiture['type_carburant']); ?></p>
                <p><img src="date.png" alt="" width="14"> <?php echo ($voiture['annee']); ?></p>
                <a href="reserver.php?id=<?= $voiture['id_voiture']; ?>">Détails & Réserver</a>
            </div>
            <?php }?>
        </div>
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