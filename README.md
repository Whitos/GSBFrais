
# GSB Frais - Dossier Technique

## Présentation du projet
L'application **GSB Frais** est destinée à la gestion des frais engagés par les visiteurs médicaux du laboratoire **Galaxy Swiss Bourdin**.
Elle permet l'enregistrement, la validation et le remboursement des frais, tout en offrant un accès sécurisé aux utilisateurs.

## Fonctionnement de l'application
### Visiteurs médicaux
- Authentification sécurisée
- Saisie des frais forfaitisés et hors forfait
- Suivi de l'évolution des remboursements

### Comptables
- Validation des fiches de frais
- Suivi du paiement des remboursements

## Architecture technique
- **Architecture** : MVC (Model-View-Controller)
- **Technologies** :
  - PHP / Symfony
  - Base de données MySQL
  - Front-end HTML/CSS (avec Tailwind/Bootstrap possibles)
- **Modèles** :
  - `User`, `FicheFrais`, `FraisForfait`, `FraisHorsForfait`

## Diagramme de classes
![Diagramme de classes](5e6d76f5-e2a2-4ecb-9efc-e4db4bbbdb14.png)

## Description des principales classes et contrôleurs
- **UserController** : Gestion des utilisateurs et de l'authentification
- **FicheFraisController** : Gestion des fiches de frais (saisie, validation, consultation)
- **Entités** :
  - **FicheFrais** : représente une fiche de frais par mois/utilisateur
  - **FraisForfait** : représente un frais standardisé
  - **FraisHorsForfait** : représente un frais exceptionnel avec justificatif

## Assurance qualité

### Tests fonctionnels réalisés :
- ✅ Connexion/déconnexion sécurisée
- ✅ Ajout, modification et suppression de frais
- ✅ Validation correcte des fiches de frais
- ✅ Respect des délais de clôture/mise en paiement
- ✅ Sécurité des accès

### Tests complémentaires recommandés :
- Tests unitaires sur les modèles
- Analyse de la qualité du code avec SonarQube / PHPStan

## Annexes
- **Accès au dépôt GitHub** : [GSB Frais Repository](https://github.com/Whitos/GSBFrais)
- **Instructions de déploiement** :
  1. Cloner le dépôt : `git clone https://github.com/Whitos/GSBFrais.git`
  2. Installer les dépendances Symfony
  3. Configurer le fichier `.env` pour la base de données
  4. Lancer les migrations si nécessaire
  5. Démarrer le serveur local : `symfony server:start`
