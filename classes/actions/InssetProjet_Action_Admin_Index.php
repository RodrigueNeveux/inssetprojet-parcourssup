<?php

class InssetProjet_Actions_Admin_Index {

    public static function dothejob() {

        check_ajax_referer('ajax_nonce_generic', 'security');

        if (empty($_POST) || !is_array($_POST)) {
            wp_send_json_error(array('message' => 'Pas de données reçues'));
        }

        // Préparation des paramètres pour le CRUD :
        // InssetProjet_Crud_Config::update() attend un tableau de tableaux
        // de la forme [ [id, value], [id, value], ... ]
        $settings = array();

        foreach ($_POST as $key => $value) {
            if (in_array($key, array('action', 'security'), true)) {
                continue;
            }

            $settings[] = array($key, $value);
        }

        if (!empty($settings)) {
            InssetProjet_Crud_Config::update($settings);
        }

        wp_send_json_success(array('message' => 'Configuration mise à jour'));
    }

    /**
     * Export de la grille de suivi au format CSV
     * et envoi du fichier par email à l'administrateur du site.
     */
    public static function export_suivi() {
        // Vérification des droits et du nonce.
        if (!current_user_can('administrator')) {
            wp_die(__('Permissions insuffisantes', 'inssetprojet'));
        }

        check_admin_referer('inssetprojet_export_suivi');

        // Récupération des données.
        $rows = InssetProjet_Crud_Inscription::get_all();
        if (!is_array($rows)) {
            $rows = array();
        }

        // Génère le contenu CSV en mémoire.
        $filename = 'inscriptions-inssetprojet-' . date('Y-m-d-H-i-s') . '.csv';

        $handle = fopen('php://temp', 'r+');

        // Ajout d'un BOM UTF-8 pour Excel.
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Ligne d'en-têtes du CSV.
        fputcsv(
            $handle,
            array('ID', 'Civilité', 'Nom', 'Prénom', 'Date de naissance', 'Email', 'Téléphone', 'Date inscription'),
            ';'
        );

        // Lignes de données.
        foreach ($rows as $row) {
            fputcsv(
                $handle,
                array(
                    isset($row['id']) ? $row['id'] : '',
                    isset($row['user_civilite']) ? $row['user_civilite'] : '',
                    isset($row['user_nom']) ? $row['user_nom'] : '',
                    isset($row['user_prenom']) ? $row['user_prenom'] : '',
                    isset($row['user_birthday']) ? $row['user_birthday'] : '',
                    isset($row['user_email']) ? $row['user_email'] : '',
                    isset($row['user_phone']) ? $row['user_phone'] : '',
                    isset($row['created_at']) ? $row['created_at'] : '',
                ),
                ';'
            );
        }

        rewind($handle);
        $csv_content = stream_get_contents($handle);
        fclose($handle);

        // Sauvegarde temporaire du fichier dans le répertoire uploads.
        $upload_dir = wp_upload_dir();
        if (!empty($upload_dir['path']) && is_writable($upload_dir['path'])) {
            $filepath = trailingslashit($upload_dir['path']) . $filename;
            file_put_contents($filepath, $csv_content);

            // Prépare l'email à destination de l'admin du site.
            $to      = get_option('admin_email');
            $subject = __('Export des inscriptions InssetProjet', 'inssetprojet');
            $message = __('Veuillez trouver en pièce jointe le fichier CSV des inscriptions.', 'inssetprojet');
            // On laisse Brevo gérer l\'envoi via wp_mail, mais on force un From propre.
            $from_email = get_option('admin_email');
            $from_name  = get_bloginfo('name');
            $headers    = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . $from_name . ' <' . $from_email . '>'
            );

            wp_mail($to, $subject, $message, $headers, array($filepath));

            // Nettoyage du fichier temporaire.
            @unlink($filepath);
        }

        // Retour à la page de suivi avec un indicateur de succès.
        $redirect_url = add_query_arg(
            array(
                'page'   => 'inssetprojet_suivi',
                'export' => 1,
            ),
            admin_url('admin.php')
        );

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Suppression d'une inscription depuis la grille de suivi.
     */
    public static function delete_inscription() {
        if (!current_user_can('administrator')) {
            wp_die(__('Permissions insuffisantes', 'inssetprojet'));
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        // Nonce spécifique à la ligne (par ID).
        check_admin_referer('inssetprojet_delete_inscription_' . $id);

        if ($id > 0) {
            InssetProjet_Crud_Inscription::delete($id);
        }

        // Retour à la page de suivi.
        $redirect_url = add_query_arg(
            array(
                'page'    => 'inssetprojet_suivi',
                'deleted' => 1,
            ),
            admin_url('admin.php')
        );

        wp_safe_redirect($redirect_url);
        exit;
    }

}