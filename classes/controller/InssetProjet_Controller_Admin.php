<?php

class InssetProjet_Controller_Admin {

    public static function settings() {
        $view = new InssetProjet_View_Config();
        $view->display();
    }

    public static function suivi() {
        $view = new InssetProjet_View_Suivi();
        $view->display();
    }

    public static function stats() {
        $view = new InssetProjet_View_Stats();
        $view->display();
    }

    public static function timeline() {
        $view = new InssetProjet_View_Timeline();
        $view->display();
    }
}
