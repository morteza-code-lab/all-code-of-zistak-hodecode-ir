jQuery(document).ready(function($){
    var $form = $('#chat-form');
    var $messages = $('#chat-messages');
    var $input = $('#chat-input');

    $form.on('submit', function(e){
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: vendorChatAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                if(res.success){
                    // اضافه کردن پیام جدید به انتهای چت
                    var align = 'right';
                    var bg = '#dcf8c6';
                    var html = '<div style="text-align:'+align+';margin:5px;">';
                    html += '<span style="background:'+bg+';padding:8px 12px;border-radius:12px;display:inline-block;border:1px solid #ddd;">';
                    html += res.data.message;
                    if(res.data.attachment) html += '<br><a href="'+res.data.attachment+'" target="_blank">📎 فایل پیوست</a>';
                    html += '</span><br><small>'+res.data.time+'</small></div>';

                    $messages.append(html);
                    $messages.scrollTop($messages[0].scrollHeight); // اسکرول به آخر

                    $input.val('');
                    $form[0].reset();
                } else {
                    alert(res.data);
                }
            }
        });
    });

    // بارگذاری خودکار پیام‌ها هر 5 ثانیه
    setInterval(function(){
        $.ajax({
            url: vendorChatAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_vendor_chat',
                vendor_id: $form.find('input[name=vendor_id]').val(),
                product_id: $form.find('input[name=product_id]').val()
            },
        });
    }, 5000);
});
