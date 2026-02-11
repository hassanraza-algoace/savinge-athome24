<ul class="menu list-group">
    <?php foreach ($args as $nav): ?>
    <li class="mb-2"><a class="text-white" href="<?php echo $nav->url; ?>"><?php echo $nav->title; ?></a></li>
    <?php endforeach;?>
</ul>