<?php

class InssetProjet_View_Config {

    public function display() {

        global $wpdb;
        $basename  = $wpdb->prefix . INSSETPROJET_BASENAME;
        $tablename = $basename . '_config';

        print do_shortcode(
            sprintf(
                '[INSSETPROJET_VIEW_CONFIG tablename="%s" basename="%s" plugin="%s"]',
                $tablename,
                $basename,
                INSSETPROJET_PLUGIN_NAME
            )
        );

    }

}
