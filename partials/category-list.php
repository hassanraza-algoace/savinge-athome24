<?php
$term = get_queried_object();
$current_catid = ($term && isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy === 'product_cat') ? $term->term_id : 0;

// Get category hierarchy
$ancestor_ids = $term ? array_reverse(get_ancestors($current_catid, 'product_cat')) : [];
$active_ids = $term ? array_merge([$current_catid], $ancestor_ids) : [];

// Build filter URL parameters
$filters = [];
$filter_params = ['akcijos', 'naujienos', 'prekes-zenklas-filter', 
                'medziagiskumas-filter', 'matmenys-filter', 'svoris-filter'];

foreach ($filter_params as $param) {
    if (!empty($_GET[$param])) {
        $filters[] = esc_attr($param) . '=' . esc_attr($_GET[$param]);
    }
}
$filter_query = $filters ? '?' . implode('&', $filters) : '';

// Get top-level categories
$top_level_categories = get_categories([
    'taxonomy' => 'product_cat',
    'orderby' => 'name',
    'hide_empty' => false,
    'pad_counts' => true,
    'parent' => 0
]);



// Exclude specific categories by name
$excluded_names = array();
$top_level_categories = array_filter($top_level_categories, function($cat) use ($excluded_names) {
    return !in_array(trim($cat->name), $excluded_names);
});

// Define recursive function with proper exclusion
if (!function_exists('build_category_tree')) {
    function build_category_tree($parent_id, $active_ids, $filter_query, $current_catid, $parent_collapse_id, $depth = 1) {
        $categories = get_categories([
            'taxonomy' => 'product_cat',
            'parent' => $parent_id,
            'hide_empty' => false,
            'pad_counts' => true,
            'orderby' => 'name'
        ]);
        
        // Filter out excluded categories
        $excluded_names = array();
        $categories = array_filter($categories, function($cat) use ($excluded_names) {
            return !in_array(
                mb_strtolower(trim($cat->name)), 
                array_map('mb_strtolower', $excluded_names)
            );
        });
        
        if (empty($categories)) return;
        
        foreach ($categories as $cat) {
            $is_active = in_array($cat->term_id, $active_ids);
            $term_link = get_term_link($cat);
            
            if (is_wp_error($term_link)) continue;
            
            // Check if category has children
            $children = get_categories([
                'taxonomy' => 'product_cat',
                'parent' => $cat->term_id,
                'hide_empty' => false,
                'number' => 1
            ]);
            
            $show_dropdown = !empty($children) && $depth <= 2;
            ?>
            
            <div class="accordion <?php echo ($depth > 1) ? 'ps-3' : ''; ?>">
                <div class="accordion-item border-0">
                    <div class="d-flex justify-content-between align-items-center w-100 position-relative">
                        <!-- Clickable category name -->
                        <a href="<?php echo esc_url($term_link . $filter_query); ?>" 
                           class="text-decoration-none flex-grow-1 <?php echo $is_active ? 'fw-bold' : ''; ?>"
                           style="z-index: 2;">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                        
                        <!-- Modified dropdown toggle to match old style -->
                        <?php if ($show_dropdown) : ?>
                        <h2 class="accordion-header m-0">
                            <button class="accordion-button p-1 bg-transparent shadow-none" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseCat<?php echo $cat->term_id; ?>"
                                    aria-expanded="<?php echo $is_active ? 'true' : 'false' ?>">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h2>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($show_dropdown) : ?>
                        <div id="collapseCat<?php echo $cat->term_id; ?>" 
                             class="accordion-collapse collapse <?php echo $is_active ? 'show' : ''; ?>" 
                             data-bs-parent="#<?php echo $parent_collapse_id; ?>">
                            <div class="accordion-body p-0">
                                <?php build_category_tree(
                                    $cat->term_id, 
                                    $active_ids, 
                                    $filter_query, 
                                    $current_catid,
                                    'collapseCat' . $cat->term_id,
                                    $depth + 1
                                ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
}
?>

<div class="accordion p-3 p-sm-0" id="mainNavigation">
    <?php foreach ($top_level_categories as $top_cat) : 
        $term_link = get_term_link($top_cat);
        if (is_wp_error($term_link)) continue;

        $children = get_categories([
            'taxonomy' => 'product_cat',
            'parent' => $top_cat->term_id,
            'hide_empty' => false,
            'number' => 1
        ]);
        $has_children = !empty($children);
        
        if (!$has_children && $top_cat->count < 1) continue;
        
        $is_active = in_array($top_cat->term_id, $active_ids);
        ?>
        
        <div class="accordion-item">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a href="<?= esc_url($term_link . $filter_query) ?>" 
                   class="text-decoration-none flex-grow-1 <?= $is_active ? 'fw-bold' : '' ?>">
                    <?= esc_html($top_cat->name) ?>
                </a>
                <?php if ($has_children) : ?>
                <h2 class="accordion-header m-0">
                    <button class="accordion-button p-1 bg-transparent shadow-none <?= $is_active ? '' : 'collapsed' ?>" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapseTop<?= $top_cat->term_id ?>">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </h2>
                <?php endif; ?>
            </div>
            <?php if ($has_children) : ?>
                <div id="collapseTop<?= $top_cat->term_id ?>" 
                     class="accordion-collapse collapse <?= $is_active ? 'show' : '' ?>" 
                     data-bs-parent="#mainNavigation">
                    <div class="accordion-body p-2">
                        <?php build_category_tree(
                            $top_cat->term_id, 
                            $active_ids, 
                            $filter_query, 
                            $current_catid,
                            'collapseTop' . $top_cat->term_id
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>