<?php
/**
 * CRUD étudiant (ParcoursSup) : login type PS, mot de passe hashé.
 */

class InssetProjet_Crud_Student {

    private static function get_table() {
        global $wpdb;
        return $wpdb->prefix . INSSETPROJET_BASENAME . '_student';
    }

    /**
     * Récupère un étudiant par identifiant (login type PS).
     *
     * @param string $login
     * @return array|null
     */
    public static function get_by_login($login) {
        global $wpdb;
        $table = self::get_table();
        $login = sanitize_text_field($login);
        if ($login === '') {
            return null;
        }
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM `{$table}` WHERE `login` = %s LIMIT 1",
                $login
            ),
            ARRAY_A
        );
        return is_array($row) ? $row : null;
    }

    /**
     * Récupère un étudiant par ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = self::get_table();
        $id    = (int) $id;
        if ($id <= 0) {
            return null;
        }
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM `{$table}` WHERE `id` = %d LIMIT 1", $id),
            ARRAY_A
        );
        return is_array($row) ? $row : null;
    }

    /**
     * Crée un étudiant (mot de passe hashé avec password_hash).
     *
     * @param string $login
     * @param string $password Mot de passe en clair.
     * @return int|false ID de l'étudiant ou false.
     */
    public static function create($login, $password) {
        global $wpdb;
        $table = self::get_table();
        $login = sanitize_text_field($login);
        if ($login === '') {
            return false;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ok   = $wpdb->insert(
            $table,
            array(
                'login'         => $login,
                'password_hash' => $hash,
            ),
            array('%s', '%s')
        );
        return $ok ? (int) $wpdb->insert_id : false;
    }
}
