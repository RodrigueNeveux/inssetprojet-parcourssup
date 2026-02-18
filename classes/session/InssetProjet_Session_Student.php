<?php
/**
 * Session étudiant ParcoursSup : connexion / déconnexion, redirection 302 si non connecté.
 */

class InssetProjet_Session_Student {

    const SESSION_KEY = 'inssetprojet_student_id';

    /**
     * Démarre la session PHP si nécessaire (front uniquement).
     */
    public static function maybe_start() {
        if (is_admin()) {
            return;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * L'étudiant est-il connecté ?
     *
     * @return bool
     */
    public static function is_logged_in() {
        return isset($_SESSION[self::SESSION_KEY]) && (int) $_SESSION[self::SESSION_KEY] > 0;
    }

    /**
     * ID de l'étudiant connecté (0 si non connecté).
     *
     * @return int
     */
    public static function get_student_id() {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return 0;
        }
        return (int) $_SESSION[self::SESSION_KEY];
    }

    /**
     * Connecte l'étudiant (enregistre son ID en session).
     *
     * @param int $student_id
     */
    public static function set_student($student_id) {
        $_SESSION[self::SESSION_KEY] = (int) $student_id;
    }

    /**
     * Déconnecte l'étudiant.
     */
    public static function logout() {
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Redirige vers l'accueil avec code 302 si l'étudiant n'est pas connecté.
     * À appeler avant d'afficher une page du parcours (campagne, choix, confirmation).
     *
     * @return bool true si connecté, false si redirection envoyée (exit).
     */
    public static function require_login() {
        if (self::is_logged_in()) {
            return true;
        }
        wp_safe_redirect(home_url('/'), 302);
        exit;
    }
}
