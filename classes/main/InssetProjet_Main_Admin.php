<?php

class InssetProjet_Main_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'inssetprojet'), -1);
        add_action('admin_enqueue_scripts', array($this, 'assets'), 999);
        add_action('wp_ajax_bisounours', array('InssetProjet_Actions_Admin_Index', 'dothejob'));
        // Export CSV et suppression d'inscriptions passent par admin-post (utilisateurs connectés uniquement).
        add_action('admin_post_inssetprojet_export_suivi', array('InssetProjet_Actions_Admin_Index', 'export_suivi'));
        add_action('admin_post_inssetprojet_delete_inscription', array('InssetProjet_Actions_Admin_Index', 'delete_inscription'));
        return;
    }

    public function inssetprojet() {
        add_menu_page(
            __('InssetProjet'),
            __('InssetProjet'),
            'administrator',
            'inssetprojet_settings',
            array('InssetProjet_Controller_Admin', 'settings'),
            '',
            601
        );

        add_submenu_page(
            'inssetprojet_settings',
            __('InssetProjet - Settings'),
            __('Settings'),
            'administrator',
            'inssetprojet_settings',
            array('InssetProjet_Controller_Admin', 'settings')
        );

        add_submenu_page(
            'inssetprojet_settings',
            __('Grille de suivi'),
            __('Grille de suivi'),
            'administrator',
            'inssetprojet_suivi',
            array('InssetProjet_Controller_Admin', 'suivi')
        );

        add_submenu_page(
            'inssetprojet_settings',
            __('Statistiques'),
            __('Statistiques'),
            'administrator',
            'inssetprojet_stats',
            array('InssetProjet_Controller_Admin', 'stats')
        );

        add_submenu_page(
            'inssetprojet_settings',
            __('Timeline'),
            __('Timeline'),
            'administrator',
            'inssetprojet_timeline',
            array('InssetProjet_Controller_Admin', 'timeline')
        );
    }

    public function assets() {

        wp_enqueue_script(
            'inssetprojet_admin', 
            plugins_url(INSSETPROJET_PLUGIN_NAME . '/assets/js/InssetProjet_Admin.js'),
            array('jquery'),      
            INSSETPROJET_VERSION, 
            true                  
        );

        wp_enqueue_style(
            'inssetprojet_admin_css',
            plugins_url(INSSETPROJET_PLUGIN_NAME . '/assets/css/inssetprojet-admin.css'),
            array(),
            INSSETPROJET_VERSION
        );

 
        wp_localize_script(
            'inssetprojet_admin',
            'girafe',
            array(
                'security' => wp_create_nonce('ajax_nonce_generic'),
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );

    }

    public function update_config() {

        check_ajax_referer('ajax_nonce_generic', 'security');

        if (!current_user_can('administrator')) {
            wp_send_json_error(array('message' => 'Permissions insuffisantes'));
        }

        
    }

}
