<?php
/**
 * Vue admin : grille de suivi des inscriptions.
 */

class InssetProjet_View_Suivi {

    public function display() {
        // Récupère les données pour l'affichage du tableau.
        $inscriptions       = InssetProjet_Crud_Inscription::get_all();
        // Récupère le nombre total via une méthode dédiée (avec transient).
        $inscriptions_count = InssetProjet_Crud_Inscription::get_count();

        // Formulaire d'export CSV de la grille de suivi (admin-post => réservé aux utilisateurs connectés).
        $export_action_url = admin_url('admin-post.php');
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Grille de suivi des inscriptions', 'inssetprojet'); ?></h1>

            <p>
                <?php
                printf(
                    esc_html(_n('%d inscription au total', '%d inscriptions au total', $inscriptions_count, 'inssetprojet')),
                    $inscriptions_count
                );
                ?>
            </p>

            <form method="post" action="<?php echo esc_url($export_action_url); ?>">
                <input type="hidden" name="action" value="inssetprojet_export_suivi" />
                <?php wp_nonce_field('inssetprojet_export_suivi'); ?>
                <button type="submit" class="button button-secondary">
                    <?php esc_html_e('Exporter le tableau en CSV', 'inssetprojet'); ?>
                </button>
            </form>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="column-id"><?php esc_html_e('ID', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-civilite"><?php esc_html_e('Civilité', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-nom"><?php esc_html_e('Nom', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-prenom"><?php esc_html_e('Prénom', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-birthday"><?php esc_html_e('Date de naissance', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-email"><?php esc_html_e('Email', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-phone"><?php esc_html_e('Téléphone', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-date"><?php esc_html_e('Date d\'inscription', 'inssetprojet'); ?></th>
                        <th scope="col" class="column-actions"><?php esc_html_e('Actions', 'inssetprojet'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inscriptions)) : ?>
                        <tr>
                            <td colspan="9"><?php esc_html_e('Aucune inscription pour le moment.', 'inssetprojet'); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($inscriptions as $row) : ?>
                            <tr>
                                <td class="column-id"><?php echo (int) $row['id']; ?></td>
                                <td class="column-civilite"><?php echo esc_html(isset($row['user_civilite']) ? $row['user_civilite'] : ''); ?></td>
                                <td class="column-nom"><?php echo esc_html($row['user_nom']); ?></td>
                                <td class="column-prenom"><?php echo esc_html($row['user_prenom']); ?></td>
                                <td class="column-birthday"><?php echo esc_html($row['user_birthday']); ?></td>
                                <td class="column-email"><?php echo esc_html($row['user_email']); ?></td>
                                <td class="column-phone"><?php echo esc_html($row['user_phone']); ?></td>
                                <td class="column-date"><?php echo esc_html($row['created_at']); ?></td>
                                <td class="column-actions">
                                    <?php
                                    $id          = (int) $row['id'];
                                    $delete_url  = admin_url('admin-post.php');
                                    $nonce_field = wp_nonce_field('inssetprojet_delete_inscription_' . $id, '_wpnonce', true, false);
                                    ?>
                                    <form method="post" action="<?php echo esc_url($delete_url); ?>" onsubmit="return confirm('<?php echo esc_js(__('Supprimer définitivement cette inscription ?', 'inssetprojet')); ?>');">
                                        <input type="hidden" name="action" value="inssetprojet_delete_inscription" />
                                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                        <?php echo $nonce_field; ?>
                                        <button type="submit" class="button-link delete">
                                            <span class="dashicons dashicons-trash"></span>
                                            <span class="screen-reader-text"><?php esc_html_e('Supprimer', 'inssetprojet'); ?></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
