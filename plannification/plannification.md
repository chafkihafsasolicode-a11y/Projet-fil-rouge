# Planification de projet : Location de voiture
### Phase 1 : Analyse (1 semaine)

Analyse du cahier de charges

Compréhension du besoin (client / admin)

Définition des fonctionnalités :

Espace public, Espace client, Espace admin

Définition des pages :

Accueil, Catalogue, Détails voiture, Réservation, Login / Register, Dashboard admin

Création des maquettes (wireframes)

### Phase 2 : Conception & Base de données (1 semaine)

Réalisation du MCD / MLD

Création des tables :

utilisateurs, voitures, réservations, etc.

Configuration de la base de données dans MySQL

Relations entre les tables (users ↔ réservations ↔ voitures)

### Phase 3 : Développement Front-end (2 semaines)

Intégration HTML des pages

Styling avec CSS (UI moderne et responsive)

Interactivité avec JavaScript

Création de l’interface utilisateur :

Catalogue voitures, filtres, détails, formulaire de réservation

### Phase 4 : Développement Back-end (2 semaines)

Configuration de PHP et connexion à MySQL avec PDO

Système d’authentification (inscription / connexion)

Gestion des voitures (CRUD admin)

Gestion des réservations :

Création réservation

Vérification disponibilité

Calcul automatique du prix (jours × prix journalier)

Espace client :

Historique des réservations

Suivi des statuts (en attente, confirmée, terminée)

Espace admin :

Gestion des véhicules

Gestion des réservations

Statistiques (revenus, voitures les plus louées)

Sécurité (validation, protection SQL injection)