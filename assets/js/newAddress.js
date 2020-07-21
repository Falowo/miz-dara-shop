$(document).ready(function () {
    $('#address_cityJsonId').hide();
    $('label[for=address_cityJsonId]').hide();
    $('#address_phoneNumber').hide();
    $('legend.col-form-label').hide();
    $(document).on('change', '#address_country', function () {
        let $field = $(this);
        let $form = $field.closest('form');
        let data = {};
        data[$field.attr('name')] = $field.val();



        $.post($form.attr('action'), data).then(function (data) {

            let $input = $(data).find('#address_cityJsonId');
            $('#address_cityJsonId').replaceWith($input);
            $('#address_cityJsonId').show();
            $('label[for=address_cityJsonId]').show();

            let $countryCode = $(data).find('#address_phoneNumber_country');
            $('#address_phoneNumber_country').replaceWith($countryCode);
            $('#address_phoneNumber').show();
            $('legend.col-form-label').show();

        });
    });


});