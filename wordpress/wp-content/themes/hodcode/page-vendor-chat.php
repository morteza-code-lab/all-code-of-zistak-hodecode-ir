<?php
/* Template Name: Vendor Chat */
get_header();

if ( !is_user_logged_in() ) {
    echo '<p>برای استفاده از چت، لطفاً وارد سایت شوید.</p>';
    get_footer();
    exit;
}

$vendor_id  = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ( !$vendor_id || !$product_id ) {
    echo '<p>اطلاعات محصول یا فروشنده معتبر نیست.</p>';
    get_footer();
    exit;
}
?>

<div id="chat-container" style="max-width:600px;margin:20px auto;border:1px solid black;border-radius:10px;display:flex;flex-direction:column;height:80vh;gap:6px">

    <!-- بخش پیام‌ها -->
    <div id="chat-messages" style="flex:1;overflow-y:auto;padding:15px;">
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'vendor_chat_messages';
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE vendor_id=%d AND product_id=%d ORDER BY created_at ASC",
            $vendor_id, $product_id
        ));

        foreach($messages as $msg){
            $align = ($msg->user_id == get_current_user_id()) ? 'right' : 'left';
            $bg = ($msg->user_id == get_current_user_id()) ? '#dcf8c6' : '#fff';

            echo '<div style="text-align:'.$align.';margin:5px;">';
            echo '<span style="background:'.$bg.';padding:8px 12px;border-radius:12px;display:inline-block;border:1px solid #ddd;">';
            echo esc_html($msg->message);
            if($msg->attachment) echo '<br><a href="'.esc_url($msg->attachment).'" target="_blank">📎 فایل پیوست</a>';
            echo '</span><br><small>'.esc_html($msg->created_at).'</small>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- فرم ارسال -->
    <form id="chat-form" enctype="multipart/form-data" style="display:flex;border-top:1px solid #ddd;margin:7px">
        <?php wp_nonce_field('vendor_chat_nonce','nonce'); ?>
        <input type="hidden" name="action" value="send_vendor_chat">
        <input type="hidden" name="vendor_id" value="<?php echo esc_attr($vendor_id); ?>">
        <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>">

        <input type="text" name="message" id="chat-input" placeholder="پیام خود را بنویسید..." style="flex:1;padding:10px;border:none;outline:none;background-color:white;border-radius:10px;" required>
        <input type="file" name="attachment" style="display:none;" id="chat-attachment">
        <button type="submit" style="padding:10px 20px;background:#0088cc;color:#fff;border:none;cursor:pointer;">ارسال</button>
    </form>
</div>

<?php get_footer(); ?>
