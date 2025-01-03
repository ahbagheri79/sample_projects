<?php
function custom_pagination_shortcode($atts) {
    // تنظیمات پیش‌فرض
    $atts = shortcode_atts(
        array(
            'taxonomy' => 'category', // نام دسته‌بندی پیش‌فرض
            'posts_per_page' => 10,   // تعداد محصولات در هر صفحه
        ),
        $atts
    );

    // دریافت اطلاعات کوئری فعلی
    global $wp_query;
    $paged = max(1, get_query_var('paged'));

    // تنظیم تعداد محصولات در هر صفحه
    $args = array(
        'post_type' => 'product', // نوع پست محصولات
        'posts_per_page' => $atts['posts_per_page'],
        'paged' => $paged,
        'tax_query' => array(
            array(
                'taxonomy' => $atts['taxonomy'],
                'field'    => 'slug',
                'terms'    => get_query_var('term'), // نام دسته فعلی
            ),
        ),
    );

    // کوئری جدید
    $query = new WP_Query($args);

    // تولید خروجی HTML
    $output = '<div class="custom-pagination">';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<div class="product-item">' . get_the_title() . '</div>';
    }
    $output .= '</div>';

    // صفحه‌بندی
    $big = 999999999; // عدد بزرگ برای جایگزینی در لینک‌ها
    $output .= paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '/page/%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'type'      => 'list',
        'prev_text' => __('« قبلی'),
        'next_text' => __('بعدی »'),
    ));

    // بازگرداندن نتایج
    wp_reset_postdata();
    return $output;
}
add_shortcode('custom_pagination', 'custom_pagination_shortcode');
