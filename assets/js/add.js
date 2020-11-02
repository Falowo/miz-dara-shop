$(function () {

    

    if (!($('#purchase_line_size').val())) {
        $('div#button  button.btn.btn-success').hide().attr('disabled', true);
        $('#purchase_line_tint').hide();
        $('#purchase_line_quantity').hide();
        
    }
    else if(!($('#purchase_line_tint').val())){
        $('div#button  button.btn.btn-success').hide().attr('disabled', true);
        $('#purchase_line_tint').focus();
    $('#purchase_line_quantity').hide();

    }
    else{
        $('#purchase_line_quantity').focus();
    }
    
   

    

    $(document).on('change', '#purchase_line_size, #purchase_line_tint', function () {
        let $field = $(this);
        let $form = $field.closest('form');
        let $sizeField = $('#purchase_line_size')
        let target = '#' + $field.attr('id').replace('tint', 'quantity').replace('size', 'tint');
        let data = {};
        data[$sizeField.attr('name')] = $sizeField.val();
        data[$field.attr('name')] = $field.val();

        console.log($('#purchase_line_size').val());
        console.log($('#purchase_line_tint').val());
        console.log($('#purchase_line_quantity').val());
        $.post($form.attr('action'), data).then(function (data) {
            let $input = $(data).find(target);
            $(target).replaceWith($input);

            $(target).show().focus();
            if ($('#purchase_line_size').val() && $('#purchase_line_tint').val()) {
                if(!($('#purchase_line_quantity').val())){
                    $('#purchase_line_quantity').val(1);
                }
                $('div#button  button.btn.btn-success').show().attr('disabled', false);
            }
        });

    });




});
