<?php
/**
 * Hodcode Theme Functions
 *
 * Theme setup, enqueue, helpers, and WooCommerce custom UI.
 */

defined('ABSPATH') || exit; // Prevent direct access

// ---------------------- Theme Setup ----------------------
if (!function_exists('hodcode_theme_setup')) {
    function hodcode_theme_setup()
    {
        // Add theme support for core WordPress features
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('custom-logo');

        // Add theme support for WooCommerce and its features
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

        // Define WooCommerce image sizes
        add_theme_support('woocommerce', [
            'thumbnail_image_width' => 350,
            'single_image_width' => 500,
        ]);

        // Register navigation menus
        register_nav_menus([
            'Header' => 'Header Menu',
        ]);
    }
}
add_action('after_setup_theme', 'hodcode_theme_setup');

// ---------------------- Customizer: Social Media Links ----------------------
add_action('customize_register', function ($wp_customize) {
    // Add a new section for social links
    $wp_customize->add_section('hodcode_social_links', [
        'title' => __('Social Media Links', 'hodcode'),
        'priority' => 30,
    ]);

    // Define social media fields
    $social_fields = ['linkedin', 'whatsapp', 'telegram'];
    foreach ($social_fields as $social) {
        $wp_customize->add_setting("hodcode_{$social}", [
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control("hodcode_{$social}", [
            'label' => ucfirst($social) . ' URL',
            'section' => 'hodcode_social_links',
            'type' => 'url',
        ]);
    }
});

// ---------------------- Enqueue Styles & Scripts ----------------------
if (!function_exists('hodcode_enqueue_scripts_styles')) {
    function hodcode_enqueue_scripts_styles()
    {
        // Enqueue main stylesheet and font
        wp_enqueue_style('hodcode-style', get_stylesheet_uri());
        wp_enqueue_style('hodcode-webfont', get_template_directory_uri() . '/assets/fontiran.css');

        // Enqueue Tailwind CDN script
        wp_enqueue_script(
            'tailwind',
            'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4',
            [],
            null,
            true // Load in footer for better performance
        );
        
        // Enqueue custom scripts
        wp_enqueue_script(
            'hero-banner',
            get_template_directory_uri() . '/js/hero-banner.js',
            [],
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'hodcode_enqueue_scripts_styles');


// ---------------------- Helpers ----------------------
/**
 * Convert English numerals to Persian numerals.
 * @param mixed $input
 * @return string
 */
function toPersianNumerals($input)
{
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    return str_replace($english, $persian, (string) $input);
}


// ---------------------- WooCommerce Base Tweaks ----------------------
add_filter('loop_shop_per_page', function($cols){
    return -1; // نشون دادن همه محصولات
}, 9999);


remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close');
add_filter('woocommerce_enqueue_styles', '__return_false');
remove_action('woocommerce_cart', 'woocommerce_cart');

// Remove default single product elements
add_action('wp', function () {
    if (!is_product()) return;

    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
}, 20);

// Restore only the Description tab on single product page
add_filter('woocommerce_product_tabs', 'hodcode_restore_description_tab', 1);
function hodcode_restore_description_tab($tabs)
{
    $tabs = [];
    $tabs['description'] = [
        'title' => __('توضیحات', 'woocommerce'),
        'priority' => 10,
        'callback' => 'woocommerce_product_description_tab'
    ];
    return $tabs;
}

// ---------------------- Search Form Functions ----------------------
// Modify search query to show 9 products per page
add_action('pre_get_posts', 'hodcode_custom_search_query');
function hodcode_custom_search_query($query)
{
    if (!is_admin() && $query->is_main_query() && is_search()) {
        $query->set('posts_per_page', 9);
    }
}

// Display search form in header on specific pages
add_action('hodcode_before_main', 'hodcode_header_search_form');
function hodcode_header_search_form()
{
    $allowed_page_slugs = ['search-page'];
    if (!is_search() && (is_front_page() || is_page($allowed_page_slugs))) {
?>
        <div class="header-search p-5 flex justify-center ">
            <form role="search" method="get" class="woocommerce-product-search flex w-full max-w-md gap-2" action="<?php echo home_url('/'); ?>">
                <input type="search" name="s" placeholder="جستجوی محصولات…" value="<?php echo get_search_query(); ?>" class="bg-white flex-1 px-4 py-2 border border-gray-300 rounded-md outline-none" />
                <input type="hidden" name="post_type" value="product" />
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-r-md hover:bg-green-700">🔍</button>
            </form>
        </div>
<?php
    }
}

// Display search form inside main content on search results page
add_action('hodcode_in_main_content', 'hodcode_main_search_form');
function hodcode_main_search_form()
{
    if (is_search()) {
?>
        <div class="search-inside-main p-5 flex justify-center">
            <form role="search" method="get" class="woocommerce-product-search flex w-full max-w-md gap-2" action="<?php echo home_url('/'); ?>">
                <input type="search" name="s" placeholder="جستجوی محصولات…" value="<?php echo get_search_query(); ?>" class="bg-white flex-1 px-4 py-2 border border-gray-300 rounded-md outline-none" />
                <input type="hidden" name="post_type" value="product" />
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-r-md hover:bg-green-700">🔍</button>
            </form>
        </div>
<?php
    }
}

add_action('woocommerce_product_query', 'hodcode_filter_products');
function hodcode_filter_products($q) {
    // قیمت
    if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
        $meta_query = $q->get('meta_query');
        $price_filter = ['key' => '_price', 'type' => 'NUMERIC'];

        if (!empty($_GET['min_price'])) {
            $price_filter['value'][] = (float) $_GET['min_price'];
            $price_filter['compare'] = '>=';
        }
        if (!empty($_GET['max_price'])) {
            $price_filter['value'] = (float) $_GET['max_price'];
            $price_filter['compare'] = '<=';
        }

        $meta_query[] = $price_filter;
        $q->set('meta_query', $meta_query);
    }

    // برند
    if (!empty($_GET['filter_brand'])) {
        $tax_query = $q->get('tax_query');
        $tax_query[] = [
            'taxonomy' => 'pa_brand',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['filter_brand']),
        ];
        $q->set('tax_query', $tax_query);
    }

    // دسته‌بندی
    if (!empty($_GET['product_cat'])) {
        $tax_query = $q->get('tax_query');
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'id',
            'terms'    => (int) $_GET['product_cat'],
        ];
        $q->set('tax_query', $tax_query);
    }

    // موجودی
    if (!empty($_GET['stock_status'])) {
        $meta_query = $q->get('meta_query');
        $meta_query[] = [
            'key'     => '_stock_status',
            'value'   => $_GET['stock_status'],
            'compare' => '=',
        ];
        $q->set('meta_query', $meta_query);
    }

    // کشور سازنده
    if (!empty($_GET['filter_country'])) {
        $tax_query = $q->get('tax_query');
        $tax_query[] = [
            'taxonomy' => 'pa_country',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['filter_country']),
        ];
        $q->set('tax_query', $tax_query);
    }

    // شکل محصول
    if (!empty($_GET['filter_shape'])) {
        $tax_query = $q->get('tax_query');
        $tax_query[] = [
            'taxonomy' => 'pa_shape',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['filter_shape']),
        ];
        $q->set('tax_query', $tax_query);
    }
}

// ---------------------- Related Products (as function) ----------------------
if (!function_exists('hodcode_render_related_aside')) {
    function hodcode_render_related_aside()
    {
        if (!is_product()) return;
        global $product;

        $related_ids = wc_get_related_products($product->get_id(), 3);

        if (!empty($related_ids)) : ?>
            <aside class="w-full lg:w-64 mt-2 lg:mt-0">
                <div class="bg-white rounded-xl shadow-sm p-3">
                    <h2 class="text-base font-bold mb-3">محصولات مشابه</h2>
                    <?php foreach ($related_ids as $index => $related_id) :
                        $related_product = wc_get_product($related_id); ?>
                        <div class="flex items-center gap-3 p-2">
                            <img src="<?php echo esc_url(get_the_post_thumbnail_url($related_id, 'thumbnail')); ?>" class="w-14 h-14 object-cover rounded" alt="<?php echo esc_attr($related_product->get_name()); ?>">
                            <p class="text-sm text-gray-700"><?php echo esc_html($related_product->get_name()); ?></p>
                        </div>
                        <?php if ($index < count($related_ids) - 1) : ?>
                            <hr class="border-gray-100">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </aside>
<?php endif;
    }
}

// ---------------------- Single Product: Custom Layout ----------------------
add_action('woocommerce_single_product_summary', function () {
    if (!is_product()) return;
    global $product; ?>
    <div class="max-w-screen-lg mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="mt-4 related.product">
            <?php hodcode_render_related_aside(); ?>
        </div>
        <div id="" class="mb-4 md:col-span-3">
            <div class="w-full bg-white rounded-xl shadow-md overflow-hidden pb-3 ">
                <div id="img" class="flex justify-center p-6 rounded">
                    <?php echo $product->get_image('large', [
                        'class' => 'max-w-[360px] w-auto h-auto object-contain rounded'
                    ]); ?>
                </div>
                <div class="flex flex-wrap justify-between items-center gap-3 py-3">
                    <h1 class="text-lg font-bold"><?php the_title(); ?></h1>
                    <div class="flex items-center gap-2">
                        <?php if ($product->is_on_sale() && $product->get_regular_price()) : ?>
                            <span class="discount-badge bg-red-600 text-white text-xs px-2 py-1 rounded-md">
                                <?php
                                $off = round(100 * ($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price());
                                echo toPersianNumerals($off) . '%';
                                ?>
                            </span>
                            <div class="old-price text-gray-400 line-through text-sm">
                                <span><?php echo wc_price($product->get_regular_price()); ?></span>
                            </div>
                            <div class="new-price text-lg font-bold text-red-600">
                                <span><?php echo wc_price($product->get_sale_price()); ?></span>
                            </div>
                        <?php else : ?>
                            <div class="normal-price text-lg font-bold text-gray-800">
                                <span><?php echo wc_price($product->get_price()); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="px-4 pb-4 text-gray-700 text-sm leading-relaxed">
                    <?php
                    $content = apply_filters('the_content', get_the_content());
                    $content_without_tags = wp_strip_all_tags($content);
                    $content_words = explode(' ', $content_without_tags);
                    $excerpt_words = array_slice($content_words, 0, 21);
                    $excerpt_text = implode(' ', $excerpt_words);
                    $rest_text = trim(str_replace($excerpt_text, '', $content_without_tags));
                    ?>
                    <div id="product-excerpt" class="mb-3">
                        <p><?php echo esc_html($excerpt_text); ?>...</p>
                    </div>
                    <div id="product-full-content" class="hidden text-justify">
                        <p><?php echo wp_kses_post($content); ?></p>
                    </div>
                    <?php if (!empty($rest_text)) : ?>
                        <input type="button" id="read-more-btn" value="ادامه توضیحات" class="cursor-pointer font-bold text-blue-600 mx-auto w-fit block pt-1 pb-1 pr-6 pl-6 rounded-lg" />
                    <?php endif; ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const excerpt = document.getElementById('product-excerpt');
                            const fullContent = document.getElementById('product-full-content');
                            const readMoreBtn = document.getElementById('read-more-btn');
                            if (readMoreBtn) {
                                readMoreBtn.addEventListener('click', function() {
                                    excerpt.style.display = 'none';
                                    fullContent.style.display = 'block';
                                    readMoreBtn.style.display = 'none';
                                });
                            }
                        });
                    </script>
                </div>
                <div class="flex justify-between items-center w-full">
                    <div class="flex gap-8 px-4 pb-6 bg-none">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                    <div class="inline px-4 pb-6">
                        <?php
                        $vendor_id = get_post_field('post_author', $product->get_id());
                        $chat_page = site_url("/vendor-chat?vendor_id={$vendor_id}&product_id={$product->get_id()}");
                        ?>
                        <a class="vendor-chat-button inline-block bg-blue-600 text-white font-bold px-4 py-2 rounded-lg hover:bg-blue-700 transition" href="<?php echo esc_url($chat_page); ?>">
                            💬 چت با فروشنده
                        </a>
                    </div>
                </div>
                <?php
                $attributes = $product->get_attributes();
                if (!empty($attributes)) : ?>
                    <div class="bg-white px-4 pb-4">
                        <h2 class="font-bold text-base mb-2">ویژگی‌ها</h2>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                            <?php
                            foreach ($attributes as $attribute) {
                                if ($attribute->get_variation()) continue;
                                $label = wc_attribute_label($attribute->get_name());
                                $values = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
                                if (!empty($values)) {
                                    echo '<li>' . esc_html($label . ': ' . implode('، ', $values)) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
}, 5);

// Override search template
add_filter('template_include', 'hodcode_custom_search_template', 99);
function hodcode_custom_search_template($template)
{
    if (is_search()) {
        $post_type = get_query_var('post_type');
        if ('product' === $post_type) {
            $new_template = locate_template(['woocommerce/search-custom.php']);
            if ($new_template) {
                return $new_template;
            }
        }
    }
    return $template;
}


// ---------------------- My Account Customizations ----------------------
// Add "My Account" link to header menu
add_filter('wp_nav_menu_items', 'hodcode_add_my_account_menu_link', 10, 2);
function hodcode_add_my_account_menu_link($items, $args)
{
    if ($args->theme_location == 'Header') {
        $my_account_page = get_permalink(get_option('woocommerce_myaccount_page_id'));
        if (is_user_logged_in()) {
            $items .= '<li class="menu-item my-account-menu"><a href="' . esc_url($my_account_page) . '">حساب من</a></li>';
        } else {
            $items .= '<li class="menu-item my-account-menu"><a href="' . esc_url($my_account_page) . '">ورود / ثبت نام</a></li>';
        }
    }
    return $items;
}

// Modify My Account menu items
add_filter('woocommerce_account_menu_items', 'hodcode_modify_account_menu_items');
function hodcode_modify_account_menu_items($items)
{
    unset($items['downloads']);
    unset($items['customer-logout']);
    $new_order = [
        'dashboard' => 'داشبورد',
        'orders' => 'سفارش‌های من',
        'edit-address' => 'آدرس‌ها',
        'edit-account' => 'جزئیات حساب',
        'deals' => 'تخفیف ها',
        'wallet' => 'کیف پول',
    ];
    return $new_order;
}

// Dashboard content
remove_action('woocommerce_account_dashboard', 'woocommerce_account_dashboard');
add_action('woocommerce_account_dashboard', 'hodcode_custom_dashboard_content');
function hodcode_custom_dashboard_content()
{
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name ?: $current_user->user_login;

    echo '<h2>خوش آمدید، ' . esc_html($user_name) . '!</h2>';
    echo '<p>این داشبورد خلاصه فعالیت‌های شما را نمایش می‌دهد.</p>';

    $orders_count = wc_get_customer_order_count($current_user->ID);
    echo '<p>تعداد سفارش‌های شما: ' . esc_html($orders_count) . '</p>';

    $wallet_balance = get_user_meta($current_user->ID, 'wallet_balance', true);
    if ($wallet_balance === '') $wallet_balance = 0;
    echo '<p>موجودی کیف پول: ' . esc_html($wallet_balance) . ' تومان</p>';

    echo '<p>برای مشاهده سفارش‌ها، به تب <a href="' . esc_url(wc_get_account_endpoint_url('orders')) . '">سفارش‌ها</a> بروید.</p>';
}

// Orders content
remove_action('woocommerce_account_orders_endpoint', 'woocommerce_account_orders', 10);
add_action('woocommerce_account_orders_endpoint', 'hodcode_custom_orders_content');
function hodcode_custom_orders_content()
{
    $current_user = wp_get_current_user();
    $customer_orders = wc_get_orders([
        'customer' => $current_user->ID,
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'status' => ['wc-pending', 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-cancelled', 'wc-refunded', 'wc-failed']
    ]);

    if (count($customer_orders) === 0) {
        echo '<p>شما هنوز سفارشی ثبت نکرده‌اید. <a href="' . esc_url(wc_get_page_permalink('shop')) . '">مرور محصولات</a></p>';
        return;
    }

    echo '<h2>سفارش‌های شما</h2>';
    echo '<table class="shop_table shop_table_responsive my_account_orders">';
    echo '<thead>
                <tr>
                    <th>شماره سفارش</th>
                    <th>تاریخ</th>
                    <th>وضعیت</th>
                    <th>مبلغ کل</th>
                    <th>جزئیات</th>
                </tr>
              </thead>
              <tbody>';

    foreach ($customer_orders as $order) {
        $order_id = $order->get_id();
        $order_date = wc_format_datetime($order->get_date_created());
        $order_status = wc_get_order_status_name($order->get_status());
        $order_total = $order->get_total();
        $order_currency = $order->get_currency();
        $view_url = $order->get_view_order_url();

        echo '<tr>
                    <td>#' . esc_html($order_id) . '</td>
                    <td>' . esc_html($order_date) . '</td>
                    <td>' . esc_html($order_status) . '</td>
                    <td>' . wc_price($order_total, ['currency' => $order_currency]) . '</td>
                    <td><a href="' . esc_url($view_url) . '">مشاهده</a></td>
                </tr>';
    }
    echo '</tbody></table>';
}

// Addresses content
add_action('woocommerce_account_edit-address', 'hodcode_custom_address_content');
add_action('woocommerce_account_edit-address_endpoint', 'hodcode_custom_address_endpoint_content');
function hodcode_custom_address_content()
{
    echo '<div class="custom-address">';
    echo '<p>آدرس‌ها و اطلاعات تحویل خودتو اینجا مدیریت کن.</p>';
    echo '</div>';
}
function hodcode_custom_address_endpoint_content()
{
    echo '<h2>آدرس‌های شما</h2>';
}

// Account details content
add_action('woocommerce_account_edit-account', 'hodcode_custom_account_details_content');
add_action('woocommerce_account_edit-account_endpoint', 'hodcode_custom_account_details_endpoint_content');
function hodcode_custom_account_details_content()
{
    echo '<div class="custom-account-details">';
    echo '<p>اطلاعات شخصی و رمز خودتو اینجا به‌روز کن.</p>';
    echo '</div>';
}
function hodcode_custom_account_details_endpoint_content()
{
    // You can add a shortcode or other content here if needed
}

// Custom endpoints
add_action('init', 'hodcode_add_custom_endpoints');
function hodcode_add_custom_endpoints()
{
    add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('wallet', EP_ROOT | EP_PAGES);
}


// creat product
// 1. اضافه کردن آیتم "ثبت محصول" به منوی حساب کاربری
add_filter( 'woocommerce_account_menu_items', 'hod_add_custom_account_menu_item' );
function hod_add_custom_account_menu_item( $items ) {
    $new_items = array();
    foreach ( $items as $key => $value ) {
        $new_items[ $key ] = $value;
        if ( $key === 'dashboard' ) { 
            $new_items['add_product'] = 'ثبت محصول';
        }
    }
    return $new_items;
}

// 2. محتوای صفحه فرم ثبت محصول
add_action( 'woocommerce_account_add_product_endpoint', 'hod_render_add_product_form' );
function hod_render_add_product_form() {
    if ( ! is_user_logged_in() ) {
        echo '<p>برای ثبت محصول باید وارد سایت شوید.</p>';
        return;
    }

    // بررسی ارسال فرم
    if ( isset($_POST['hod_submit_product']) ) {
        hod_handle_product_submission();
    }

    ?>
    <form method="post" enctype="multipart/form-data" class="form">
        <p><label>عنوان محصول <span style="color:red">*</span></label><br>
        <input type="text" name="product_title" required></p>

        <p><label>توضیحات بلند</label><br>
        <textarea name="product_content"></textarea></p>

        <p><label>توضیحات کوتاه</label><br>
        <textarea name="product_excerpt"></textarea></p>

        <p><label>قیمت عادی <span style="color:red">*</span></label><br>
        <input type="number" name="regular_price" step="0.01" required></p>

        <p><label>قیمت تخفیف</label><br>
        <input type="number" name="sale_price" step="0.01"></p>

        <p><label>دسته‌بندی‌ها</label><br>
        <?php
        $categories = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));
        foreach ( $categories as $cat ) {
            echo '<input type="checkbox" name="product_cats[]" value="'.esc_attr($cat->term_id).'"> '.esc_html($cat->name).'<br>';
        }
        ?>
        <br>
        <label>افزودن دسته جدید (در صورت عدم انتخاب دسته موجود)</label><br>
        <input type="text" name="new_category" placeholder="نام دسته جدید">
        </p>

        <p><label>تصاویر محصول <span style="color:red">*</span></label><br>
        <input type="file" name="product_images[]" multiple required></p>

        <p><label>برندها</label><br>
        <input type="text" name="product_brands"></p>

        <p><label>یادداشت خرید</label><br>
        <textarea name="purchase_note"></textarea></p>

        <p><label>نویسنده محصول <span style="color:red">*</span></label><br>
        <input type="text" name="product_author" required></p>

        <p><input type="submit" name="hod_submit_product" value="ثبت محصول"></p>
    </form>
    <?php
}

// 3. ثبت محصول در ووکامرس
function hod_handle_product_submission() {
    $user_id = get_current_user_id();

    $title = sanitize_text_field($_POST['product_title']);
    $content = sanitize_textarea_field($_POST['product_content']);
    $excerpt = sanitize_textarea_field($_POST['product_excerpt']);
    $regular_price = sanitize_text_field($_POST['regular_price']);
    $sale_price = sanitize_text_field($_POST['sale_price']);
    $author_name = sanitize_text_field($_POST['product_author']);

    // اعتبارسنجی الزامی‌ها
    if ( empty($title) || empty($author_name) || empty($regular_price) || empty($_FILES['product_images']['name'][0]) || (empty($content) && empty($excerpt)) ) {
        echo '<p style="color:red;">لطفا همه فیلدهای الزامی را پر کنید (عنوان، توضیحات بلند یا کوتاه، قیمت عادی، تصاویر و نویسنده محصول).</p>';
        return;
    }

    // ایجاد محصول
    $post_data = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
        'post_status' => 'publish',
        'post_type' => 'product',
        'post_author' => $user_id,
    );
    $product_id = wp_insert_post( $post_data );

    if ( $product_id ) {
        // قیمت
        update_post_meta($product_id, '_regular_price', $regular_price);
        update_post_meta($product_id, '_price', $regular_price);
        if ( !empty($sale_price) ) {
            update_post_meta($product_id, '_sale_price', $sale_price);
            update_post_meta($product_id, '_price', $sale_price);
        }

        // دسته‌بندی‌ها
        $cats = !empty($_POST['product_cats']) ? array_map('intval', $_POST['product_cats']) : array();
        if ( !empty($_POST['new_category']) ) {
            $new_cat_name = sanitize_text_field($_POST['new_category']);
            $new_cat_id = wp_insert_term($new_cat_name, 'product_cat');
            if (!is_wp_error($new_cat_id)) $cats[] = $new_cat_id['term_id'];
        }
        if (!empty($cats)) wp_set_object_terms($product_id, $cats, 'product_cat');

        // یادداشت خرید
        if ( !empty($_POST['purchase_note']) ) {
            update_post_meta($product_id, '_purchase_note', sanitize_textarea_field($_POST['purchase_note']));
        }

        // ویژگی‌ها: نویسنده محصول
        $taxonomy = 'pa_نویسنده_محصول';
        if ( !taxonomy_exists($taxonomy) ) {
            register_taxonomy(
                $taxonomy,
                'product',
                array(
                    'label' => 'نویسنده محصول',
                    'rewrite' => array( 'slug' => 'product-author' ),
                    'hierarchical' => false,
                )
            );
        }
        wp_set_object_terms( $product_id, $author_name, $taxonomy, true );

        // ویژگی‌ها: برندها
        if ( !empty($_POST['product_brands']) ) {
            $taxonomy = 'pa_برند';
            if ( !taxonomy_exists($taxonomy) ) {
                register_taxonomy(
                    $taxonomy,
                    'product',
                    array(
                        'label' => 'برند',
                        'rewrite' => array( 'slug' => 'product-brand' ),
                        'hierarchical' => false,
                    )
                );
            }
            wp_set_object_terms( $product_id, sanitize_text_field($_POST['product_brands']), $taxonomy, true );
        }

        // تصاویر محصول
        if ( !empty($_FILES['product_images']['name'][0]) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $files = $_FILES['product_images'];
            $attachments = array();
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );
                    $_FILES = array("upload_file" => $file);
                    foreach ($_FILES as $file => $array) {
                        $attach_id = media_handle_upload($file, $product_id);
                        if (!is_wp_error($attach_id)) $attachments[] = $attach_id;
                    }
                }
            }
            if (!empty($attachments)) {
                set_post_thumbnail($product_id, $attachments[0]);
                if (count($attachments) > 1) {
                    update_post_meta($product_id, '_product_image_gallery', implode(',', array_slice($attachments,1)));
                }
            }
        }

        echo '<p style="color:green;">محصول با موفقیت ثبت شد. <a href="'.get_permalink($product_id).'">مشاهده محصول</a></p>';
    } else {
        echo '<p style="color:red;">خطا در ثبت محصول. لطفا دوباره تلاش کنید.</p>';
    }
}

// 4. ثبت endpoint جدید برای ووکامرس
add_action( 'init', 'hod_add_product_endpoint' );
function hod_add_product_endpoint() {
    add_rewrite_endpoint( 'add_product', EP_PAGES );
}




// my products

// ==========================
// 1. اضافه کردن آیتم "محصولات من" به منوی حساب کاربری
// ==========================
add_filter( 'woocommerce_account_menu_items', function($items){
    $new_items = array();
    foreach ($items as $key=>$value){
        $new_items[$key] = $value;
        if ($key === 'dashboard'){
            $new_items['my_products'] = 'محصولات من';
        }
    }
    return $new_items;
});

// ==========================
// 2. ثبت endpointها
// ==========================
add_action('init', function(){
    add_rewrite_endpoint('my_products', EP_PAGES);
    add_rewrite_endpoint('edit_product', EP_PAGES);
});

// ==========================
// 3. نمایش لیست محصولات کاربر
// ==========================
add_action('woocommerce_account_my_products_endpoint','hod_render_my_products');
function hod_render_my_products(){
    if(!is_user_logged_in()){
        echo '<p>لطفا وارد شوید.</p>';
        return;
    }

    $args = [
        'post_type'=>'product',
        'author'=>get_current_user_id(),
        'posts_per_page'=>-1
    ];
    $products = get_posts($args);

    echo '<h2>محصولات من</h2>';
    if($products){
        echo '<table class="hod-my-products" style="width:100%; border-collapse:collapse">';
        echo '<tr><th>تصویر</th><th>عنوان</th><th>قیمت</th><th>موجودی</th><th>عملیات</th></tr>';
        foreach($products as $p){
            $product = wc_get_product($p->ID);
            $edit_url = wc_get_account_endpoint_url('edit_product').$p->ID;
            echo '<tr>';
            echo '<td>'.get_the_post_thumbnail($p->ID,[50,50]).'</td>';
            echo '<td>'.esc_html($p->post_title).'</td>';
            echo '<td>'.wc_price($product->get_price()).'</td>';
            echo '<td>'.($product->is_in_stock() ? 'موجود' : 'ناموجود').'</td>';
            echo '<td><a class="button" href="'.$edit_url.'">ویرایش</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>هیچ محصولی ثبت نکرده‌اید.</p>';
    }
}

// ==========================
// 4. صفحه ویرایش محصول
// ==========================
add_action('woocommerce_account_edit_product_endpoint','hod_render_edit_product');
function hod_render_edit_product($product_id){
    if(!is_user_logged_in()){
        echo '<p>برای ویرایش باید وارد شوید.</p>';
        return;
    }

    $product_id = absint($product_id);
    $product = wc_get_product($product_id);

    // بررسی اینکه محصول وجود داره و نویسنده‌اش همون کاربره
    if(!$product || get_post_field('post_author', $product_id) != get_current_user_id()){
        echo '<p style="color:red;">محصولی برای ویرایش یافت نشد.</p>';
        return;
    }

    // پر کردن داده‌ها با مدیریت خالی بودن
    $data = [
        'title'         => $product ? $product->get_name() : '',
        'content'       => get_post_field('post_content',$product_id) ?: '',
        'excerpt'       => get_post_field('post_excerpt',$product_id) ?: '',
        'regular_price' => $product && $product->get_regular_price() ? $product->get_regular_price() : '',
        'sale_price'    => $product && $product->get_sale_price() ? $product->get_sale_price() : '',
        'categories'    => taxonomy_exists('product_cat') ? wp_get_post_terms($product_id,'product_cat',['fields'=>'ids']) : [],
        'brands'        => taxonomy_exists('pa_برند') ? wp_get_post_terms($product_id,'pa_برند',['fields'=>'names']) : [],
        'purchase_note' => $product && $product->get_purchase_note() ? $product->get_purchase_note() : '',
        'author_name'   => taxonomy_exists('pa_نویسنده_محصول') ? wp_get_post_terms($product_id,'pa_نویسنده_محصول',['fields'=>'names']) : [],
        'stock_qty'     => $product && method_exists($product,'get_stock_quantity') ? $product->get_stock_quantity() : '',
    ];

    // پردازش فرم با try/catch برای جلوگیری از توقف PHP
    if(isset($_POST['hod_submit_product'])){
        try {
            hod_handle_product_submission($product_id);
        } catch(Exception $e){
            echo '<p style="color:red;">خطا هنگام ثبت محصول: '.$e->getMessage().'</p>';
        }
    }

    // نمایش فرم
    hod_render_product_form($data,$product_id);
}

// ==========================
// 5. فرم ثبت/ویرایش محصول
// ==========================
function hod_render_product_form($data=[],$product_id=0){ ?>
    <h2><?php echo $product_id ? 'ویرایش محصول' : 'ثبت محصول جدید'; ?></h2>
    <form method="post" enctype="multipart/form-data" style="position:relative; left:-40px; max-width:700px; margin:30px auto; padding:20px;  border-radius:10px;">
        <p>
            <label>عنوان محصول *</label><br>
            <input type="text" name="product_title" value="<?php echo esc_attr($data['title'] ?? ''); ?>" required>
        </p>
        <p>
            <label>توضیحات بلند</label><br>
            <textarea name="product_content"><?php echo esc_textarea($data['content'] ?? ''); ?></textarea>
        </p>
        <p>
            <label>توضیحات کوتاه</label><br>
            <textarea name="product_excerpt"><?php echo esc_textarea($data['excerpt'] ?? ''); ?></textarea>
        </p>
        <p>
            <label>قیمت عادی *</label><br>
            <input type="number" name="regular_price" step="0.01" value="<?php echo esc_attr($data['regular_price'] ?? ''); ?>" required>
        </p>
        <p>
            <label>قیمت تخفیف</label><br>
            <input type="number" name="sale_price" step="0.01" value="<?php echo esc_attr($data['sale_price'] ?? ''); ?>">
        </p>
        <p>
            <label>دسته‌بندی‌ها</label><br>
           <p class="category">
               <?php
                $cats = get_terms(['taxonomy'=>'product_cat','hide_empty'=>false]);
                if(!is_wp_error($cats)){
                    foreach($cats as $cat){
                        $checked = (!empty($data['categories']) && in_array($cat->term_id,$data['categories'])) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="product_cats[]" value="'.$cat->term_id.'" '.$checked.'> '.$cat->name.'</label><br>';
                    }
                }
                ?>
           </p>
           <br><input type="text" name="new_category" placeholder="نام دسته جدید">
        </p>
        <p>
            <label>موجودی</label><br>
            <input type="number" name="stock_qty" value="<?php echo esc_attr($data['stock_qty'] ?? ''); ?>">
        </p>
        <p>
            <label>تصاویر محصول <?php if(!$product_id) echo '*'; ?></label><br>
            <input type="file" name="product_images[]" multiple <?php if(!$product_id) echo 'required'; ?>>
        </p>
        <p>
            <label>برند</label><br>
            <input type="text" name="product_brands" value="<?php echo esc_attr(!empty($data['brands']) ? implode(', ',$data['brands']) : ''); ?>">
        </p>
        <p>
            <label>یادداشت خرید</label><br>
            <textarea name="purchase_note"><?php echo esc_textarea($data['purchase_note'] ?? ''); ?></textarea>
        </p>
        <p>
            <label>نویسنده محصول *</label><br>
            <input type="text" name="product_author" value="<?php echo esc_attr(!empty($data['author_name']) ? implode(', ',$data['author_name']) : ''); ?>" required>
        </p>
        <p>
            <input type="submit" name="hod_submit_product" value="<?php echo $product_id ? 'ذخیره تغییرات' : 'ثبت محصول'; ?>">
        </p>
    </form>
<?php }


// Wishlist content
add_action('woocommerce_account_wishlist_endpoint', 'hodcode_wishlist_content');
function hodcode_wishlist_content()
{
    echo '<h2>لیست علاقه‌مندی‌ها</h2>';
    echo '<p>اینجا می‌توانید محصولات مورد علاقه خود را ذخیره کنید.</p>';
}

// Wallet content
add_action('woocommerce_account_wallet_endpoint', 'hodcode_wallet_content');
function hodcode_wallet_content()
{
    $current_user_id = get_current_user_id();
    $orders = wc_get_orders([
        'customer_id' => $current_user_id,
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
    ]);
    $transactions = [];
    $balance = 0;

    foreach ($orders as $order) {
        $total = floatval($order->get_total());
        $status = $order->get_status();
        if ($status == 'completed') {
            $amount = -$total;
            $desc = 'خرید محصول (سفارش #' . $order->get_id() . ')';
        } else {
            $amount = 0;
            $desc = 'سفارش #' . $order->get_id() . ' در حال پردازش';
        }
        $transactions[] = [
            'date' => $order->get_date_created()->date('Y-m-d'),
            'desc' => $desc,
            'amount' => $amount,
        ];
    }

    $wallet_additions = get_user_meta($current_user_id, 'wallet_additions', true);
    if ($wallet_additions && is_array($wallet_additions)) {
        foreach ($wallet_additions as $tx) {
            $transactions[] = [
                'date' => $tx['date'],
                'desc' => $tx['desc'],
                'amount' => $tx['amount'],
            ];
        }
    }
    usort($transactions, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });

    echo '<h2>کیف پول شما</h2>';
    echo '<p>لیست تراکنش‌ها و مانده حساب شما:</p>';
    echo '<table class="wallet-table">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>شرح تراکنش</th>
                    <th>مقدار</th>
                    <th>مانده</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($transactions as $tx) {
        $balance += $tx['amount'];
        $amount_text = $tx['amount'] >= 0 ? '+' . number_format($tx['amount']) : number_format($tx['amount']);
        echo '<tr>
                <td>' . esc_html($tx['date']) . '</td>
                <td>' . esc_html($tx['desc']) . '</td>
                <td>' . $amount_text . ' تومان</td>
                <td>' . number_format($balance) . ' تومان</td>
            </tr>';
    }
    echo '</tbody></table>';
    echo '<p>مانده نهایی کیف پول شما: <strong>' . number_format($balance) . ' تومان</strong></p>';
}

// ---------------------- WooCommerce Checkout Fields Tweaks ----------------------
// Modify and remove checkout fields
add_filter('woocommerce_default_address_fields', 'hodcode_custom_address_fields', 20);
function hodcode_custom_address_fields($fields)
{
    if (isset($fields['city'])) {
        $fields['city']['placeholder'] = 'نام شهر خود را وارد نمایید';
    }
    return $fields;
}

add_filter('woocommerce_billing_fields', 'hodcode_custom_billing_fields', 20);
function hodcode_custom_billing_fields($fields)
{
    if (isset($fields['billing_last_name'])) {
        unset($fields['billing_last_name']);
    }
    if (isset($fields['billing_postcode'])) {
        $fields['billing_postcode']['required'] = false;
    }
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'hodcode_make_last_name_not_required');
function hodcode_make_last_name_not_required($fields)
{
    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['required'] = false;
    }
    return $fields;
}

add_filter('woocommerce_save_account_details_required_fields', 'hodcode_remove_last_name_from_required');
function hodcode_remove_last_name_from_required($required_fields)
{
    if (isset($required_fields['account_last_name'])) {
        unset($required_fields['account_last_name']);
    }
    return $required_fields;
}

add_action('woocommerce_checkout_update_user_meta', 'hodcode_set_last_name_from_first_name', 10, 2);
add_action('woocommerce_save_account_details', 'hodcode_set_last_name_from_first_name', 10, 2);
function hodcode_set_last_name_from_first_name($user_id, $posted = null)
{
    if (isset($_POST['billing_first_name'])) {
        update_user_meta($user_id, 'billing_last_name', sanitize_text_field($_POST['billing_first_name']));
    }
}

add_filter('woocommerce_shipping_fields', 'hodcode_custom_shipping_fields', 20, 1);
function hodcode_custom_shipping_fields($fields)
{
    if (isset($fields['shipping_address_1'])) {
        $fields['shipping_address_1']['label'] = 'آدرس خانه';
        $fields['shipping_address_1']['placeholder'] = 'آدرس منزل خود را وارد نمایید';
    }
    if (isset($fields['shipping_country'])) {
        $fields['shipping_country']['label'] = 'کشور';
        $fields['shipping_country']['placeholder'] = 'لطفاً کشور خود را انتخاب کنید';
    }
    if (isset($fields['shipping_state'])) {
        $fields['shipping_state']['label'] = 'استان';
        $fields['shipping_state']['placeholder'] = 'لطفاً استان خود را وارد نمایید';
    }
    return $fields;
}

// Remove billing address display from My Account page
add_filter('woocommerce_my_account_get_addresses', 'hodcode_remove_billing_address_display');
function hodcode_remove_billing_address_display($addresses)
{
    if (isset($addresses['billing'])) {
        unset($addresses['billing']);
    }
    return $addresses;
}

add_filter( 'woocommerce_shipping_fields', 'custom_shipping_contact_fields', 20, 1 );
        function custom_shipping_contact_fields( $fields ) {
            // اضافه کردن تلفن به آدرس حمل‌ونقل
            $fields['shipping_phone'] = array(
                'label'       => 'شماره تلفن گیرنده',
                'placeholder' => 'مثلاً: 09123456789',
                'required'    => true,
                'type'        => 'tel',
                'priority'    => 120,
            );

            return $fields;
        }


// ---------------------- Coupons Shortcode ----------------------
add_shortcode('show_coupons', 'hodcode_display_active_coupons');
function hodcode_display_active_coupons()
{
    if (!class_exists('WC_Coupon')) {
        return 'ووکامرس فعال نیست.';
    }

    $args = [
        'posts_per_page' => -1,
        'post_type' => 'shop_coupon',
        'post_status' => 'publish',
    ];
    $coupons = get_posts($args);

    if (empty($coupons)) {
        return '<p>کوپنی یافت نشد.</p>';
    }

    ob_start();
?>
    <div class="coupons-list">
        <?php
        foreach ($coupons as $coupon_post) {
            $coupon = new WC_Coupon($coupon_post->post_title);
            $expiry_date = $coupon->get_date_expires();
            if ($expiry_date && $expiry_date->getTimestamp() < time()) {
                continue;
            }
            $code = $coupon->get_code();
            $amount = $coupon->get_amount();
            $discount_type = $coupon->get_discount_type();
            $minimum_amount = $coupon->get_minimum_amount();
            $discount_text = '';
            switch ($discount_type) {
                case 'percent':
                    $discount_text = $amount . '% تخفیف';
                    break;
                case 'fixed_cart':
                    $discount_text = wc_price($amount) . ' تخفیف کل سفارش';
                    break;
                case 'fixed_product':
                    $discount_text = wc_price($amount) . ' تخفیف محصول';
                    break;
            }
            $condition_text = $minimum_amount ? ' برای سفارش بالای ' . wc_price($minimum_amount) : '';
            $expires_timestamp = $expiry_date ? $expiry_date->getTimestamp() + 86399 : null;
        ?>
            <div class="coupon-item">
                <h3>کد: <strong><?php echo esc_html($code); ?></strong></h3>
                <p class="discount-text"><?php echo $discount_text . $condition_text; ?></p>
                <button class="copy-btn" onclick="copyCoupon('<?php echo esc_js($code); ?>')">کپی کد</button>
                <?php if ($expires_timestamp) : ?>
                    <div class="countdown-timer" data-expire="<?php echo esc_attr($expires_timestamp); ?>">در حال بارگذاری...</div>
                <?php endif; ?>
            </div>
        <?php
        }
        ?>
    </div>
    <script>
        function copyCoupon(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('کد کوپن کپی شد: ' + code);
            }).catch(() => {
                alert('مشکلی در کپی کردن کد رخ داد');
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            const timers = document.querySelectorAll('.countdown-timer');
            timers.forEach(timer => {
                const expireTime = parseInt(timer.dataset.expire) * 1000;
                function updateCountdown() {
                    const now = new Date().getTime();
                    const diff = expireTime - now;
                    if (diff < 0) {
                        timer.innerHTML = '⛔ این کوپن منقضی شده';
                        timer.style.color = '#c0392b';
                        return;
                    }
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    let output = '🕒 باقی‌مانده: ';
                    output += `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    timer.innerHTML = output;
                }
                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        });
    </script>
<?php
    return ob_get_clean();
}

//---پیگیری سفارش---//
function custom_redirect_after_checkout($url, $order)
{
  // شناسهٔ سفارش رو از شیء $order دریافت می‌کنیم
  $order_id = $order->get_id();

  // آدرس صحیح برای مشاهدهٔ سفارش رو می‌سازیم
  // و شناسهٔ سفارش رو به انتهای اون اضافه می‌کنیم
  $redirect_url = 'http://zistak.hodecode.ir/my-account/view-order/' . $order_id;

  // اگر نیاز به اضافه کردن پارامترهای دیگر مثل کلید دارید، می‌تونید از add_query_arg استفاده کنید.
  // به عنوان مثال:
  // $order_key = $order->get_order_key();
  // $redirect_url = add_query_arg('key', $order_key, $redirect_url);

  return $redirect_url;
}

add_filter('woocommerce_get_checkout_order_received_url', 'custom_redirect_after_checkout', 10, 2);

add_filter('woocommerce_get_checkout_order_received_url', 'custom_redirect_after_checkout', 10, 2);

function custom_add_order_tracking_endpoint()
{
  add_rewrite_endpoint('order-tracking', EP_ROOT | EP_PAGES);
}
add_action('init', 'custom_add_order_tracking_endpoint');

// اضافه کردن به کوئری‌های ووکامرس
function custom_order_tracking_query_vars($vars)
{
  $vars[] = 'order-tracking';
  return $vars;
}
add_filter('query_vars', 'custom_order_tracking_query_vars', 0);

//---نمایش محتوا در endpoint---//
function custom_order_tracking_content()
{
  echo do_shortcode('[woocommerce_order_tracking]');
}
add_action('woocommerce_account_order-tracking_endpoint', 'custom_order_tracking_content');
//---اضافه کردن به منوی حساب کاربری---//
function custom_add_order_tracking_link_my_account($items)
{
  // محل قرارگیری رو انتخاب کن (اینجا بعد از سفارش‌ها)
  $new = array();

  foreach ($items as $key => $value) {
    $new[$key] = $value;
    if ($key === 'orders') {
      $new['order-tracking'] = 'پیگیری سفارش';
    }
  }

  return $new;
}
add_filter('woocommerce_account_menu_items', 'custom_add_order_tracking_link_my_account');

//---vendor contact---//
function vendor_chat_enqueue_scripts() {
    if ( is_page('vendor-chat') ) { // فقط صفحه چت
        wp_enqueue_script(
            'vendor-chat',
            get_stylesheet_directory_uri() . '/js/vendor-chat.js',
            array('jquery'),
            '1.0',
            true
        );
        wp_localize_script('vendor-chat', 'vendorChatAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('vendor_chat_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'vendor_chat_enqueue_scripts');

function send_vendor_chat_callback() {
    if ( !is_user_logged_in() ) wp_send_json_error('لطفاً وارد شوید');

    check_ajax_referer('vendor_chat_nonce', 'nonce');

    $vendor_id = intval($_POST['vendor_id']);
    $product_id = intval($_POST['product_id']);
    $user_id = get_current_user_id();
    $message = sanitize_textarea_field($_POST['message']);
    $attachment_url = '';

    if ( !empty($_FILES['attachment']['name']) ) {
        $uploaded = wp_handle_upload($_FILES['attachment'], ['test_form' => false]);
        if ( isset($uploaded['url']) ) $attachment_url = esc_url($uploaded['url']);
    }

    wp_send_json_success([
        'message'    => $message,
        'attachment' => $attachment_url,
        'name'       => wp_get_current_user()->display_name,
        'time'       => current_time('mysql')
    ]);
}
add_action('wp_ajax_send_vendor_chat', 'send_vendor_chat_callback');

add_action('wp_ajax_load_vendor_chat', 'load_vendor_chat_callback');
add_action('wp_ajax_nopriv_load_vendor_chat', 'load_vendor_chat_callback');

function load_vendor_chat_callback() {
    $vendor_id  = intval($_POST['vendor_id']);
    $product_id = intval($_POST['product_id']);
    $current_user = get_current_user_id();

    global $wpdb;
    $table_name = $wpdb->prefix . 'vendor_chat_messages';
    $messages = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE vendor_id=%d AND product_id=%d ORDER BY created_at ASC",
        $vendor_id, $product_id
    ));

    $data = [];
    foreach($messages as $msg){
        $data[] = [
            'user_id'    => $msg->user_id,
            'name'       => $msg->name,
            'message'    => $msg->message,
            'attachment' => $msg->attachment,
            'created_at' => $msg->created_at
        ];
    }

    wp_send_json_success($data + ['current_user'=>$current_user]);
}

//---سیاست حفظ حریم خصوصی---//
function custom_privacy_policy_text( $text ) {
    $custom_url = 'http://zistak.hodecode.ir/wordpress/privacy-policy/'; // لینک جدید خودتون رو اینجا بذارید
    $custom_text = 'سیاست حفظ حریم خصوصی'; // عنوان لینک رو اینجا بنویسید

    $text = sprintf(
        wp_kses(
            __( 'اطلاعات شخصی شما برای پردازش سفارش، پشتیبانی از تجربه شما در این وبسایت، و سایر اهداف ذکر شده در %s استفاده خواهد شد.', 'woocommerce' ),
            array(
                'a' => array(
                    'href' => array(),
                    'target' => array(),
                ),
            )
        ),
        '<a href="' . esc_url( $custom_url ) . '" target="_blank">' . esc_html( $custom_text ) . '</a>'
    );

    return $text;
}
add_filter( 'woocommerce_get_privacy_policy_text', 'custom_privacy_policy_text' );

//----responsive---//
add_action('wp_head', function () { ?>
    <style>
        /* برای موبایل */
        @media (max-width: 768px) {
            /* همه المان‌ها به صورت پیش‌فرض جمع بشن */
            body *:not(.grid):not(.flex) {
                max-width: 100% !important;
                width: auto !important;
                height: auto !important;
                box-sizing: border-box;
            }

            /* عکس‌ها */
            img, video, iframe {
                max-width: 100% !important;
                height: auto !important;
            }

            /* جدول‌ها */
            table {
                display: block;
                width: 100% !important;
                overflow-x: auto;
            }
        }
    </style>
<?php });

function hodcode_enqueue_scripts() {
    // فایل JS سفارشی‌تو لود کن
    wp_enqueue_script(
        'hodcode-responsive',
        get_stylesheet_directory_uri() . '/assets/js/responsive.js', // مسیر فایل JS
        array(), // وابستگی‌ها
        null,
        true // true یعنی قبل از بسته شدن </body> لود بشه
    );
}
add_action('wp_enqueue_scripts', 'hodcode_enqueue_scripts');