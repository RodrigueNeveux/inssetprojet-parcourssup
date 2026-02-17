<?php
/**
 * Règles de réécriture pour la page de confirmation d'inscription.
 * URL : /test-bg/steps/confirme-inscription
 */

class InssetProjet_Rewrite_Confirmation {

    const QUERY_VAR = 'inssetprojet_confirmation';
    const SLUG = 'test-bg/steps/confirme-inscription';

    /**
     * Enregistre la règle de réécriture et les hooks.
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'add_rewrite_rules'));
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));
        add_filter('template_include', array(__CLASS__, 'template_include'));
    }

    /**
     * Ajoute la règle de réécriture pour /inscription-confirmation.
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule(
            '^' . self::SLUG . '/?$',
            'index.php?' . self::QUERY_VAR . '=1',
            'top'
        );
    }

    /**
     * Enregistre la variable de requête personnalisée.
     */
    public static function add_query_vars($vars) {
        $vars[] = self::QUERY_VAR;
        return $vars;
    }

    /**
     * Charge le template de la page de confirmation quand l'URL correspond.
     */
    public static function template_include($template) {
        if (get_query_var(self::QUERY_VAR)) {
            $plugin_template = INSSETPROJET_DIR . '/templates/confirmation.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Retourne l'URL de la page de confirmation.
     */
    public static function get_url() {
        return home_url('/' . self::SLUG . '/');
    }

    /**
     * Flush des règles de réécriture (à appeler à l'activation du plugin).
     */
    public static function flush_rules() {
        self::add_rewrite_rules();
        flush_rewrite_rules();
    }
}
