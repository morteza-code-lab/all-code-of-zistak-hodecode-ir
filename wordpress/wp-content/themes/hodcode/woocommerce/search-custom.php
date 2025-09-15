<?php get_header(); ?>

<div class="search-page container flex gap-1">

    <!-- ستون راست: فیلترها -->
    <aside class="sidebar w-1/4 p-4 m-4 bg-white shadow rounded-xl">
        <h3 class="text-lg font-bold mb-4">فیلترها</h3>

        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="space-y-4 grid gap-6">

            <!-- نگه داشتن نوع پست و عبارت سرچ -->
            <input type="hidden" name="post_type" value="product">
            <input type="hidden" name="s" value="<?php echo get_search_query(); ?>">

            <!-- فیلتر قیمت -->
            <details class="border rounded p-2" <?php if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">قیمت</summary>
                <div>
                    <label>حداقل قیمت</label>
                    <input type="number" name="min_price" value="<?php echo esc_attr($_GET['min_price'] ?? ''); ?>" class="border rounded p-2 w-full">
                    <label>حداکثر قیمت</label>
                    <input type="number" name="max_price" value="<?php echo esc_attr($_GET['max_price'] ?? ''); ?>" class="border rounded p-2 w-full">
                </div>
            </details>

            <!-- فیلتر برند -->
            <details class="border rounded p-2" <?php if (!empty($_GET['filter_brand'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">برند</summary>
                <div>
                    <?php
                    $brands = get_terms('pa_brand');
                    if (!empty($brands)) :
                    ?>
                        <select name="filter_brand" class="border rounded p-2 w-full">
                            <option value="">انتخاب کنید</option>
                            <?php foreach ($brands as $brand) : ?>
                                <option value="<?php echo esc_attr($brand->slug); ?>" <?php selected($_GET['filter_brand'] ?? '', $brand->slug); ?>>
                                    <?php echo esc_html($brand->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </details>

            <!-- دسته بندی -->
            <details class="border rounded p-2" <?php if (!empty($_GET['product_cat'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">دسته بندی</summary>
                <div>
                    <?php wp_dropdown_categories(array(
                        'taxonomy'         => 'product_cat',
                        'name'             => 'product_cat',
                        'class'            => 'border rounded p-2 w-full',
                        'show_option_all'  => 'همه دسته‌ها',
                        'selected'         => $_GET['product_cat'] ?? 0,
                    )); ?>
                </div>
            </details>

            <!-- موجودی -->
            <details class="border rounded p-2" <?php if (!empty($_GET['stock_status'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">موجودی</summary>
                <div>
                    <select name="stock_status" class="border rounded p-2 w-full">
                        <option value="">همه</option>
                        <option value="instock" <?php selected($_GET['stock_status'] ?? '', 'instock'); ?>>موجود</option>
                        <option value="outofstock" <?php selected($_GET['stock_status'] ?? '', 'outofstock'); ?>>ناموجود</option>
                    </select>
                </div>
            </details>

            <!-- کشور سازنده -->
            <details class="border rounded p-2" <?php if (!empty($_GET['filter_country'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">کشور سازنده</summary>
                <div>
                    <?php
                    $countries = get_terms('pa_country');
                    if (!empty($countries)) :
                    ?>
                        <select name="filter_country" class="border rounded p-2 w-full">
                            <option value="">انتخاب کنید</option>
                            <?php foreach ($countries as $country) : ?>
                                <option value="<?php echo esc_attr($country->slug); ?>" <?php selected($_GET['filter_country'] ?? '', $country->slug); ?>>
                                    <?php echo esc_html($country->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </details>

            <!-- شکل محصول -->
            <details class="border rounded p-2" <?php if (!empty($_GET['filter_shape'])) echo 'open'; ?>>
                <summary class="cursor-pointer font-bold">شکل محصول</summary>
                <div>
                    <?php
                    $forms = get_terms('pa_shape');
                    if (!empty($forms)) :
                    ?>
                        <select name="filter_shape" class="border rounded p-2 w-full">
                            <option value="">انتخاب کنید</option>
                            <?php foreach ($forms as $form) : ?>
                                <option value="<?php echo esc_attr($form->slug); ?>" <?php selected($_GET['filter_shape'] ?? '', $form->slug); ?>>
                                    <?php echo esc_html($form->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </details>

            <button type="submit" class="bg-blue-600 text-white !mt-6 rounded w-full">اعمال فیلتر</button>
        </form>
    </aside>

    <!-- بدنه اصلی: نتایج محصولات -->
    <main class="content flex flex-col items-center w-3/4 p-4 gap-3">

        <?php do_action('hodcode_in_main_content'); ?>

        <!-- تیتر جستجو -->
        <div>
            <h2 class="search-title p-7 font-bold">
                نتایج جستجو برای: "<?php echo get_search_query(); ?>"
            </h2>
        </div>

        <?php if (have_posts()) : ?>
            <div class="products-grid grid grid-cols-3 gap-5">
                <?php while (have_posts()) : the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            </div>

            <div class="pagination mt-6 flex justify-center gap-2">
                <?php
                echo paginate_links(array(
                    'prev_text' => '<span class="prev-button">« قبلی</span>',
                    'next_text' => '<span class="next-button">بعدی »</span>',
                    'mid_size'  => 3,
                ));
                ?>
            </div>
        <?php else : ?>
            <h2>هیچ محصولی پیدا نشد.</h2>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>
