jQuery(document).ready(function($){
    $('#vendor-chat-form').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: vendorChatAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.success){
                    $('#vendor-chat-messages').append('<div class="vendor-chat-message"><strong>شما:</strong> '+$('textarea[name=message]').val()+'</div>');
                    $('#vendor-chat-form')[0].reset();
                } else {
                    alert(response.data);
                }
            }
        });
    });
});
