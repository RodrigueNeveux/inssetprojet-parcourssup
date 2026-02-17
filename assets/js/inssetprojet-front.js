jQuery(document).ready(function () {

    jQuery('form.inssetprojet-form-inscription').on('submit', function (e) {
        e.preventDefault();

        jQuery('#inssetprojet-loading').show();

        var formData = new FormData();
        formData.append('action', 'inssetprojet_inscription');
        formData.append('inssetprojet_inscription_nonce', inssetprojet_front.nonce);

        jQuery('form.inssetprojet-form-inscription').find('input, textarea, select').each(function () {
            var name = jQuery(this).attr('name');
            if (typeof name !== 'undefined' && name !== '' && name !== 'inssetprojet_inscription_nonce') {
                if (jQuery(this).attr('type') === 'checkbox') {
                    formData.append(name, jQuery(this).prop('checked') ? '1' : '0');
                } else {
                    formData.append(name, jQuery(this).val());
                }
            }
        });

        jQuery.ajax({
            url: inssetprojet_front.ajax_url,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'POST',
            success: function (rs) {
                if (rs.success) {
                    jQuery('form.inssetprojet-form-inscription')[0].reset();
                    if (inssetprojet_front.confirmation_url) {
                        window.location.href = inssetprojet_front.confirmation_url;
                    } else {
                        alert(rs.data && rs.data.message ? rs.data.message : 'Inscription enregistrée.');
                    }
                } else if (rs.data && rs.data.message) {
                    alert(rs.data.message);
                }
            },
            error: function () {
                alert('Une erreur est survenue.');
            },
            complete: function () {
                jQuery('#inssetprojet-loading').hide();
            }
        });

        return false;
    });

    jQuery('#inssetprojet-tel').on('input', function () {
        var valeur = jQuery(this).val();
        var chiffresSeulement = valeur.replace(/\D/g, '').slice(0, 10);
        jQuery(this).val(chiffresSeulement);
    });

});
