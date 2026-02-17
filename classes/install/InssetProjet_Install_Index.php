<?php

class InssetProjet_Install_Index {

    public function setup() {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Table config
        $sql = '
            CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . INSSETPROJET_BASENAME . '_config` (
                `id` VARCHAR(255) NOT NULL,
                `value` VARCHAR(255) NULL,
                `description` TEXT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB ' . $charset_collate;
        dbDelta($sql);

        // Table des inscriptions (grille de suivi)
        $table_inscriptions = $wpdb->prefix . INSSETPROJET_BASENAME . '_inscriptions';
        $sql_inscriptions = '
            CREATE TABLE IF NOT EXISTS `' . $table_inscriptions . '` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_civilite` VARCHAR(20) NOT NULL DEFAULT "",
                `user_nom` VARCHAR(100) NOT NULL,
                `user_prenom` VARCHAR(100) NOT NULL,
                `user_birthday` DATE NOT NULL,
                `user_email` VARCHAR(255) NOT NULL,
                `user_phone` VARCHAR(10) NOT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB ' . $charset_collate;
        dbDelta($sql_inscriptions);

        // --- Tables ParcoursSup-like (projet noté LP 2025-2026) ---
        $this->setup_parcourssup_tables();

        // Données par défaut config (une seule fois)
        $config_table = $wpdb->prefix . INSSETPROJET_BASENAME . '_config';
        if ($wpdb->get_var("SELECT COUNT(*) FROM `" . $config_table . "`") == 0) {
            $wpdb->insert(
                $wpdb->prefix . INSSETPROJET_BASENAME . '_config',
                array(
                    'id'          => 'date',
                    'value'       => '',
                    'description' => 'Date de référence'
                ),
                array('%s', '%s', '%s')
            );

            $wpdb->insert(
                $wpdb->prefix . INSSETPROJET_BASENAME . '_config',
                array(
                    'id'          => 'isOpen',
                    'value'       => 'false',
                    'description' => 'Cocher si la fonctionnalité est activée'
                ),
                array('%s', '%s', '%s')
            );

            $wpdb->insert(
                $wpdb->prefix . INSSETPROJET_BASENAME . '_config',
                array(
                    'id'          => 'nbPersonnes',
                    'value'       => '0',
                    'description' => 'Nombre de personnes'
                ),
                array('%s', '%s', '%s')
            );
        }

    }

    public function isTableBaseAlreadyCreated() {

        global $wpdb;

        $sql = 'SHOW TABLES LIKE \'%' . $wpdb->prefix . INSSETPROJET_BASENAME . '%\'';
        return $wpdb->get_var($sql);

    }

    /**
     * Crée la table inscriptions si elle n'existe pas (mise à jour sans réactivation).
     */
    public static function ensure_inscriptions_table() {
        global $wpdb;
        $table = $wpdb->prefix . INSSETPROJET_BASENAME . '_inscriptions';
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'") !== $table) {
            $sql = '
                CREATE TABLE IF NOT EXISTS `' . $table . '` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `user_civilite` VARCHAR(20) NOT NULL DEFAULT "",
                    `user_nom` VARCHAR(100) NOT NULL,
                    `user_prenom` VARCHAR(100) NOT NULL,
                    `user_birthday` DATE NOT NULL,
                    `user_email` VARCHAR(255) NOT NULL,
                    `user_phone` VARCHAR(10) NOT NULL,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB ' . $charset_collate;
            dbDelta($sql);
        } else {
            // Ajouter la colonne civilité si elle manque (mise à jour)
            $col = $wpdb->get_results("SHOW COLUMNS FROM `" . $table . "` LIKE 'user_civilite'");
            if (empty($col)) {
                $wpdb->query("ALTER TABLE `" . $table . "` ADD COLUMN `user_civilite` VARCHAR(20) NOT NULL DEFAULT '' AFTER `id`");
            }
        }
    }

}