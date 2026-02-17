<?php

class InssetProjet_Crud_Config {

    public static function update($settings) {
        $instance = new self();
        $results = array();

        foreach ($settings as $setting) {
            // $setting[0] est la clé (ex: 'date'), $setting[1] est la valeur
            $results[] = $instance->updateConfig($setting[0], $setting[1]);
        }

        return $results;
    }

    public function updateConfig($id, $value) {
        global $wpdb;
        $table = $wpdb->prefix . INSSETPROJET_BASENAME . '_config';

        // 1. Protection contre les clés interdites
        if (in_array($id, array('action', 'security'), true)) {
            return false;
        }

        return $wpdb->replace(
            $table,
            array(
                'id'    => sanitize_key($id),
                'value' => sanitize_text_field($value)
            ),
            array('%s', '%s')
        );
    }
}