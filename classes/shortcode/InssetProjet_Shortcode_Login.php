<?php
/**
 * Shortcode [INSSETPROJET_LOGIN] : box d'authentification étudiant (PS + mot de passe).
 */

class InssetProjet_Shortcode_Login {

    public static function display($atts) {
        InssetProjet_Session_Student::maybe_start();

        $logout_url = add_query_arg(
            array('action' => 'inssetprojet_student_logout'),
            admin_url('admin-post.php')
        );
        $logout_url = wp_nonce_url($logout_url, 'inssetprojet_student_logout');

        ob_start();

        if (InssetProjet_Session_Student::is_logged_in()) {
            ?>
            <div class="inssetprojet-login-box inssetprojet-logged-in">
                <p><?php esc_html_e('Vous êtes connecté.', 'inssetprojet'); ?></p>
                <p><a href="<?php echo esc_url($logout_url); ?>" class="inssetprojet-btn inssetprojet-btn-secondary"><?php esc_html_e('Se déconnecter', 'inssetprojet'); ?></a></p>
            </div>
            <?php
            return ob_get_clean();
        }

        $error = isset($_GET['inssetprojet_login_error']) ? sanitize_text_field($_GET['inssetprojet_login_error']) : '';
        $msg   = '';
        if ($error === 'nonce') {
            $msg = __('Erreur de sécurité. Réessayez.', 'inssetprojet');
        } elseif ($error === 'empty') {
            $msg = __('Identifiant et mot de passe requis.', 'inssetprojet');
        } elseif ($error === 'invalid') {
            $msg = __('Identifiant ou mot de passe incorrect.', 'inssetprojet');
        }

        $form_action = admin_url('admin-post.php');
        $nonce       = wp_nonce_field('inssetprojet_student_login', 'inssetprojet_student_login_nonce', true, false);
        ?>
        <div class="inssetprojet-login-box">
            <h2><?php esc_html_e('Connexion étudiant', 'inssetprojet'); ?></h2>
            <?php if ($msg) : ?>
                <p class="inssetprojet-login-error"><?php echo esc_html($msg); ?></p>
            <?php endif; ?>
            <form method="post" action="<?php echo esc_url($form_action); ?>" class="inssetprojet-form inssetprojet-form-login">
                <input type="hidden" name="action" value="inssetprojet_student_login" />
                <?php echo $nonce; ?>
                <p>
                    <label for="inssetprojet-student-login"><?php esc_html_e('Identifiant (PS)', 'inssetprojet'); ?></label>
                    <input type="text" id="inssetprojet-student-login" name="student_login" value="" required autocomplete="username" class="inssetprojet-input" />
                </p>
                <p>
                    <label for="inssetprojet-student-password"><?php esc_html_e('Mot de passe', 'inssetprojet'); ?></label>
                    <input type="password" id="inssetprojet-student-password" name="student_password" value="" required autocomplete="current-password" class="inssetprojet-input" />
                </p>
                <p>
                    <button type="submit" class="inssetprojet-btn inssetprojet-btn-primary"><?php esc_html_e('Se connecter', 'inssetprojet'); ?></button>
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
