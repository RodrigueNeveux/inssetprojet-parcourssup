<?php
/**
 * Template de la page de confirmation d'inscription.
 * URL : /inscription-confirmation
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="inssetprojet-confirmation-page" role="main">
    <div class="inssetprojet-confirmation-wrapper">
        <div class="inssetprojet-confirmation-box">
            <h1 class="inssetprojet-confirmation-title"><?php esc_html_e('Inscription enregistrée', 'inssetprojet'); ?></h1>
            <p class="inssetprojet-confirmation-message">
                <?php esc_html_e('Merci ! Votre inscription a bien été prise en compte. Nous vous recontacterons prochainement.', 'inssetprojet'); ?>
            </p>
            <p class="inssetprojet-confirmation-back">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inssetprojet-btn inssetprojet-btn-primary"><?php esc_html_e('Retour à l\'accueil', 'inssetprojet'); ?></a>
            </p>
        </div>
    </div>
</main>
<?php
get_footer();
