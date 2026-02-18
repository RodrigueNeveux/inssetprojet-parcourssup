<?php
/**
* Plugin Name: InssetProjet
* Version: 1.1.1
* Author: Rodrigue Le goat
* Description: Description du plugin
*/

if (!defined('ABSPATH'))
    exit;

if (!defined('INSSETPROJET_BASENAME')) {

    define('INSSETPROJET_VERSION', '1.1.1');
    define('INSSETPROJET_FILE', __FILE__);
    define('INSSETPROJET_DIR', dirname(INSSETPROJET_FILE));
    define('INSSETPROJET_BASENAME', pathinfo((INSSETPROJET_FILE))['filename']);
    define('INSSETPROJET_PLUGIN_NAME', INSSETPROJET_BASENAME);

    foreach (glob(INSSETPROJET_DIR .'/classes/*/*.php') as $filename)
        if (!@require_once $filename)
            throw new Exception(sprintf(__('Failed to include %s'), $filename));

    /**
     * Configuration de PHPMailer pour utiliser le SMTP Brevo.
     * (Exercice : en production, il faudrait stocker ces identifiants hors du code.)
     */
    add_action('phpmailer_init', function ($phpmailer) {
        // SMTP Brevo
        $phpmailer->isSMTP();
        $phpmailer->Host       = 'smtp-relay.brevo.com';
        $phpmailer->Port       = 587;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->SMTPSecure = 'tls';

        // Identifiants SMTP Brevo
        $phpmailer->Username   = 'a2a093001@smtp-brevo.com';
        $phpmailer->Password   = 'qNSn7YFREIGhxpyt';

        // Expéditeur par défaut
        if (empty($phpmailer->From)) {
            $phpmailer->setFrom('a2a093001@smtp-brevo.com', get_bloginfo('name'));
        }
    });

    add_action('plugins_loaded', array('InssetProjet_Install_Index', 'ensure_inscriptions_table'));
    add_action('plugins_loaded', array('InssetProjet_Install_Index', 'ensure_parcourssup_tables'));

    register_activation_hook(INSSETPROJET_FILE, function() {
        $InssetProjet_Install_Index = new InssetProjet_Install_Index();
        $InssetProjet_Install_Index->setup();
        InssetProjet_Rewrite_Confirmation::flush_rules();
    });

    InssetProjet_Rewrite_Confirmation::init();

    // Session étudiant ParcoursSup (front)
    add_action('init', array('InssetProjet_Session_Student', 'maybe_start'), 1);

    // Connexion / déconnexion étudiant (admin-post, avec et sans auth WP)
    add_action('admin_post_inssetprojet_student_login', array('InssetProjet_Action_Student_Login', 'handle_login'));
    add_action('admin_post_nopriv_inssetprojet_student_login', array('InssetProjet_Action_Student_Login', 'handle_login'));
    add_action('admin_post_inssetprojet_student_logout', array('InssetProjet_Action_Student_Login', 'handle_logout'));
    add_action('admin_post_nopriv_inssetprojet_student_logout', array('InssetProjet_Action_Student_Login', 'handle_logout'));

    // AJAX inscription (front) : pour utilisateurs connectés et non connectés
    add_action('wp_ajax_inssetprojet_inscription', array('InssetProjet_Action_Front_Index', 'save_inscription'));
    add_action('wp_ajax_nopriv_inssetprojet_inscription', array('InssetProjet_Action_Front_Index', 'save_inscription'));

    if (is_admin()) {
        new InssetProjet_Main_Admin();
    } else {
        new InssetProjet_Main_Front();
    }

}