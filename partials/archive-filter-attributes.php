<?php
/**
 * Custom WooCommerce attribute filter form (no fe_widget).
 * Used in archive-product desktop filter bar and mobile modal.
 */
if (!defined('ABSPATH')) {
    exit;
}
$attribute_taxonomies = function_exists('wc_get_attribute_taxonomies') ? wc_get_attribute_taxonomies() : array();
// Get base URL - handle search page specially
if (is_search()) {
    $shop_page_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
    $base_url = $shop_page_id ? esc_url(get_permalink($shop_page_id)) : esc_url(home_url('/'));
    // Preserve search query in base URL
    if (!empty($_GET['s'])) {
        $base_url = add_query_arg('s', sanitize_text_field($_GET['s']), $base_url);
        $base_url = add_query_arg('post_type', 'product', $base_url);
    }
} else {
    $base_url = is_shop() ? wc_get_page_permalink('shop') : (is_tax() ? get_term_link(get_queried_object()) : '');
    if (is_wp_error($base_url)) {
        $base_url = wc_get_page_permalink('shop');
    }
}
// Get current category/taxonomy context
$current_term = get_queried_object();
$current_taxonomy = '';
$current_term_id = 0;
if ($current_term && isset($current_term->taxonomy)) {
    $current_taxonomy = $current_term->taxonomy;
    $current_term_id = $current_term->term_id;
}

// Get products in current category to filter attributes
$category_product_ids = array();
if ($current_taxonomy === 'product_cat' && $current_term_id) {
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $current_term_id,
            ),
        ),
    );
    $query = new WP_Query($args);
    $category_product_ids = $query->posts;
    wp_reset_postdata();
}

// Get price range from current category products or search results
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$price_range = array('min' => 0, 'max' => 0);

if (!empty($category_product_ids)) {
    // Category context: use category products
    global $wpdb;
    $price_query = "SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price, MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price 
                    FROM {$wpdb->postmeta} 
                    WHERE post_id IN (" . implode(',', array_map('intval', $category_product_ids)) . ") 
                    AND meta_key = '_price' 
                    AND meta_value != ''";
    $price_result = $wpdb->get_row($price_query);
    if ($price_result) {
        $price_range['min'] = floatval($price_result->min_price);
        $price_range['max'] = floatval($price_result->max_price);
    }
} elseif (is_search() && !empty($_GET['s'])) {
    // Search context: get price range from search results
    $search_query = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        's' => sanitize_text_field($_GET['s']),
        'posts_per_page' => -1,
        'fields' => 'ids',
    ));
    if (!empty($search_query->posts)) {
        global $wpdb;
        $price_query = "SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price, MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price 
                        FROM {$wpdb->postmeta} 
                        WHERE post_id IN (" . implode(',', array_map('intval', $search_query->posts)) . ") 
                        AND meta_key = '_price' 
                        AND meta_value != ''";
        $price_result = $wpdb->get_row($price_query);
        if ($price_result) {
            $price_range['min'] = floatval($price_result->min_price);
            $price_range['max'] = floatval($price_result->max_price);
        }
    }
    wp_reset_postdata();
}

// Fallback: get all products price range
if ($price_range['min'] == 0 && $price_range['max'] == 0) {
    $price_range['min'] = floatval(wc_get_price_min());
    $price_range['max'] = floatval(wc_get_price_max());
}

// Collect active filters for chips display
$active_filters = array();
$has_any_filter = false;

// Check attribute filters
foreach ($_GET as $k => $v) {
    if (strpos($k, 'pa_') === 0 && taxonomy_exists($k) && $v !== '') {
        $term = get_term_by('slug', sanitize_text_field($v), $k);
        if ($term && !is_wp_error($term)) {
            $attr = wc_get_attribute_taxonomy_by_name(str_replace('pa_', '', $k));
            $active_filters[] = array(
                'type' => 'attribute',
                'key' => $k,
                'label' => ($attr ? $attr->attribute_label : $k) . ': ' . $term->name,
                'value' => $v,
            );
            $has_any_filter = true;
        }
    }
}

// Check price filters
if ($min_price > 0 || $max_price > 0) {
    $price_label = '';
    if ($min_price > 0 && $max_price > 0) {
        $price_label = wc_price($min_price) . ' - ' . wc_price($max_price);
    } elseif ($min_price > 0) {
        $price_label = 'Nuo ' . wc_price($min_price);
    } elseif ($max_price > 0) {
        $price_label = 'Iki ' . wc_price($max_price);
    }
    if ($price_label) {
        $active_filters[] = array(
            'type' => 'price',
            'key' => 'price',
            'label' => 'Kaina: ' . $price_label,
            'value' => $min_price . '-' . $max_price,
        );
        $has_any_filter = true;
    }
}

// Get shop page URL for search redirect
$shop_page_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
$search_action = $shop_page_id ? esc_url(get_permalink($shop_page_id)) : esc_url(home_url('/'));
$current_search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
?>
<!-- Product Search Box -->

<!-- Active Filters Chips -->
<?php if (!empty($active_filters)): ?>
    <div class="archive-filter-chips">
        <div class="archive-filter-chips__list">
            <?php foreach ($active_filters as $filter): ?>
                <span class="archive-filter-chip" data-filter-type="<?php echo esc_attr($filter['type']); ?>"
                    data-filter-key="<?php echo esc_attr($filter['key']); ?>">
                    <span class="archive-filter-chip__label"><?php echo esc_html($filter['label']); ?></span>
                    <button type="button" class="archive-filter-chip__remove"
                        aria-label="<?php echo esc_attr__('Pašalinti filtrą', 'woocommerce'); ?>">
                        <svg xmlns="https://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 3L3 9M3 3l6 6"></path>
                        </svg>
                    </button>
                </span>
            <?php endforeach; ?>
        </div>
        <a href="<?php echo esc_url($base_url); ?>"
            class="archive-filter-chips__clear-all"><?php echo esc_html__('Išvalyti visus', 'woocommerce'); ?></a>
    </div>
<?php endif; ?>

<form method="get" class="archive-filter-form" action="<?php echo esc_url($base_url); ?>"
    data-base-url="<?php echo esc_url($base_url); ?>">
    <?php if (!empty($_GET['orderby'])): ?>
        <input type="hidden" name="orderby"
            value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['orderby']))); ?>">
    <?php endif; ?>
    <?php if (!empty($_GET['s'])): ?>
        <input type="hidden" name="s" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['s']))); ?>">
    <?php endif; ?>
    <?php if (!empty($_GET['post_type'])): ?>
        <input type="hidden" name="post_type"
            value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_GET['post_type']))); ?>">
    <?php endif; ?>
    <input type="hidden" name="paged" value="1">
    <?php
    // Only show attributes that have products in current category
    foreach ($attribute_taxonomies as $attr) {
        $taxonomy = 'pa_' . $attr->attribute_name;
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        // Get terms - if we have category products, only show terms used by those products
        $terms = array();
        if (!empty($category_product_ids)) {
            // Get terms that are actually assigned to products in this category
            $term_ids = wp_get_object_terms($category_product_ids, $taxonomy, array('fields' => 'ids'));
            if (!is_wp_error($term_ids) && !empty($term_ids)) {
                $terms = get_terms(array(
                    'taxonomy' => $taxonomy,
                    'include' => $term_ids,
                    'hide_empty' => false,
                ));
            }
        } else {
            // No category context - show all terms
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));
        }

        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }

        $current = isset($_GET[$taxonomy]) ? sanitize_text_field(wp_unslash($_GET[$taxonomy])) : '';
        $label = $attr->attribute_label ?: $attr->attribute_name;
        ?>
        <div class="archive-filter-form__field">
            <label
                for="filter-<?php echo esc_attr($taxonomy); ?>-<?php echo esc_attr(get_query_var('archive_filter_form_id', 'main')); ?>"
                class="archive-filter-form__label"><?php echo esc_html($label); ?></label>
            <select name="<?php echo esc_attr($taxonomy); ?>"
                id="filter-<?php echo esc_attr($taxonomy); ?>-<?php echo esc_attr(get_query_var('archive_filter_form_id', 'main')); ?>"
                class="archive-filter-form__select">
                <option value=""><?php echo esc_html__('Visi', 'woocommerce'); ?></option>
                <?php foreach ($terms as $term): ?>
                    <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current, $term->slug); ?>>
                        <?php echo esc_html($term->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php } ?>

    <!-- Price Filter -->
    <?php if ($price_range['max'] > $price_range['min']):
        // Format price for placeholder (plain text, no HTML)
        $min_price_placeholder = number_format($price_range['min'], 2, '.', '') . ' €';
        $max_price_placeholder = number_format($price_range['max'], 2, '.', '') . ' €';
        ?>
        <div class="archive-filter-form__field archive-filter-form__field--price">
            <label class="archive-filter-form__label"><?php echo esc_html__('Kaina', 'woocommerce'); ?></label>
            <div class="archive-filter-price">
                <input type="number" name="min_price" class="archive-filter-price__input"
                    placeholder="<?php echo esc_attr($min_price_placeholder); ?>"
                    min="<?php echo esc_attr($price_range['min']); ?>" max="<?php echo esc_attr($price_range['max']); ?>"
                    step="0.01" value="<?php echo $min_price > 0 ? esc_attr($min_price) : ''; ?>">
                <span class="archive-filter-price__separator">-</span>
                <input type="number" name="max_price" class="archive-filter-price__input"
                    placeholder="<?php echo esc_attr($max_price_placeholder); ?>"
                    min="<?php echo esc_attr($price_range['min']); ?>" max="<?php echo esc_attr($price_range['max']); ?>"
                    step="0.01" value="<?php echo $max_price > 0 ? esc_attr($max_price) : ''; ?>">
            </div>
        </div>
    <?php endif; ?>
    <div class="archive-filter-form__actions">
        <?php if ($has_any_filter): ?>
            <a href="<?php echo esc_url($base_url); ?>"
                class="archive-filter-form__clear"><?php echo esc_html__('Išvalyti visus', 'woocommerce'); ?></a>
        <?php endif; ?>
    </div>
</form>