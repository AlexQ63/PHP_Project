# Création d'un site de Watchlist
	L'objectif de ce projet est de concevoir et de développer un site internet de gestion de
	watchlist en PHP, fonctionnant entièrement en local. Vous travaillerez seul et disposez d'un
	mois pour réaliser le projet.

### Fonctionnalités requises

#### 1. Système de connexion et d'enregistrement
	● Implémenter un système de gestion des utilisateurs permettant :
		○ La création d'un compte utilisateur (nom d'utilisateur et mot de passe).
		○ La connexion et la déconnexion.
	● Les mots de passe doivent être stockés hachés dans la base de données.

#### 2. Gestion des films

	● Chaque utilisateur doit pouvoir :
		○ Ajouter un film à sa watchlist avec les informations suivantes :
			■ Titre
			■ Image (upload local d'un fichier image)
		○ Modifier les informations d'un film qu'il a ajouté.
		○ Supprimer un film qu'il a ajouté.
		○ Voir la liste des films qu'il a ajoutés.
	● Ajouter un système de catégories ou de tags pour les films.
	● Implémenter une pagination pour les listes de films.

#### 3. Restrictions

	● Un utilisateur ne doit voir que les films qu'il a créés.
	● Les données doivent être stockées dans une base de données locale (par exemple,
	MySQL).

### Contraintes techniques

####  1. Langages et technologies
	
	○ Backend : PHP (sans frameworks, PHP natif).
	○ Base de données : MySQL ou SQLite.
	○ Frontend : HTML/CSS de base. Vous pouvez utiliser un peu de JavaScript si
	nécessaire, mais ce n'est pas obligatoire.

#### 2. Validation des données
	○ Les formulaires doivent être correctement validés (côté serveur) pour
	empêcher les entrées invalides ou malveillantes.

#### 3. Sécurité
	○ Protéger les pages sensibles par des vérifications d'authentification.
	○ Utiliser des requêtes préparées pour empêcher les injections SQL.
	Livrables attendus
	● Le code source complet du projet.
	● Un fichier SQL pour créer la base de données et insérer les données de départ (si
	nécessaire).
	● Un document expliquant comment installer et lancer le projet en local.

### Critères d'évaluation

#### 1. Fonctionnalités
	
	○ Toutes les fonctionnalités requises sont implémentées.
	○ Les utilisateurs peuvent ajouter, modifier, supprimer et afficher leurs films.

#### 2. Qualité du code
	
	○ Lisibilité et organisation du code.
	○ Respect des bonnes pratiques (exemple : sécurité des mots de passe,
	validations des formulaires).

#### 3. Interface utilisateur

	○ Interface fonctionnelle et facile à utiliser.
	○ Respect des règles de base du design web.
