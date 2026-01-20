C'est parti. Voici le `README.md` **définitif et complet**.

Il regroupe tout : la structure, l'installation, la sécurité post-install, les commandes utiles, et surtout **la section sur le développement (Volumes vs Bind Mounts)** qui prouve que tu maîtrises le sujet pour ton alternance.

---

```markdown
# 🛍️ Projet Docker : PrestaShop 8

Ce dépôt contient un environnement de développement complet et conteneurisé pour **PrestaShop 8**. Il est conçu pour être modulaire, sécurisé et persistant, utilisant Docker Compose pour orchestrer l'application, la base de données et les outils d'administration.

## 📂 Structure du Projet

```text
.
├── docker-compose.yml    # Définition des services (PrestaShop, MariaDB, PMA)
├── .env                  # Variables d'environnement (Mots de passe, Config)
└── README.md             # Documentation

```

## 📋 Prérequis

* **Docker Engine** & **Docker Compose** installés sur la machine.
* Ports **8080** (Web) et **8081** (PMA) libres sur votre machine hôte.

---

## 🚀 Installation et Démarrage

### 1. Configuration (.env)

Le fichier `.env` à la racine centralise les secrets.

* Si vous cloner ce projet, copiez le fichier d'exemple (s'il existe) ou assurez-vous que le `.env` contient :
```ini
# --- Base de données ---
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=prestashop
MYSQL_USER=prestashop
MYSQL_PASSWORD=prestashop
DB_SERVER=db

# --- Config PrestaShop ---
PS_DEV_MODE=1
PS_INSTALL_AUTO=1
PS_DOMAIN=localhost:8080

# --- Info Admin (OBLIGATOIRES pour l'auto-install) ---
# Sans ça, l'installation automatique échoue silencieusement
ADMIN_MAIL=admin@prestashop.com
ADMIN_PASSWD=password123
PS_LANGUAGE=fr
PS_COUNTRY=FR

```



### 2. Lancement des conteneurs

À la racine du projet, exécutez :

```bash
docker-compose up -d

```

Cela va télécharger les images (MariaDB, PrestaShop, phpMyAdmin) et lancer le réseau.

### 3. Finalisation de l'installation (Web)

1. Rendez-vous sur **[http://localhost:8080](https://www.google.com/search?q=http://localhost:8080)**.
2. Suivez l'assistant d'installation.
3. À l'étape "Configuration de la base de données", utilisez ces paramètres (correspondant au `docker-compose.yml` et `.env`) :
* **Adresse du serveur :** `db`  *(⚠️ Important : ne pas mettre localhost)*
* **Nom de la base :** `prestashop`
* **Identifiant :** `presta_user`
* **Mot de passe :** `presta_password`



---

## 🔐 Post-Installation (Obligatoire)

Pour accéder au Back Office (administration), PrestaShop impose des mesures de sécurité.

### 1. Suppression du dossier d'installation

Exécutez cette commande dans votre terminal :

```bash
docker exec -it prestashop_app rm -rf /var/www/html/install

```

### 2. Récupération du nom du dossier Admin

PrestaShop renomme aléatoirement le dossier `admin` (ex: `admin582xyz`). Pour le trouver :

```bash
docker exec -it prestashop_app ls -d /var/www/html/admin*

```

*Notez le nom du dossier qui s'affiche.*

### 3. Accès

* **Boutique :** [http://localhost:8080](https://www.google.com/search?q=http://localhost:8080)
* **Administration :** `http://localhost:8080/NOM_DU_DOSSIER_ADMIN`
* **phpMyAdmin (SQL) :** [http://localhost:8081](https://www.google.com/search?q=http://localhost:8081)

---

## 💻 Guide de Développement (Avancé)

### Comprendre la persistance (Volumes vs Bind Mounts)

Ce projet utilise deux types de stockage, une distinction importante pour le développement :

1. **Volumes Docker (Persistance)** :
* Le volume `shop_data` (défini dans `docker-compose.yml`) stocke le cœur de PrestaShop (`/var/www/html`).
* Cela garantit que votre boutique ne s'efface pas si vous supprimez le conteneur.
* *Inconvénient* : Les fichiers sont gérés par Docker, difficilement accessibles depuis votre IDE Windows/Mac/Linux.


2. **Bind Mounts (Développement)** :
* Pour développer un module ou un thème personnalisé, nous utilisons un **Bind Mount**. Cela lie un dossier de votre PC directement à l'intérieur du conteneur.
* Vous modifiez le code sur votre PC, et les changements sont immédiats dans PrestaShop.



### Comment développer un module ?

Pour travailler sur un module personnalisé, modifiez le fichier `docker-compose.yml` comme suit :

1. Créez un dossier local (ex: `./modules/mon_module`).
2. Ajoutez la ligne de mapping dans la section `volumes` du service `prestashop` :

```yaml
    volumes:
      - shop_data:/var/www/html
      # Mapping pour le développement :
      - ./modules/mon_module:/var/www/html/modules/mon_module

```

3. Appliquez le changement :
```bash
docker-compose up -d

```



---

## 🛠️ Commandes Utiles (Cheat Sheet)

| Action | Commande | Description |
| --- | --- | --- |
| **Arrêter** | `docker-compose stop` | Met en pause les conteneurs (conserve les données). |
| **Redémarrer** | `docker-compose restart` | Utile après un changement de config PHP/Apache. |
| **Logs** | `docker-compose logs -f --tail=50` | Affiche les 50 dernières lignes de logs en direct. |
| **Shell** | `docker exec -it prestashop_app bash` | Ouvre un terminal à l'intérieur du conteneur. |
| **Nettoyage** | `docker-compose down -v` | **⚠️ DANGER** : Supprime les conteneurs ET les volumes (BDD perdue). |

## 🐛 Troubleshooting

* **Erreur "Link to database cannot be established" :**
* Vérifiez que le conteneur `prestashop_db` est bien lancé (`docker ps`).
* Vérifiez que vous utilisez `db` comme hôte SQL, et non `127.0.0.1`.


* **Problème de droits (Linux/Mac) :**
* Si PrestaShop n'arrive pas à écrire des fichiers, exécutez :
  `docker exec -it prestashop_app chown -R www-data:www-data /var/www/html`



```

### Prochaine étape possible
Comme tout est en place, souhaites-tu que je te génère un fichier `.gitignore` spécifique pour ne pas envoyer par erreur tes fichiers de configuration ou tes images Docker sur Git ?

```
