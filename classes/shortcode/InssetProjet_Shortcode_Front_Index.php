<?php
class InssetProjet_Shortcode_Front_Index {


    public static function display($atts) {
        $nonce = wp_nonce_field('inssetprojet_inscription', 'inssetprojet_inscription_nonce', true, false);

        // Compteur du nombre total de demandes / inscriptions déjà enregistrées.
        $inssetprojet_count = 0;
        if (class_exists('InssetProjet_Crud_Inscription')) {
            $inssetprojet_inscriptions = InssetProjet_Crud_Inscription::get_all();
            if (is_array($inssetprojet_inscriptions)) {
                $inssetprojet_count = count($inssetprojet_inscriptions);
            }
        }

        ob_start();
        ?>
        <div class="inssetprojet-form-wrapper">
            <div class="inssetprojet-counter">
                <?php
                /* Traduit et gère singulier/pluriel. */
                printf(
                    esc_html(_n('%d demande', '%d demandes', $inssetprojet_count, 'inssetprojet')),
                    $inssetprojet_count
                );
                ?>
            </div>
            <form class="inssetprojet-form inssetprojet-form-inscription" action="#" method="post">

                <?php echo $nonce; ?>

                <fieldset class="inssetprojet-fieldset">
                    <legend class="inssetprojet-legend"><?php esc_html_e('Identité de l\'élève', 'inssetprojet'); ?></legend>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-civilite"><?php esc_html_e('Civilité', 'inssetprojet'); ?> *</label>
                        <select id="inssetprojet-civilite" name="user_civilite" required class="inssetprojet-input inssetprojet-select">
                            <option value=""><?php esc_html_e('Choisir...', 'inssetprojet'); ?></option>
                            <option value="Monsieur"><?php esc_html_e('Monsieur', 'inssetprojet'); ?></option>
                            <option value="Madame"><?php esc_html_e('Madame', 'inssetprojet'); ?></option>
                            <option value="Mademoiselle"><?php esc_html_e('Mademoiselle', 'inssetprojet'); ?></option>
                            <option value="Autre"><?php esc_html_e('Autre', 'inssetprojet'); ?></option>
                        </select>
                    </div>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-nom"><?php esc_html_e('Nom', 'inssetprojet'); ?> *</label>
                        <input type="text" id="inssetprojet-nom" name="user_nom" required minlength="2" maxlength="100" class="inssetprojet-input" placeholder="<?php esc_attr_e('Votre nom', 'inssetprojet'); ?>" />
                    </div>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-prenom"><?php esc_html_e('Prénom', 'inssetprojet'); ?> *</label>
                        <input type="text" id="inssetprojet-prenom" name="user_prenom" required minlength="2" maxlength="100" class="inssetprojet-input" placeholder="<?php esc_attr_e('Votre prénom', 'inssetprojet'); ?>" />
                    </div>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-date_naissance"><?php esc_html_e('Date de naissance', 'inssetprojet'); ?> *</label>
                        <input type="date" id="inssetprojet-date_naissance" name="user_birthday" required class="inssetprojet-input" />
                    </div>
                </fieldset>

                <fieldset class="inssetprojet-fieldset">
                    <legend class="inssetprojet-legend"><?php esc_html_e('Coordonnées', 'inssetprojet'); ?></legend>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-email"><?php esc_html_e('Email', 'inssetprojet'); ?> *</label>
                        <input type="email" id="inssetprojet-email" name="user_email" required class="inssetprojet-input" placeholder="nom@ecole.com" />
                    </div>

                    <div class="inssetprojet-form-group">
                        <label for="inssetprojet-tel"><?php esc_html_e('Téléphone', 'inssetprojet'); ?> *</label>
                        <input type="tel" id="inssetprojet-tel" name="user_phone" required maxlength="10" pattern="[0-9]{10}" class="inssetprojet-input" placeholder="0612345678" title="<?php esc_attr_e('10 chiffres', 'inssetprojet'); ?>" />
                        <span class="inssetprojet-hint"><?php esc_html_e('10 chiffres sans espace', 'inssetprojet'); ?></span>
                    </div>
                </fieldset>

                <div class="inssetprojet-form-actions">
                    <button type="submit" class="inssetprojet-btn inssetprojet-btn-primary"><?php esc_html_e('Valider l\'inscription', 'inssetprojet'); ?></button>
                </div>

                <div id="inssetprojet-loading" class="inssetprojet-loading" hidden aria-hidden="true"><?php esc_html_e('Chargement...', 'inssetprojet'); ?></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
