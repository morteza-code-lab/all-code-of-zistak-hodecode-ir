<?php
/* Template Name: صفحه سبد خرید */
get_header();
?>

<style>
    .cart-container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
        font-family: "IRANSans", Arial, sans-serif;
    }

    /* عنوان اصلی */
    .cart-container h1 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
        font-size: 28px;
        font-weight: bold;
    }

    /* کارت محصول */
    .cart-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        padding: 15px 20px;
        margin-bottom: 20px;
        transition: 0.3s;
    }

    .cart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .cart-card img {
        width: 80px;
        height: auto;
        border-radius: 8px;
        margin-left: 15px;
    }

    .cart-card .product-info {
        flex: 1;
    }

    .cart-card .product-info h2 {
        font-size: 18px;
        font-weight: bold;
        margin: 0;
        color: #222;
    }

    .cart-card .product-info .price {
        font-size: 16px;
        color: #28a745;
        /* سبز برای قیمت */
        margin-top: 5px;
    }

    .cart-card .actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cart-card .remove {
        font-size: 20px;
        color: #ff4d4d;
        text-decoration: none;
        transition: 0.3s;
    }

    .cart-card .remove:hover {
        color: #d93636;
    }

    /* دکمه تعداد */
    .qty-buttons {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .qty-buttons button {
        background: #f5f5f5;
        border: none;
        padding: 5px 12px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
    }

    .qty-buttons input {
        width: 50px;
        text-align: center;
        border: none;
        outline: none;
    }

    /* جمع کل */
    .cart-total-box {
        position: sticky;
        bottom: 0;
        background: #f0fdf4;
        border: 2px solid #28a745;
        border-radius: 12px;
        padding: 15px;
        margin-top: 30px;
        font-size: 20px;
        font-weight: bold;
        color: #28a745;
        text-align: center;
    }

    /* دکمه‌ها */
    .cart-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .cart-buttons button,
    .cart-buttons a {
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: 0.3s;
        font-size: 16px;
    }

    .cart-buttons button {
        background-color: #0073e6;
        color: #fff;
    }

    .cart-buttons button:hover {
        background-color: #005bb5;
    }

    .cart-buttons a {
        background-color: #ff7f50;
        /* نارنجی برای تسویه حساب */
        color: #fff;
    }

    .cart-buttons a:hover {
        background-color: #e5673e;
    }

    /* انیمیشن قیمت */
    .fade {
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0.3;
        }

        to {
            opacity: 1;
        }
    }

    /* موبایل */
    @media (max-width: 768px) {
        .cart-card {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .cart-card img {
            margin: 0 auto 15px auto;
        }

        .cart-card .actions {
            justify-content: center;
        }

        .cart-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .cart-buttons button,
        .cart-buttons a {
            width: 100%;
        }
    }
</style>

<div class="cart-container">
    <h1>سبد خرید شما</h1>

    <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>

        <form action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                $_product   = $cart_item['data'];
                $product_id = $cart_item['product_id'];
            ?>
                <div class="cart-card fade">
                    <?php echo $_product->get_image('thumbnail'); ?>
                    <div class="product-info">
                        <h2><?php echo $_product->get_name(); ?></h2>
                        <div class="price"><?php echo wc_price($_product->get_price()); ?></div>
                    </div>

                    <div class="actions">
                        <div class="qty-buttons">
                            <button type="button" class="qty-minus">-</button>
                            <?php
                            woocommerce_quantity_input(array(
                                'input_name'  => "cart[{$cart_item_key}][qty]",
                                'input_value' => $cart_item['quantity'],
                                'max_value'   => $_product->get_max_purchase_quantity(),
                                'min_value'   => 1,
                            ), $_product);
                            ?>
                            <button type="button" class="qty-plus">+</button>
                        </div>
                        <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="remove" aria-label="حذف محصول">
                            🗑
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total-box fade">
                جمع کل: <?php echo WC()->cart->get_cart_total(); ?>
            </div>

            <div class="cart-buttons">
                <button type="submit" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                    به‌روزرسانی سبد
                </button>
                <a href="<?php echo wc_get_checkout_url(); ?>">تسویه حساب</a>
            </div>

            <?php wp_nonce_field('woocommerce-cart'); ?>
        </form>

    <?php else : ?>
        <div class="empty-cart">
            سبد خرید شما خالی است.
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".qty-plus").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                let input = this.closest(".qty-buttons").querySelector("input.qty");
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event("change"));
                document.querySelector("button[name='update_cart']").click();
            });
        });

        document.querySelectorAll(".qty-minus").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                let input = this.closest(".qty-buttons").querySelector("input.qty");
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    input.dispatchEvent(new Event("change"));
                    document.querySelector("button[name='update_cart']").click();
                }
            });
        });
    });
</script>

<?php get_footer(); ?>