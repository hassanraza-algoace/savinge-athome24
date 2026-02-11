<?php
$menu_locations = get_nav_menu_locations();
$menu_nuorodos = isset($menu_locations['footer-links']) ? wp_get_nav_menu_items($menu_locations['footer-links']) : array();
$menu_kategorijos = isset($menu_locations['footer-client']) ? wp_get_nav_menu_items($menu_locations['footer-client']) : array();
$menu_informacija = isset($menu_locations['footer-company']) ? wp_get_nav_menu_items($menu_locations['footer-company']) : array();
$footer_bottom_menu = (isset($menu_locations['footer_bottom_menu']) && $menu_locations['footer_bottom_menu'])
    ? wp_get_nav_menu_items($menu_locations['footer_bottom_menu'])
    : array();
$logo_id = 41;
$logo_src = ($logo_id && wp_get_attachment_image_src($logo_id)) ? wp_get_attachment_image_src($logo_id)[0] : '';
?>
<footer class="footer">
    <div class="footer-container">
        <!-- Logo and Description Section -->
        <div class="footer-logo-section">
            <div class="footer-logo">
                <?php if ($logo_src): ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo-link">
                        <img src="<?php echo esc_url($logo_src); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                            class="footer-logo-icon" width="auto" height="auto">
                    </a>
                <?php else: ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo-link">
                        <div class="footer-logo-icon"></div>
                        <div class="footer-logo-text">atHome24</div>
                    </a>
                <?php endif; ?>
            </div>
            <p class="footer-description">
                Elektroninė parduotuvė, kurioje rasite platų pasirinkimą namams, buičiai, remontui, sodo darbams,
                automobilio priežiūrai skirtų prekių.
            </p>
            <div class="footer-contact">
                <div><a href="tel:+37060832314">+370 608 32 314</a></div>
                <div><a href="mailto:info@athome24.lt">info@athome24.lt</a></div>
            </div>
            <div class="footer-social">
                <a href="https://www.facebook.com/profile.php?id=61555944455435" target="_blank"
                    rel="noopener noreferrer" class="social-icon" aria-label="Facebook">
                    <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                </a>
                <a href="https://www.instagram.com/athome24.lt/" target="_blank" rel="noopener noreferrer"
                    class="social-icon" aria-label="Instagram">
                    <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Nuorodos Section -->
        <div class="footer-section">
            <h3>Nuorodos</h3>
            <div class="footer-section-header" onclick="toggleFooterSection(this)">
                <h3>Nuorodos</h3>
                <svg class="dropdown-arrow" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
                </svg>
            </div>
            <div class="footer-section-content">
                <ul>
                    <?php
                    if (!empty($menu_nuorodos)) {
                        foreach ($menu_nuorodos as $item) {
                            echo '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a></li>';
                        }
                    } else {
                        ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Mano paskyra</a></li>
                        <li><a href="<?php echo esc_url(home_url('/wishlist')); ?>">Mano pamėgtos prekės</a></li>
                        <li><a href="<?php echo esc_url(get_page_link(123)); ?>">Kontaktai</a></li>
                        <li><a href="<?php echo esc_url(home_url('/duk')); ?>">D.U.K</a></li>
                        <li><a href="<?php echo esc_url(get_post_type_archive_link('patarimai')); ?>">Naudingi Patarimai</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Prekių kategorijos Section -->
        <div class="footer-section">
            <h3>Prekių kategorijos</h3>
            <div class="footer-section-header" onclick="toggleFooterSection(this)">
                <h3>Prekių kategorijos</h3>
                <svg class="dropdown-arrow" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
                </svg>
            </div>
            <div class="footer-section-content">
                <ul>
                    <?php
                    if (!empty($menu_kategorijos)) {
                        foreach ($menu_kategorijos as $item) {
                            echo '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a></li>';
                        }
                    } else {
                        ?>
                        <li><a href="<?php echo esc_url(home_url('/shop')); ?>">Akcijos</a></li>
                        <li><a href="<?php echo esc_url(home_url('/product-category/naujienos')); ?>">Naujienos</a></li>
                        <li><a href="<?php echo esc_url(home_url('/shop')); ?>">Populiariausi</a></li>
                        <li><a href="<?php echo esc_url(home_url('/product-category/remontas')); ?>">Remonto darbams</a>
                        </li>
                        <li><a href="<?php echo esc_url(home_url('/product-category/namams')); ?>">Namams ir sau</a></li>
                        <li><a href="<?php echo esc_url(home_url('/product-category/automobiliai')); ?>">Automobilių
                                priežiūrai</a></li>
                        <li><a href="<?php echo esc_url(home_url('/product-category/sodas')); ?>">Sodui ir daržui</a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Informacija Section -->
        <div class="footer-section">
            <h3>Informacija</h3>
            <div class="footer-section-header" onclick="toggleFooterSection(this)">
                <h3>Informacija</h3>
                <svg class="dropdown-arrow" xmlns="https://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z" />
                </svg>
            </div>
            <div class="footer-section-content">
                <ul>
                    <?php
                    if (!empty($menu_informacija)) {
                        foreach ($menu_informacija as $item) {
                            echo '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a></li>';
                        }
                    } else {
                        ?>
                        <li><a href="<?php echo esc_url(home_url('/apie-mus')); ?>">Apie mus</a></li>
                        <li><a href="<?php echo esc_url(home_url('/prekiu-pirkimo-pardavimo-taisykles')); ?>">Prekių pirkimo
                                - pardavimo taisyklės</a></li>
                        <li><a href="<?php echo esc_url(home_url('/pristatymas')); ?>">Prekių pristatymas ir atsiėmimas</a>
                        </li>
                        <li><a href="<?php echo esc_url(home_url('/garantija')); ?>">Garantinis aptarnavimas ir prekių
                                grąžinimas</a></li>
                        <li><a href="<?php echo esc_url(get_privacy_policy_url() ?: home_url('/privatumo-politika')); ?>">Privatumo
                                politika</a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer_section_content">
            <ul>
                <?php
                if (!empty($footer_bottom_menu)) {
                    foreach ($footer_bottom_menu as $item) {
                        echo '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a></li>';
                    }
                } else {
                    ?>
                    <li><a href="<?php echo esc_url(get_privacy_policy_url() ?: '#'); ?>">Privatumo politika</a></li>
                    <li><a href="https://ikiwi.lt/" target="_blank" rel="noopener">Sprendimas iKiwi.lt</a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
        AtHome24.lt © <?php echo esc_html(date('Y')); ?> Visos teisės saugomos
    </div>
</footer>

</main>
</div>
<?php wp_footer(); ?>
</body>

</html>