# 📋 Cliento

**Système de gestion des abonnements et clients** — une application web locale destinée aux petites structures (salles de sport, salles de jeux, centres de formation) pour gérer leurs clients, suivre le renouvellement des abonnements et éviter les pertes financières liées aux oublis de paiement.

---

## ✨ Aperçu

Cliento permet à un gérant de :

- Suivre l'ensemble de ses clients et leur historique d'abonnements
- Définir plusieurs formules d'abonnement (durée, prix)
- Activer, renouveler ou annuler un abonnement en quelques clics
- Visualiser en un coup d'œil qui est actif, qui arrive à échéance et qui a expiré

---

## 🌐 Démo en ligne

Une version de démonstration est disponible : **[cliento.infinityfree.me](https://cliento.infinityfree.me/login)**

| Champ | Valeur |
|---|---|
| Email | `admin@cliento.com` |
| Mot de passe | `password123` |

> ⚠️ Hébergement gratuit à but de démonstration uniquement — non représentatif des performances en production.

---

## 🧱 Stack technique

| Composant | Technologie |
|---|---|
| Langage backend | PHP (OOP, PDO) |
| Frontend | HTML5, CSS3 |
| Base de données | PostgreSQL |
| Architecture | MVC (Model-View-Controller) |
| Gestion des dépendances | Composer |
| Conteneurisation | Docker |

---

## 🏗️ Architecture

Le projet suit une architecture **MVC** stricte, avec une séparation claire des responsabilités :

```
Cliento/
├── config/          # Configuration de l'application (BDD, environnement)
├── public/          # Point d'entrée public (index.php, assets)
├── src/             # Code source : Models, Controllers, logique métier
├── views/           # Templates / vues (HTML rendu par les contrôleurs)
├── logs/            # Journalisation des erreurs et événements
├── schema.sql       # Script SQL de création de la base de données (PostgreSQL)
├── composer.json    # Dépendances PHP
├── Dockerfile        # Image Docker de l'application
└── .env.example     # Modèle de variables d'environnement
```

---

## 🗃️ Modèle de données

La base de données (PostgreSQL) a été conçue selon la méthode **MCD → MLD → SQL**, autour de 3 entités principales :

- **Client** : informations personnelles (nom, téléphone, email, date d'inscription)
- **Plan** (formule d'abonnement) : nom, prix, durée en jours
- **Abonnement (Subscription)** : lie un client à un plan, avec date de début, date de fin calculée automatiquement, et statut (actif, expire bientôt, expiré, annulé)

**Règles de gestion principales :**
- Un client peut avoir plusieurs abonnements dans le temps, mais un seul abonnement actif à la fois
- Un plan peut être associé à plusieurs abonnements
- La date de fin d'un abonnement est **toujours calculée par le système** (jamais saisie manuellement) pour éviter les erreurs humaines

---

## 🚀 Modules fonctionnels

### 🔑 Authentification
Connexion sécurisée du gérant (email + mot de passe hashé), gestion de session.

### 👥 Gestion des clients
Ajout, modification, suppression, recherche par nom/téléphone, fiche client détaillée avec historique des abonnements.

### 📦 Gestion des plans
Création et modification des formules d'abonnement (nom, prix, durée).

### 🔄 Gestion des abonnements
Activation, calcul automatique de la date d'expiration, suivi des statuts (actif / bientôt expiré / expiré / annulé), renouvellement rapide.

---

## ⚙️ Installation

### Prérequis
- PHP >= 8.0
- Composer
- PostgreSQL
- Docker (optionnel, pour un déploiement conteneurisé)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/othmane01010/Cliento.git
cd Cliento

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
# → renseigner les identifiants de connexion PostgreSQL

# 4. Importer le schéma de la base de données
psql -U [user] -d [database] -f schema.sql

# 5. Lancer le serveur local
php -S localhost:8000 -t public
```

L'application est ensuite accessible sur `http://localhost:8000`.

### Avec Docker

```bash
docker build -t cliento .
docker run -p 8000:8000 cliento
```

---

## 🖥️ Pages principales

| Route | Vue (`views/`) | Description |
|---|---|---|
| `/login` | `views/auth/login.php` | Écran de connexion sécurisé |
| `/dashboard` | `views/dashboard/index.php` | Statistiques globales et alertes d'échéance |
| `/clients` | `views/clients/index.php` | Gestion des clients, recherche et profils |
| `/plans` | `views/plans/index.php` | Configuration des formules d'abonnement |
| `/subscriptions` | `views/subscriptions/index.php` | Activation, suivi et renouvellement des abonnements |

---

## 🔒 Sécurité et bonnes pratiques

- **Protection CSRF** : génération et validation de tokens (`hash_equals`) sur toutes les requêtes POST (login, création, modification, suppression)
- **Prévention des injections SQL** : requêtes préparées PDO exclusivement, via une classe `Database` centralisée (`prepare()` + `execute()`)
- **Sanitisation et XSS** : entrées nettoyées (`strip_tags` + `htmlspecialchars`) et sorties échappées systématiquement dans les vues
- **Gestion sécurisée des sessions** : flags `HttpOnly` et `SameSite=Lax`, régénération de l'ID de session après connexion
- **Upload sécurisé** : validation du type MIME réel via `finfo` (pas seulement l'extension), limite de taille (2 Mo), renommage aléatoire des fichiers
- **Mots de passe** : hachage via `password_hash()` (BCrypt, cost 12)

---

## 🗺️ Roadmap

- [ ] Notifications automatiques (email/SMS) avant expiration
- [ ] Export des données clients (PDF/Excel)
- [ ] Tableau de bord avec statistiques avancées
- [ ] API REST pour intégrations tierces
- [ ] Tests unitaires (PHPUnit)

---

## 👤 Auteur

**Othmane** — Étudiant en informatique (filière IA), Université Cadi Ayyad
[GitHub](https://github.com/othmane01010)

---

## 📄 Licence

Ce projet est sous licence [MIT](LICENSE).

> ⚠️ N'oublie pas d'ajouter un fichier `LICENSE` à la racine du dépôt contenant le texte de la licence MIT — sinon ce lien sera cassé.
