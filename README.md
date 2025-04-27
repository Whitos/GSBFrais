
# GSB Frais

Application Web de gestion des frais professionnels pour les visiteurs médicaux et le service comptable du laboratoire Galaxy Swiss Bourdin.

## 📋 Présentation du projet

L'application **GSB Frais** permet :
- Aux visiteurs médicaux de saisir leurs frais forfaitisés et hors-forfait, et de suivre l’état de leurs remboursements.
- Aux comptables de valider les fiches de frais, contrôler les frais saisis, et suivre le paiement des remboursements.

Le projet s'inscrit dans une démarche de modernisation et d'uniformisation de la gestion des frais, avec un accent sur la **sécurité**, la **traçabilité**, et l'**ergonomie**.

## 🛠️ Fonctionnalités principales

- Authentification sécurisée (visiteur médical ou comptable)
- Saisie et modification des frais forfaitisés et hors forfait
- Consultation et suivi des fiches de frais
- Validation des fiches par les comptables
- Suivi du paiement et remboursement des frais

## 🧱 Architecture technique

- **Symfony** (PHP) pour le back-end
- **Twig** pour les vues
- **MySQL** pour la base de données
- **Tailwind CSS** pour le design responsive
- Architecture **MVC** (Modèle - Vue - Contrôleur)

## 🗂️ Arborescence des principaux dossiers Symfony

```
src/
├── Controller/
├── Entity/
├── Form/
├── Repository/
├── Security/
templates/
```

## 🧪 Assurance qualité

### Tests fonctionnels réalisés
- Connexion/déconnexion sécurisée
- Saisie/modification/suppression de frais
- Validation correcte des fiches de frais
- Suivi de l’état de remboursement
- Sécurité des accès

### Tests unitaires
- Test des entités principales : `User`, `FicheFrais`, `Etat`, `LigneFraisForfait`, `LigneFraisHorsForfait`
- Vérification de la validité des données (emails, montants, dates)

## 🚀 Déploiement rapide

1. Cloner le dépôt :
```bash
git clone https://github.com/Whitos/GSBFrais.git
```
2. Installer les dépendances Symfony :
```bash
composer install
```
3. Configurer votre fichier `.env` pour la connexion à votre base de données.
4. Créer et mettre à jour la base :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
5. Lancer le serveur Symfony :
```bash
symfony server:start
```

## 🔗 Liens utiles

- [Accéder au projet sur GitHub](https://github.com/Whitos/GSBFrais)
