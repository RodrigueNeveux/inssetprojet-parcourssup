jQuery(document).ready(function () {

    jQuery('#inssetprojet_param_update .button-primary').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var formData = new FormData();

        jQuery('#inssetprojet_param_update').find('input, textarea, select').each(function(){
            let id = jQuery(this).attr('id');
            if (typeof id !== "undefined") {

                if (jQuery(this).attr('type') === 'hidden') {
                    return;
                }

                if (jQuery(this).attr('type') === 'checkbox') {
                    formData.append(id, jQuery(this).prop('checked') ? 'true' : 'false');
                } else {
                    formData.append(id, jQuery(this).val());
                }
            }
        });

        formData.append('action', 'bisounours');
        formData.append('security', girafe.security);

        jQuery.ajax({
            async: true,
            url: girafe.ajax_url,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (rs) {
                console.log(Array.from(formData.entries()));
                jQuery('.update-message').removeClass('hide');
                return;
            },
            error: function(request, status, error) {
                console.error('Erreur AJAX InssetProjet :', status, error);
            }
        });
    });
});