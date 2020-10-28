$(function(){

    $('#purchase_line_size').focus();
    $('#purchase_line_tint').hide();
    $('#purchase_line_quantity').hide();

    $(document).on('change', '#purchase_line_size, #purchase_line_tint', function(){
        let $field = $(this);
        let $form = $field.closest('form');
        let $sizeField = $('#purchase_line_size')
        let target = '#' + $field.attr('id').replace('tint', 'quantity').replace('size', 'tint');
        let data = {};
        data[$sizeField.attr('name')] = $sizeField.val();
        data[$field.attr('name')] = $field.val();
        $.post($form.attr('action'), data).then(function (data) {
           let $input = $(data).find(target);
           $(target).replaceWith($input);
        //    console.log($input);
        //    console.log(target);
            $($input).show().focus();    
        });
    
    });
});
