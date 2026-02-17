<?php
/**
 * Point d'entrée front : shortcode et assets.
 * Hook front = wp_enqueue_scripts (équivalent de admin_enqueue_scripts en admin).
 */

class InssetProjet_Main_Front {

    public function __construct() {
        add_shortcode('TESTPROJETBG', array('InssetProjet_Controller_Front', 'displayForm'));
        add_action('wp_enqueue_scripts', array($this, 'assets'));
    }

    public function assets() {
        $load_assets = false;

        if (get_query_var(InssetProjet_Rewrite_Confirmation::QUERY_VAR)) {
            $load_assets = true;
        } elseif (is_singular()) {
            $post = get_post();
            if ($post && has_shortcode($post->post_content, 'TESTPROJETBG')) {
                $load_assets = true;
            }
        }

        if (!$load_assets) {
            return;
        }

        wp_enqueue_style(
            'inssetprojet_front_css',
            plugins_url(INSSETPROJET_PLUGIN_NAME . '/assets/css/inssetprojet-front.css'),
            array(),
            INSSETPROJET_VERSION
        );

        wp_enqueue_script(
            'inssetprojet_front_js',
            plugins_url(INSSETPROJET_PLUGIN_NAME . '/assets/js/inssetprojet-front.js'),
            array('jquery'),
            INSSETPROJET_VERSION,
            true
        );

        wp_localize_script(
            'inssetprojet_front_js',
            'inssetprojet_front',
            array(
                'ajax_url'         => admin_url('admin-ajax.php'),
                'nonce'            => wp_create_nonce('inssetprojet_inscription'),
                'confirmation_url' => InssetProjet_Rewrite_Confirmation::get_url(),
            )
        );
    }
}
