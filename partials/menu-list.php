<?php
$term = get_queried_object();

?>

<ul class="navbar-nav me-auto mb-2 mb-lg-0 gal-2 gap-lg-3 ps-2">
    <?php foreach ($args as $nav): ?>

    <?php
$class = '';
if (isset($_GET['akcijos']) && $nav->title == 'Akcijos') {
    if ($_GET['akcijos'] == true) {
        $class = 'active';
    }
}

if (isset($_GET['naujienos']) && $nav->title == 'Naujienos') {
    if ($_GET['naujienos'] == true) {
        $class = 'active';
    }
}

if ($term != null) {
    if ($term->name == $nav->title || $term->label == $nav->title) {
        $class = 'active';
    }
}
?>
    <li class="nav-item <?php echo $class . ' ' . sanitize_title($nav->title); ?>">
        <a class="nav-link" aria-current="page" href="<?php echo $nav->url; ?>"><?php echo $nav->title; ?></a>
    </li>
    <?php endforeach;?>
</ul>