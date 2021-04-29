$(function () {
    $('#registration_form_city').hide();
    $('label[for=registration_form_city]').hide();
    let country = $('#registration_form_country').val();
    $(document).on('change', '#registration_form_country', function () {
        let $field = $(this);
        let $form = $field.closest('form');
        let data = {};
        data[$field.attr('name')] = $field.val();
    
       

        $.post($form.attr('action'), data).then(function (data) {
            
            let $input = $(data).find('#registration_form_city');
            $('#registration_form_city').replaceWith($input);           
            $('#registration_form_city').show();            
            $('label[for=registration_form_city]').show();

            let $countryCode = $(data).find('#registration_form_phoneNumber_country');
            $('#registration_form_phoneNumber_country').replaceWith($countryCode);
            // $("#registration_form_phoneNumber_country").val(country).change();
        });
    });

    if(country !== null){
        $('#registration_form_city').show();
    $('label[for=registration_form_city]').show();
    }
});