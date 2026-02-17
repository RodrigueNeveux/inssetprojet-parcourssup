# LP 2025–2026 — Projet noté (ParcoursSup-like)

**À faire :** quand tu reprends le projet ou que tu fais intervenir quelqu’un (dont un agent IA), copie-colle ce fichier ou les sections concernées pour que le contexte et les tâches soient clairs.

---

## 1. Objectif du projet

Développer une **extension WordPress** inspirée de **ParcoursSup** :
- Front : un étudiant se connecte, accède à une campagne active, fait 3 choix de formations dans l’ordre, valide, puis voit une page de confirmation.
- Back : gestion des campagnes (CRUD), des formations par campagne, visualisation des choix par campagne, export CSV ; interdiction de supprimer une campagne si des étudiants ont déjà des choix.
- BDD : étudiants, campagnes, formations, table de liaison **student_to_campaign**, et stockage des choix en **entité/valeur** (extensible).

---

## 2. Contraintes techniques obligatoires

- **jQuery** : obligatoire pour les interactions front (selects, ordre, doublons).
- **Less** : obligatoire pour les styles (fichiers .less compilés en CSS).
- **Git** : dépôt avec commits réguliers ; lien envoyé par mail à david@koppaz.com.
- **MLD** : à rendre au plus tard le 16 février 2026, par mail au format PDF uniquement.

---

## 3. Ce que le code doit faire (découpage pour commits)

### 3.1 Base de données

- Tables : **student** (auth : identifiant type PS + mot de passe **hashé** en BDD), **campaign**, **formation**, **student_to_campaign** (liaison étudiant ↔ campagne), et une table de **choix** au format **entité / valeur** (pour extensibilité, pas limité à 3 champs).
- Chaque campagne est liée à un ensemble de formations (celles proposées dans les listes déroulantes).
- Script d’installation à l’activation du plugin (création des tables).

### 3.2 Authentification (front)

- Page d’accueil : **box d’authentification** (identifiant + mot de passe).
- Connexion vérifiée en BDD ; mot de passe stocké **hashé** (ex. `password_hash` / `password_verify`).
- **Accès au reste du parcours uniquement si connecté** : sinon redirection (HTTP **301** ou **302**) vers la page d’accueil / login.

### 3.3 Back-office : campagnes et formations

- **CRUD campagnes** : ajouter, modifier, supprimer une campagne.
- **Formations par campagne** : associer à chaque campagne l’ensemble des formations/spécialités proposées ; interface admin pour gérer ces associations.
- **Règle métier** : **interdit de supprimer une campagne** si au moins un étudiant a déjà formulé des choix dans cette campagne (vérification côté serveur avant suppression).

### 3.4 Front-office : parcours étudiant

- Une fois connecté, l’étudiant accède à **une campagne active** (définie en admin ou par statut/date).
- **Entrée en campagne** = création / utilisation d’un enregistrement dans **student_to_campaign** (un étudiant peut avoir des réponses dans plusieurs campagnes ; une campagne contient les réponses de plusieurs étudiants).
- Sur la campagne : **3 champs de sélection** (formations/spécialités).
  - **Ordre strict** : impossible de sélectionner le choix n°2 sans avoir choisi le choix n°1 ; impossible de sélectionner le choix n°3 sans le choix n°2 (ex. `<select>` désactivés tant que le précédent n’est pas choisi).
  - **Pas de doublon** : une formation déjà choisie ne peut pas être sélectionnée à nouveau dans un autre choix.
- **Validation** : bouton de validation ; enregistrement des 3 choix en BDD (format entité/valeur) ; **redirection vers une page de confirmation**.
- **Page de confirmation** : récapitulatif des 3 choix (formations) de l’étudiant pour cette campagne.
- Contraintes à gérer **côté interface (UX)** et **idéalement côté logique métier (back)**.

### 3.5 Back-office : visualisation et export

- **Visualisation des n choix des étudiants, par campagne** : écran(s) admin listant, pour chaque campagne (ou une campagne choisie), les étudiants et leurs choix.
- **Export des choix au format CSV** (depuis l’admin, sécurisé par droits et nonce).

### 3.6 Qualité et technique

- **jQuery** pour toutes les interactions front (selects, désactivation, gestion des doublons, validation côté client).
- **Less** pour tous les styles ; pas uniquement du CSS brut.
- Code propre : nommage clair, classes avec responsabilités distinctes, indentation correcte.
- **Tests** pertinents (ex. PHPUnit) sur la logique métier (règles de choix, suppression de campagne, etc.).

---

## 4. Rendu attendu

- Dépôt **Git** avec historique de commits réguliers.
- Lien du dépôt envoyé par mail à : **david@koppaz.com**.
- **MLD (Modèle Logique de Données)** en PDF, envoyé par mail **au plus tard le 16 février 2026**.

---

## 5. Comment utiliser ce fichier

- **Pour toi** : ouvrir ce fichier et cocher / découper les tâches au fur et à mesure (ex. par commit).
- **Pour un agent ou un autre dev** : copier-coller **ce fichier entier** (ou la section 3 correspondant à la tâche demandée) en précisant par exemple :  
  *« Voici les consignes du projet ; la tâche à faire maintenant est : [ex. 3.4 Front-office parcours étudiant]. »*

Ainsi, en copiant-collant, le lecteur comprend exactement le contexte et ce qu’il doit faire.
