<?php


class InssetProjet_Crud_Inscription {

    private static function get_table() {
        global $wpdb;
        return $wpdb->prefix . INSSETPROJET_BASENAME . '_inscriptions';
    }


    public static function insert($data) {
        global $wpdb;
        $table = self::get_table();

        $birthday = isset($data['user_birthday']) ? preg_replace('/[^0-9\-]/', '', $data['user_birthday']) : '';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday)) {
            $birthday = '0000-00-00';
        }

        $ok = $wpdb->insert(
            $table,
            array(
                'user_civilite' => sanitize_text_field($data['user_civilite'] ?? ''),
                'user_nom'      => sanitize_text_field($data['user_nom'] ?? ''),
                'user_prenom'   => sanitize_text_field($data['user_prenom'] ?? ''),
                'user_birthday' => $birthday,
                'user_email'    => sanitize_email($data['user_email'] ?? ''),
                'user_phone'    => preg_replace('/\D/', '', $data['user_phone'] ?? ''),
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($ok) {
            // Invalide le cache du compteur d'inscriptions.
            delete_transient('inssetprojet_inscriptions_count');
            return (int) $wpdb->insert_id;
        }

        return false;
    }


    /**
     * Supprime une inscription par ID.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id) {
        global $wpdb;
        $table = self::get_table();

        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }

        $deleted = $wpdb->delete(
            $table,
            array('id' => $id),
            array('%d')
        );

        if ($deleted) {
            // Invalide le cache du compteur d'inscriptions.
            delete_transient('inssetprojet_inscriptions_count');
            return true;
        }

        return false;
    }

    public static function get_all() {
        global $wpdb;
        $table = self::get_table();

        $sql = "SELECT * FROM `{$table}` ORDER BY `created_at` DESC";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Retourne le nombre total d'inscriptions, avec mise en cache via transient.
     *
     * Cache pendant 2 minutes pour limiter les requêtes COUNT(*) tout en gardant
     * une information suffisamment fraîche pour l'admin.
     *
     * @return int
     */
    public static function get_count() {
        $cache_key = 'inssetprojet_inscriptions_count';

        $count = get_transient($cache_key);

        if ($count === false) {
            global $wpdb;
            $table = self::get_table();

            $sql   = "SELECT COUNT(*) FROM `{$table}`";
            $count = (int) $wpdb->get_var($sql);

            set_transient($cache_key, $count, 2 * MINUTE_IN_SECONDS);
        }

        return (int) $count;
    }

    /**
     * Retourne les effectifs par civilité (Monsieur, Madame, Mademoiselle, Autre).
     *
     * @return array [ 'Monsieur' => 10, 'Madame' => 5, ... ]
     */
    public static function get_civilite_stats() {
        global $wpdb;
        $table = self::get_table();

        $sql   = "SELECT user_civilite, COUNT(*) AS total FROM `{$table}` GROUP BY user_civilite";
        $rows  = $wpdb->get_results($sql, ARRAY_A);

        $stats = array(
            'Monsieur'     => 0,
            'Madame'       => 0,
            'Mademoiselle' => 0,
            'Autre'        => 0,
        );

        foreach ($rows as $row) {
            $civ = isset($row['user_civilite']) ? trim((string) $row['user_civilite']) : '';
            $n   = (int) ($row['total'] ?? 0);

            if (isset($stats[$civ])) {
                $stats[$civ] += $n;
            } elseif ($civ !== '') {
                $stats['Autre'] += $n;
            }
        }

        return $stats;
    }

    /**
     * Retourne le nombre d'inscriptions par heure (0 à 23) à partir de created_at.
     *
     * @return array [0 => 2, 1 => 0, ..., 23 => 5]
     */
    public static function get_hourly_stats() {
        global $wpdb;
        $table = self::get_table();

        $sql  = "SELECT HOUR(created_at) AS heure, COUNT(*) AS total FROM `{$table}` GROUP BY HOUR(created_at)";
        $rows = $wpdb->get_results($sql, ARRAY_A);

        // Initialise toutes les heures à 0 pour avoir une timeline complète.
        $stats = array();
        for ($h = 0; $h < 24; $h++) {
            $stats[$h] = 0;
        }

        foreach ($rows as $row) {
            $h = isset($row['heure']) ? (int) $row['heure'] : 0;
            $n = (int) ($row['total'] ?? 0);

            if ($h >= 0 && $h <= 23) {
                $stats[$h] = $n;
            }
        }

        return $stats;
    }
}
