<?php
/**
 * Action AJAX : enregistrement d'une inscription depuis le formulaire front.
 */

class InssetProjet_Action_Front_Index {

    /**
     * Reçoit le formulaire d'inscription (POST) et enregistre en base.
     */
    public static function save_inscription() {
        check_ajax_referer('inssetprojet_inscription', 'inssetprojet_inscription_nonce');

        $data = array(
            'user_civilite' => isset($_POST['user_civilite']) ? $_POST['user_civilite'] : '',
            'user_nom'      => isset($_POST['user_nom']) ? $_POST['user_nom'] : '',
            'user_prenom'   => isset($_POST['user_prenom']) ? $_POST['user_prenom'] : '',
            'user_birthday' => isset($_POST['user_birthday']) ? $_POST['user_birthday'] : '',
            'user_email'    => isset($_POST['user_email']) ? $_POST['user_email'] : '',
            'user_phone'    => isset($_POST['user_phone']) ? $_POST['user_phone'] : '',
        );

        if (empty($data['user_civilite']) || empty($data['user_nom']) || empty($data['user_prenom']) || empty($data['user_birthday']) || empty($data['user_email']) || empty($data['user_phone'])) {
            wp_send_json_error(array('message' => __('Champs obligatoires manquants (dont la date de naissance).', 'inssetprojet')));
        }

        // Date au format YYYY-MM-DD (compatible MySQL)
        $data['user_birthday'] = preg_replace('/[^0-9\-]/', '', $data['user_birthday']);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['user_birthday'])) {
            wp_send_json_error(array('message' => __('Date de naissance invalide.', 'inssetprojet')));
        }

        $id = InssetProjet_Crud_Inscription::insert($data);

        if ($id === false) {
            wp_send_json_error(array('message' => __('Erreur lors de l\'enregistrement.', 'inssetprojet')));
        }

        wp_send_json_success(array('message' => __('Inscription enregistrée.', 'inssetprojet'), 'id' => $id));
    }
}
