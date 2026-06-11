<?php
// Démarrage de la session pour conserver l'état de connexion de l'administrateur
session_start();
// Inclusion du fichier de configuration pour se connecter à la base de données via l'objet PDO
require_once '../config.php';


// Vérification de sécurité : s'assurer que l'ID de la voiture à modifier est bien présent et non vide dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: admin.php');
    exit();
}

$id_voiture = $_GET['id'];

// 1. Récupération des données actuelles du véhicule pour pré-remplir automatiquement les champs du formulaire
$sql = "SELECT * FROM Voiture WHERE id_voiture = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_voiture]);
$voiture = $stmt->fetch(PDO::FETCH_ASSOC);
// Si l'ID ne correspond à aucune voiture en base de données, retour immédiat au tableau de bord
if (!$voiture) {
    header('Location: admin.php');
    exit();
}

// 2. Traitement de la mise à jour des données lors de la soumission du formulaire (Méthode POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modifier_voiture'])) {
    // Récupération des variables envoyées par l'utilisateur
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $carburant = $_POST['type_carburant'];
    $boite = $_POST['boite_vitesse'];
    $type = $_POST['type'];
    $tarif = $_POST['tarif_journalier'];
    $annee = $_POST['annee'];
    $image = $_POST['image'];
// Préparation de la requête SQL UPDATE pour mettre à jour l'enregistrement correspondant
    $sql_update = "UPDATE Voiture 
                   SET marque = :marque, modele = :modele, type_carburant = :carburant, 
                       boite_vitesse = :boite, categorie = :categorie,  tarif_journalier = :tarif, annee = :annee, image = :image 
                   WHERE id_voiture = :id";
                   
    $stmt_update = $pdo->prepare($sql_update);
    // Exécution sécurisée avec liaison des paramètres pour éviter les injections SQL
    $stmt_update->execute([
        'marque' => $marque, 'modele' => $modele, 'carburant' => $carburant,
        'boite' => $boite, 'categorie' =>$type, 'tarif' => $tarif, 'annee' => $annee, 'image' => $image,
        'id' => $id_voiture
    ]);

// Redirection automatique vers l'espace admin.php immédiatement après l'enregistrement    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EasyCar - Modifier une Voiture</title>
    <link rel="stylesheet" href="../Style CSS/modifier.css">
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">

    <h1>Modifier les détails de la voiture : <?= htmlspecialchars($voiture['marque'] . ' ' . $voiture['modele']); ?></h1>
    
    <div style="margin-bottom: 20px;">
        <a href="admin.php" style="display: inline-block; padding: 10px 15px; background-color: #718096; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
            ← Retour au Dashboard Admin
        </a>
    </div>

    <hr>

    <form method="POST" action="modifier.php?id=<?= $id_voiture; ?>">
        <label>Marque :</label> <br>
        <input type="text" name="marque" value="<?= htmlspecialchars($voiture['marque']); ?>" required><br><br>

        <label>Modèle :</label> <br>
        <input type="text" name="modele" value="<?= htmlspecialchars($voiture['modele']); ?>" required><br><br>

        <label>Carburant :</label> <br>
        <select name="type_carburant">
            <option value="Diesel" <?= $voiture['type_carburant'] == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
            <option value="Essence" <?= $voiture['type_carburant'] == 'Essence' ? 'selected' : ''; ?>>Essence</option>
            <option value="Électrique" <?= $voiture['type_carburant'] == 'Électrique' ? 'selected' : ''; ?>>Électrique</option>
        </select><br><br>

        <label>Boîte de vitesse :</label> <br>
        <select name="boite_vitesse">
            <option value="Manuelle" <?= $voiture['boite_vitesse'] == 'Manuelle' ? 'selected' : ''; ?>>Manuelle</option>
            <option value="Automatique" <?= $voiture['boite_vitesse'] == 'Automatique' ? 'selected' : ''; ?>>Automatique</option>
        </select><br><br>
        <label>Type de voiture :</label> <br>
        <select name="type" id="type">
                <option value="">-- Selectionner --</option>
                <option value="Citadines">Citadines</option>
                <option value="Berlines">Berlines</option>
                <option value="SUV">SUV</option>
                <option value="LUX">LUX</option>
            </select><br><br>
        <label>Tarif Journalier (DH) :</label> <br>
        <input type="number" step="0.01" name="tarif_journalier" value="<?= htmlspecialchars($voiture['tarif_journalier']); ?>" required><br><br>

        <label>Année :</label> <br>
        <input type="number" name="annee" value="<?= htmlspecialchars($voiture['annee']); ?>" required><br><br>

        <label>Lien/Nom de l'image :</label> <br>
        <input type="text" name="image" value="<?= htmlspecialchars($voiture['image']); ?>"><br><br>
        
        <button type="submit" name="modifier_voiture" style="padding: 10px 20px; background-color: #E53E3E; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            Enregistrer les modifications
        </button>
    </form>
</body>
</html>