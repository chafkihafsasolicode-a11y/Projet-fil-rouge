<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT * FROM voiture WHERE disponibilite = 'disponible' LIMIT 3");
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="navbar">
            <img src="Logo.png" alt="EasyCar">
            <nav>
                <a href="accueil.php">Accueil</a>
                <a href="category.php">Nos voitures</a>
                <a href="connexion.php">Connexion</a>
                <button>Reserver  Maintenant</button>
            </nav> 
        </div>
        <div class="hero">
            <h1>Trouver la Voiture Ideale pour Vos Voyages</h1>
            <p>Louez une voiture en toute simplicite et au meilleur prix a Tanger</p>
        </div>
    </header>
    <main>
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
            <p>Nos Vehicules Populaires</p>
            <?php foreach($voitures as $voiture){ ?>
                <div class="card">
                    <img src="<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>" height="340">
                    <h3><?php echo ($voiture['marque']); ?></h3>
                    <p><?php echo ($voiture['tarif_journalier']); ?>/Jour</p>
                    <p><?php echo ($voiture['boite_vitesse']); ?></p>
                    <p><?php echo ($voiture['annee']); ?></p>
                    <p><img src="date.png" alt="" width="14"> 2025</p>
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