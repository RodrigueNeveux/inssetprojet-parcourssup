<?php
/**
 * Action : connexion étudiant (POST login + mot de passe), session, redirection.
 */

class InssetProjet_Action_Student_Login {

    /**
     * Traite le formulaire de connexion étudiant.
     */
    public static function handle_login() {
        InssetProjet_Session_Student::maybe_start();

        if (!isset($_POST['inssetprojet_student_login_nonce']) ||
            !wp_verify_nonce($_POST['inssetprojet_student_login_nonce'], 'inssetprojet_student_login')) {
            wp_safe_redirect(add_query_arg('inssetprojet_login_error', 'nonce', home_url('/')), 302);
            exit;
        }

        $login    = isset($_POST['student_login']) ? trim($_POST['student_login']) : '';
        $password = isset($_POST['student_password']) ? $_POST['student_password'] : '';

        if ($login === '' || $password === '') {
            wp_safe_redirect(add_query_arg('inssetprojet_login_error', 'empty', home_url('/')), 302);
            exit;
        }

        $student = InssetProjet_Crud_Student::get_by_login($login);
        if (!$student || !password_verify($password, $student['password_hash'])) {
            wp_safe_redirect(add_query_arg('inssetprojet_login_error', 'invalid', home_url('/')), 302);
            exit;
        }

        InssetProjet_Session_Student::set_student($student['id']);
        wp_safe_redirect(home_url('/'), 302);
        exit;
    }

    /**
     * Déconnexion étudiant.
     */
    public static function handle_logout() {
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'inssetprojet_student_logout')) {
            wp_safe_redirect(home_url('/'), 302);
            exit;
        }
        InssetProjet_Session_Student::maybe_start();
        InssetProjet_Session_Student::logout();
        wp_safe_redirect(home_url('/'), 302);
        exit;
    }
}
