<?php
// Démarrage de la session pour pouvoir suivre l'utilisateur connecté
session_start();
// Inclusion du fichier de configuration pour la connexion à la base de données (PDO)
require_once 'config.php';
// Préparation et exécution de la requête pour récupérer au maximum 3 voitures disponibles
$stmt = $pdo->query("SELECT * FROM voiture WHERE disponibilite = 'disponible' LIMIT 3");
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Style CSS/style.css">
</head>
<body>
    <header>
        <div class="navbar">
            <img src="assets/Logo.png" alt="EasyCar">
            <nav>
                <a href="accueil.php">Accueil</a>
                <a href="category.php">Nos voitures</a>
                <a href="connexion.php">Connexion</a>
                <?php
                // Vérification si un utilisateur est connecté en session
                    if(isset($_SESSION['user'])){
                        $user = $_SESSION['user'];
                        // Affichage du nom de l'utilisateur avec un lien vers son profil
                        echo "<a href='profil.php' class='reserve'>" .$user['name'] . "</a>";
                    }else{
                      // Bouton d'action par défaut si aucun utilisateur n'est connecté
                        echo "<a href='category.php' class='reserve'>Reserver  Maintenant</a>";
                    }
                ?>
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
                <option value="">-- Selectionner --</option>
                <option value="Citadines">Citadines</option>
                <option value="Berlines">Berlines</option>
                <option value="SUV">SUV</option>
                <option value="LUX">LUX</option>
            </select>
            <label for="carburant">Carburant</label>
            <select name="carburant" id="carburant">
                <option value="">-- Selectionner --</option>
                <option value="Essence">Essence</option>
                <option value="Diesel">Diesel</option>
            </select>
            <label for="boite_vitesse">Boite de vitesse</label>
            <select name="boite_vitesse" id="boite_vitesse">
                <option value="">-- Selectionner --</option>
                <option value="Automatique">Automatique</option>
                <option value="Manuelle">Manuelle</option>
            </select>
            <button>Rechercher</button>
        </form>
            <p>Nos Vehicules Populaires</p>
            <div class="cards">
              <?php if($voitures){ 
                // Boucle pour afficher chaque voiture sous forme de carte HTML
                foreach($voitures as $voiture){ ?>
                <div class="card">
                    <img src="assets/<?php echo ($voiture['image']); ?>" alt="<?php echo ($voiture['modele']); ?>" height="340">
                    <h3><?php echo ($voiture['marque']); ?> <?php echo ($voiture['modele']); ?></h3>
                    <p><?php echo ($voiture['tarif_journalier']); ?>DH/Jour</p>
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
                    <a href="reserver.php?id=<?= $voiture['id_voiture']; ?>">Détails & Réserver</a>
                </div>
              <?php }}?>
            </div>
            
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