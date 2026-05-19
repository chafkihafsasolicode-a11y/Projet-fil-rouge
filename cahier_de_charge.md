# CAHIER DES CHARGES 

## PROJET FIL ROUGE : Application de Gestion de Location de Voiture "EASYCAR"

### 1. Contexte et Justification du Projet

Le secteur de la location de voitures au Maroc connaît une croissance importante, mais de nombreuses agences locales gèrent encore leurs flottes de manière traditionnelle ou via des outils bureautiques limités (tableurs Excel, registres papier). Cette gestion manuelle engendre des risques majeurs d'erreur de planification, notamment l'indisponibilité imprévue de véhicules ou des conflits de réservation (Overbooking) pour une même période.

De plus, l'expérience client souffre d'un manque de fluidité : l'absence de visibilité immédiate sur les caractéristiques techniques des véhicules disponibles ralentit le processus de décision. Le projet EasyCar est né pour répondre à ces problématiques en fournissant une solution numérique centralisée, moderne et simple d'utilisation, automatisant la relation entre l'administration de l'agence et les clients.

---

### 2. Objectifs Globaux

L'application web EasyCar poursuit plusieurs objectifs clés pour moderniser le processus de location :

* Centralisation des données : Suivre l'état de la flotte en temps réel pour l'administrateur (véhicules disponibles, loués ou en maintenance).
* Automatisation du calcul de tarification : Générer automatiquement le coût total de la location en multipliant le tarif journalier du véhicule choisi par le nombre exact de jours réservés.
* Amélioration de l'expérience utilisateur : Permettre un ciblage ultra-rapide des véhicules grâce à des filtres technologiques fluides exécutés côté client.
* Sécurisation des flux : Assurer le chiffrement des données d'accès et la persistance des rôles via un système de session rigoureux.

---

### 3. Description Fonctionnelle des Modules

#### 3.1 Système d'Authentification et Sessions Sécurisées

L'application implémente deux niveaux d'accès distincts contrôlés par des scripts PHP de vérification de statut :

* Inscription et Connexion : Les utilisateurs s'enregistrent via un formulaire sécurisé. Les mots de passe sont hachés en base de données.
* Persistance par Sessions (PHP $_SESSION) : Gestion des droits d'accès. Un client ne peut pas accéder aux pages d'administration, et un utilisateur non connecté est automatiquement redirigé vers la page de connexion.

#### 3.2 Catalogue Interactif et Moteur de Recherche Dynamique

La Landing Page intègre un catalogue dynamique développé pour offrir une expérience fluide, similaire à la maquette graphique validée :

* Filtre de Recherche instantané (JavaScript) : Le client sélectionne ses critères de sélection sans rechargement de page. Le script filtre dynamiquement les éléments du catalogue selon deux critères :
* Type de Carburant : Filtrage strict entre les motorisations Diesel et Essence.
* Boîte de Vitesse : Sélection entre transmission Manuelle et Automatique.


* Formulaire de Période : Saisie des dates de début et de fin de location pour initialiser le calcul de la réservation.

#### 3.3 Cycle de Réservation et Logique Métier

* Gestion des Statuts : Toute nouvelle demande de réservation est enregistrée par défaut avec le statut En attente.
* Calcul de Prix Transparent : L'application calcule automatiquement l'intervalle de jours entre la date de départ et la date de retour et affiche le montant global en Dirhams (DH).
* Prévention des Conflits : Un véhicule réservé pour une période donnée est marqué pour éviter toute soumission concurrente sur les mêmes dates.

#### 3.4 Interface Administrative (Dashboard) et Communication

* CRUD Flotte : Gestion complète du catalogue par l'administrateur (Ajouter un nouveau véhicule, modifier le prix ou les spécifications, supprimer un modèle obsolète).
* Suivi Clientèle et Validation WhatsApp : L'administrateur accède aux fiches des réservations en attente, consulte les coordonnées du client pour établir un contact de validation (via Téléphone ou WhatsApp), puis bascule manuellement le statut de la réservation sur Confirmée ou Annulée.

---

### 4. Acteurs du Système

*  L'Administrateur (Gestionnaire de l'Agence) : Possède un accès total au panneau de contrôle arrière (Back-office). Il gère le parc automobile, modifie la disponibilité des voitures et valide ou rejette les demandes de location après confirmation externe.


*  Le Client (Utilisateur Final) : Navigue sur le site, utilise le filtre JavaScript pour trouver un véhicule adapté (selon la boîte et le carburant), soumet ses dates de voyage, effectue sa demande de réservation et consulte l'état d'avancement de ses dossiers depuis son espace personnel.



---

### 5. Charte Graphique et Identité Visuelle

Conformément aux maquettes d'interface utilisateur (UI), l'application adopte un style moderne, professionnel et épuré. La palette chromatique utilise des contrastes forts pour guider l'action de l'utilisateur :

* Bleu Marine (Couleur Principale) : #1A2B49 (Exprime la confiance et le professionnalisme).
* Rouge Accent (Boutons et Actions) : #E53E3E (Apporte de la vitalité et guide le clic).
* Gris Clair (Fonds de page) : #F8FAFC (Garantit un design épuré).
* Typographie : Utilisation exclusive de la police Google Fonts Poppins, choisie pour sa clarté de lecture sur les supports mobiles et desktop.

---

### 6. Spécifications Techniques

| Composant | Technologie Utilisée | Rôle / Application dans le Projet |
| --- | --- | --- |
| **Serveur & Backend** | PHP 8 (Natif) | Traitement des requêtes, gestion de la logique métier et des sessions. |
| **Accès aux Données** | PHP PDO | Requêtes préparées pour sécuriser la base de données contre les injections. |
| **Base de Données** | MySQL | Stockage structuré des tables (Utilisateurs, Véhicules, Réservations). |
| **Interface Client (UI)** | HTML5 / CSS3 | Structure sémantique et design adaptatif (Responsive) basé sur la charte EasyCar. |
| **Dynamisme Local** | JavaScript (ES6) | Moteur de filtrage instantané du catalogue (Carburant / Boîte) côté client. |
| **Environnement Local** | XAMPP / phpMyAdmin | Serveur Apache local et gestionnaire graphique de la base de données. |

---