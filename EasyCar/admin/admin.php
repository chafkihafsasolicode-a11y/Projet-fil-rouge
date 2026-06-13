<?php
// Démarrage de la session pour maintenir les variables d'état de l'administrateur connecté
session_start();
// Inclusion du fichier de configuration pour l'initialisation de la connexion PDO
require_once '../config.php';
// Initialisation de la variable globale contenant les messages de notification (succès/échec)
$msg = "";

// 1. Traitement POST : Mise à jour du statut d'une réservation spécifique
if (isset($_POST['action_reservation'])) {
    $id_res = $_POST['id_reservation'];
    $nouveau_statut = $_POST['statut'];
    // Préparation de la requête pour modifier l'état (Confirmée, En attente, Annulée)
    $stmt = $pdo->prepare("UPDATE Reservation SET statut_reservation = :statut WHERE id_reservation = :id");
    $stmt->execute(['statut' => $nouveau_statut, 'id' => $id_res]);
    $msg = "Statut de la réservation #$id_res mis à jour avec succès !";
}

// 2. Traitement POST : Insertion d'un nouveau véhicule dans la base de données
if (isset($_POST['ajouter_voiture'])) {
    // Requête préparée pour sécuriser l'insertion des données du formulaire modale
    $stmt = $pdo->prepare("INSERT INTO Voiture (marque, modele, type_carburant, boite_vitesse, tarif_journalier, annee, image) 
                           VALUES (:marque, :modele, :carburant, :boite, :tarif, :annee, :image)");
    $stmt->execute([
        'marque'    => $_POST['marque'],
        'modele'    => $_POST['modele'],
        'carburant' => $_POST['type_carburant'],
        'boite'     => $_POST['boite_vitesse'],
        'tarif'     => $_POST['tarif_journalier'],
        'annee'     => $_POST['annee'],
        'image'     => $_POST['image']
    ]);
    $msg = "Voiture ajoutée avec succès !";
}

// 3. Traitement GET : Suppression d'un véhicule depuis l'aperçu de la flotte
if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM Voiture WHERE id_voiture = :id");
    $stmt->execute(['id' => $_GET['supprimer']]);
    // Redirection après suppression pour réinitialiser les paramètres d'URL et éviter la double exécution
    header("Location: admin.php?deleted=1");
    exit();
}

// Interception du paramètre de redirection pour afficher le message de succès de suppression
if (isset($_GET['deleted'])) {
    $msg = "Voiture supprimée avec succès !";
}

// Interception du paramètre de redirection pour afficher le message de succès de suppression
$reservations = $pdo->query(
    "SELECT r.*, u.nom, u.prenom, v.marque, v.modele 
     FROM Reservation r
     INNER JOIN Utilisateur u ON r.id_utilisateur = u.id_utilisateur
     INNER JOIN Voiture v ON r.id_voiture = v.id_voiture
     ORDER BY r.id_reservation DESC"// Du plus récent au plus ancien
)->fetchAll(PDO::FETCH_ASSOC);

// 5. Requête SQL : Récupération complète de la flotte de véhicules
$voitures = $pdo->query("SELECT * FROM Voiture ORDER BY id_voiture DESC")->fetchAll(PDO::FETCH_ASSOC);

// 6. Calcul des indicateurs clés (KPI) de performance du Dashboard
$total_reservations = count($reservations);
// Filtrage en ligne (fonction anonyme) pour isoler les réservations confirmées
$confirmees = count(array_filter($reservations, fn($r) => $r['statut_reservation'] === 'Confirmée'));
$total_voitures = count($voitures);
// Taux d'occupation / Santé de la flotte calculé dynamiquement (évite la division par zéro via max())
$sante_flotte = $total_voitures > 0 ? round(($confirmees / max($total_voitures, 1)) * 100) : 94;
// Calcul du Chiffre d'affaires cumulé réel basé sur la différence de jours (DATEDIFF) des réservations confirmées
$ca_total = $pdo->query("SELECT COALESCE(SUM(v.tarif_journalier * DATEDIFF(r.date_fin, r.date_debut)), 0) FROM Reservation r JOIN Voiture v ON r.id_voiture = v.id_voiture WHERE r.statut_reservation = 'Confirmée'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyCar – Dashboard Admin</title>
    <link rel="stylesheet" href="../Style CSS/style_admin.css">
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span>EasyCar Admin</span>
        <small>Espace administrateur</small>
    </div>
    <nav>
        <a href="../accueil.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            Accueil
        </a>
        <a href="admin.php" class="active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="voitures.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="13" rx="2"/><path d="M16 17v2M8 17v2M2 12h20"/></svg>
            Voitures
        </a>
        <a href="reservations.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            Réservations
        </a>
    </nav>
    <div class="sidebar-bottom">
        <button class="btn-add-car" onclick="openModal()">+ Ajouter une voiture</button>
        <a href="deconnexion.php" class="logout">Se déconnecter</a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<main class="main">

    <h1 class="page-title">Tableau de Bord Admin</h1>

    <?php if (!empty($msg)): ?>
        <div class="alert-success">✓ <?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- STAT CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">
                Chiffre d'affaires
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c0c5d0" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-value"><?= number_format($ca_total, 0, ',', ' ') ?> <span>DH</span></div>
            <div class="stat-trend">↑ +12.5% vs semaine dernière</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                Locations actives
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c0c5d0" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="stat-value"><?= $confirmees ?></div>
            <div class="stat-sub">réservations confirmées ce mois-ci</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                Nouveaux clients
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c0c5d0" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="stat-value"><?= $total_reservations ?></div>
            <div class="stat-sub stat-trend down">↓ −5.8% ce mois-ci</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">
                Santé de la flotte
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c0c5d0" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="stat-value"><?= min($sante_flotte, 100) ?>%</div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: <?= min($sante_flotte, 100) ?>%"></div>
            </div>
        </div>
    </div>

    <!-- PERFORMANCE + ACTIVITÉ -->
    <div class="two-col">
        <div class="panel">
            <div class="panel-header">
                <h2>Performance</h2>
                <select><option>Dernier semaine</option><option>Ce mois</option></select>
            </div>
            <div class="bar-chart" id="barChart">
                <?php
                $days  = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
                $heights = [35, 45, 55, 42, 100, 60, 30];// Hauteurs statiques de démonstration
                foreach ($days as $i => $day):
                    $h = $heights[$i];
                    // Mise en évidence graphique pour la journée du Vendredi
                    $highlight = $day === 'Ven' ? 'highlight' : '';
                ?>
                <div class="bar-wrap">
                    <div class="bar <?= $highlight ?>" style="height: <?= $h ?>%"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="bar-chart-labels">
                <?php foreach ($days as $d): ?>
                <span><?= $d ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2>Activité Récente</h2>
            </div>
            <ul class="activity-list">
                <?php
                // Extraction des 4 premiers éléments du tableau global de réservations
                $recent = array_slice($reservations, 0, 4);
                $icons  = ['green', 'blue', 'orange', 'red'];
                $svgs   = [
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>',
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
                ];
                foreach ($recent as $i => $r):
                    $ic = $icons[$i % 4];
                ?>
                <li class="activity-item">
                    <div class="activity-icon <?= $ic ?>"><?= $svgs[$i % 4] ?></div>
                    <div class="activity-body">
                        <p><?= htmlspecialchars($r['statut_reservation']) ?> — <?= htmlspecialchars($r['marque'] . ' ' . $r['modele']) ?></p>
                        <small><?= htmlspecialchars($r['nom'] . ' ' . $r['prenom']) ?> · <?= $r['date_debut'] ?></small>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?>
                <li style="font-size:12px; color:#a0a5b0;">Aucune activité récente.</li>
                <?php endif; ?>
            </ul>
            <div class="panel-footer">
                <a href="#reservations">Voir tout l'historique →</a>
            </div>
        </div>
    </div>

    <!-- APERÇU FLOTTE -->
    <div class="panel" id="flotte">
        <div class="section-header">
            <h2>Aperçu de la Flotte</h2>
            <a href="voitures.php">Gérer tout le catalogue →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Statut</th>
                    <th>Tarif / jour</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($voitures, 0, 5) as $voit): ?>
                <tr>
                    <td>
                        <div class="car-cell">
                            <img src="../assets/<?= htmlspecialchars($voit['image']) ?>" alt="<?= htmlspecialchars($voit['marque']) ?>">
                            <div>
                                <div class="car-name"><?= htmlspecialchars($voit['marque'] . ' ' . $voit['modele']) ?></div>
                                <div class="car-sub"><?= htmlspecialchars($voit['type_carburant']) ?> · <?= htmlspecialchars($voit['boite_vitesse']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="status-badge status-disponible">Disponible</span></td>
                    <td class="tarif-cell"><?= number_format($voit['tarif_journalier'], 0) ?> DH</td>
                    <td>
                        <div class="actions-cell">
                            <a href="modifier.php?id=<?= $voit['id_voiture'] ?>" class="btn-modifier">Modifier</a>
                            <a href="admin.php?supprimer=<?= $voit['id_voiture'] ?>" 
                               onclick="return confirm('Supprimer cette voiture ?')" 
                               class="btn-supprimer">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- RÉSERVATIONS -->
    <div class="panel" id="reservations" style="margin-top: 24px;">
        <div class="section-header">
            <h2>Gestion des Réservations</h2>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Véhicule</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                <tr>
                    <td style="color:#a0a5b0; font-size:12px;">#<?= $res['id_reservation'] ?></td>
                    <td><?= htmlspecialchars($res['nom'] . ' ' . $res['prenom']) ?></td>
                    <td><?= htmlspecialchars($res['marque'] . ' ' . $res['modele']) ?></td>
                    <td style="font-size:12px;"><?= $res['date_debut'] ?></td>
                    <td style="font-size:12px;"><?= $res['date_fin'] ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $res['statut_reservation'])) ?>">
                            <?= htmlspecialchars($res['statut_reservation']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap: 6px; align-items: center;">
                            <input type="hidden" name="id_reservation" value="<?= $res['id_reservation'] ?>">
                            <select name="statut" class="status-select">
                                <option value="Confirmée"  <?= $res['statut_reservation'] == 'Confirmée'  ? 'selected' : '' ?>>Confirmée</option>
                                <option value="Annulée"    <?= $res['statut_reservation'] == 'Annulée'    ? 'selected' : '' ?>>Annulée</option>
                                <option value="En attente" <?= $res['statut_reservation'] == 'En attente' ? 'selected' : '' ?>>En attente</option>
                            </select>
                            <button type="submit" name="action_reservation" class="btn-modifier">OK</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($reservations)): ?>
                <tr><td colspan="7" style="text-align:center; color:#a0a5b0; padding:24px;">Aucune réservation pour le moment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        © <?= date('Y') ?> EasyCar. Tous droits réservés.
    </footer>

</main>

<!-- ══ MODAL AJOUTER VOITURE ══ -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <h3>Ajouter une nouvelle voiture</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Marque</label>
                    <input type="text" name="marque" placeholder="ex: Renault" required>
                </div>
                <div class="form-group">
                    <label>Modèle</label>
                    <input type="text" name="modele" placeholder="ex: Clio" required>
                </div>
                <div class="form-group">
                    <label>Carburant</label>
                    <select name="type_carburant">
                        <option value="Diesel">Diesel</option>
                        <option value="Essence">Essence</option>
                        <option value="Électrique">Électrique</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Boîte de vitesse</label>
                    <select name="boite_vitesse">
                        <option value="Manuelle">Manuelle</option>
                        <option value="Automatique">Automatique</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tarif journalier (DH)</label>
                    <input type="number" step="0.01" name="tarif_journalier" placeholder="ex: 350" required>
                </div>
                <div class="form-group">
                    <label>Année</label>
                    <input type="number" name="annee" placeholder="ex: 2022" required>
                </div>
                <div class="form-group full">
                    <label>Chemin / lien de l'image</label>
                    <input type="text" name="image" value="asset/default.png">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Annuler</button>
                <button type="submit" name="ajouter_voiture" class="btn-save">Ajouter la voiture</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fonctions d'ouverture et de fermeture par manipulation de la classe CSS 'open'
function openModal()  { document.getElementById('modalOverlay').classList.add('open'); }
function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); }
// Événement permettant de fermer la modale si l'utilisateur clique en dehors de la boîte centrale
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
    const performanceData = {
    'Dernier semaine': [35, 45, 55, 42, 100, 60, 30],
    'Ce mois':         [60, 70, 50, 80, 90, 40, 55]
};

function renderChart(dataset) {
    const bars = document.querySelectorAll('#barChart .bar');
    bars.forEach((bar, i) => {
        bar.style.height = dataset[i] + '%';
    });
}


document.addEventListener('DOMContentLoaded', () => {
    const select = document.querySelector('.panel select');
    select.addEventListener('change', function () {
        const data = performanceData[this.value];
        if (data) renderChart(data);
    });
});
</script>

</body>
</html>
