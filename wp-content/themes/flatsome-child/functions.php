<?php
// Add custom Theme Functions here.

add_action('after_setup_theme', 'onthread_layout_hooks', 20);
add_action('wp_enqueue_scripts', 'onthread_enqueue_child_styles', 101);
add_action('init', 'onthread_use_collections_category_base', 0);
add_action('init', 'onthread_add_collections_rewrite_rules', 9);
add_action('init', 'onthread_add_auth_rewrite_rules', 9);
add_action('init', 'onthread_seed_primary_menu', 20);
add_action('init', 'onthread_seed_blog_content', 30);
add_action('init', 'onthread_maybe_flush_rewrite_rules', 99);
add_filter('query_vars', 'onthread_auth_query_vars');
add_filter('woocommerce_taxonomy_args_product_cat', 'onthread_flat_product_category_rewrite');
add_filter('term_link', 'onthread_flat_product_category_link', 10, 3);
add_filter('woocommerce_account_menu_items', 'onthread_account_menu_items', 20);
add_filter('woocommerce_logout_default_redirect_url', 'onthread_logout_redirect_url');
add_filter('loop_shop_per_page', 'onthread_collection_products_per_page', 20);
add_filter('nav_menu_css_class', 'onthread_primary_menu_item_classes', 10, 4);
add_filter('nav_menu_link_attributes', 'onthread_primary_menu_link_attributes', 10, 4);
add_filter('document_title_parts', 'onthread_custom_page_title_parts', 20);
add_filter('wpseo_title', 'onthread_custom_page_wpseo_title', 20);
add_filter('wpseo_opengraph_title', 'onthread_custom_page_wpseo_title', 20);
add_filter('template_include', 'onthread_contact_page_template', 99);
add_action('pre_get_posts', 'onthread_collection_archive_query', 20);
add_action('template_redirect', 'onthread_handle_auth_actions', 0);
add_action('template_redirect', 'onthread_render_auth_pages', 0);
add_action('template_redirect', 'onthread_redirect_account_dashboard', 2);
add_action('template_redirect', 'onthread_redirect_product_category_urls', 1);
add_action('flatsome_after_sidebar_menu_elements', 'onthread_mobile_sidebar_footer', 5);
add_action('wp_footer', 'onthread_logout_modal');
add_filter('theme_mod_disable_quick_view', '__return_true', 20);
add_filter('theme_mod_add_to_cart_icon', 'onthread_disable_product_card_add_to_cart_icon', 20);
add_filter('woocommerce_loop_add_to_cart_link', 'onthread_hide_product_card_add_to_cart_link', 99, 3);

function onthread_layout_hooks()
{
	remove_action('flatsome_footer', 'flatsome_page_footer', 10);
	remove_action('flatsome_footer', 'flatsome_go_to_top');
	remove_action('flatsome_product_box_actions', 'flatsome_product_box_actions_add_to_cart', 1);
	remove_action('flatsome_product_box_actions', 'flatsome_lightbox_button', 50);
	remove_action('flatsome_product_box_after', 'flatsome_woocommerce_shop_loop_button', 100);

	add_action('flatsome_footer', 'onthread_footer', 10);
}

function onthread_disable_product_card_add_to_cart_icon()
{
	return 'disabled';
}

function onthread_hide_product_card_add_to_cart_link($link, $product, $args)
{
	if (is_product()) {
		return $link;
	}

	return '';
}

function onthread_product_card_category($product)
{
	if (!$product instanceof WC_Product) {
		return array(
			'name' => 'Products',
			'url' => '',
		);
	}

	$terms = get_the_terms($product->get_id(), 'product_cat');

	if (!$terms || is_wp_error($terms)) {
		return array(
			'name' => 'Products',
			'url' => '',
		);
	}

	$term = reset($terms);
	$url = get_term_link($term);

	return array(
		'name' => $term->name,
		'url' => is_wp_error($url) ? '' : $url,
	);
}

function onthread_product_card_excerpt($product, $words = 20)
{
	if (!$product instanceof WC_Product) {
		return '';
	}

	$text = $product->get_short_description();

	if (!$text) {
		$text = get_post_field('post_excerpt', $product->get_id());
	}

	if (!$text) {
		$text = get_post_field('post_content', $product->get_id());
	}

	$text = wp_strip_all_tags($text);

	return $text ? wp_trim_words($text, $words, '...') : '';
}

function onthread_blog_asset($path)
{
	return content_url('uploads/' . ltrim($path, '/'));
}

function onthread_seed_blog_categories()
{
	return array(
		'news' => array(
			'name' => 'News',
			'description' => 'Updates and helpful notes from OnThread.',
		),
		'discounts' => array(
			'name' => 'Discounts',
			'description' => 'Promotions, savings, and limited-time offers.',
		),
		'promo' => array(
			'name' => 'Promo',
			'description' => 'Product highlights and shopping ideas.',
		),
	);
}

function onthread_seed_blog_posts()
{
	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
	$tracking_url = onthread_get_page_url('order-tracking', home_url('/order-tracking/'));
	$contact_url = onthread_get_page_url('contact', home_url('/contact/'));

	return array(
		array(
			'title' => 'Get $18 Off All Products This Month',
			'slug' => 'get-18-off-all-products-this-month',
			'category' => 'discounts',
			'date' => '2026-06-29 12:00:00',
			'excerpt' => 'Save on every OnThread product with a simple limited-time shopping offer.',
			'content' => '<h3>The OnThread monthly offer</h3><p>For a limited time, OnThread shoppers can enjoy a simple discount across the store. No complicated product exclusions, no confusing checkout steps, just a clear offer made for easy shopping.</p><p>Here is what to know before you order:</p><ul><li><strong>The Discount:</strong> $18 off eligible orders.</li><li><strong>Where to use it:</strong> Browse the current OnThread product selection.</li><li><strong>Best for:</strong> everyday essentials, fresh picks, and giftable finds.</li></ul><blockquote><p>Add your favorites to cart, review the order, and use the savings before the event ends.</p></blockquote><hr><h3>Start with the latest products</h3><p>Browse the shop and choose the items that make sense for your daily routine.</p><p><strong><a href="' . esc_url($shop_url) . '">Your Products Here</a></strong></p><p>Happy shopping!</p>',
		),
		array(
			'title' => 'New Season Essentials From OnThread',
			'slug' => 'new-season-essentials-from-onthread',
			'category' => 'news',
			'date' => '2026-06-29 10:00:00',
			'excerpt' => 'Explore simple, versatile pieces selected for everyday comfort, clean style, and easy gifting.',
			'content' => '<h3>Everyday pieces made easier</h3><p>Refresh your daily rotation with simple products chosen for comfort, clean styling, and practical use. The newest OnThread picks are built for people who want shopping to feel clear and low-friction from browse to checkout.</p><p>Here is what to look for when browsing this season:</p><ul><li><strong>Comfort first:</strong> products that feel easy to use day after day.</li><li><strong>Simple styling:</strong> clean choices that pair naturally with what you already own.</li><li><strong>Gift-ready finds:</strong> useful picks for friends, family, and everyday occasions.</li></ul><blockquote><p>Start with the essentials, then add the pieces that make the order feel personal.</p></blockquote><hr><h3>Browse the latest selection</h3><p>Visit the shop, compare the newest items, and keep customer support close if you need help with sizing, shipping, or order details.</p><p><strong><a href="' . esc_url($shop_url) . '">Shop OnThread</a></strong></p>',
		),
		array(
			'title' => 'How To Track Your Latest Order',
			'slug' => 'how-to-track-your-latest-order',
			'category' => 'news',
			'date' => '2026-06-28 11:00:00',
			'excerpt' => 'Find order updates quickly with clear tracking information and helpful customer support.',
			'content' => '<h3>Keep your order status close</h3><p>After placing an order, the fastest way to follow its progress is through the tracking page. OnThread keeps the process straightforward so you can check status updates without digging through extra steps.</p><p>Use the tracking flow when you need to confirm:</p><ul><li><strong>Order progress:</strong> see whether the order is being prepared or already shipped.</li><li><strong>Delivery updates:</strong> follow the latest carrier movement when tracking is available.</li><li><strong>Support details:</strong> reach out with your order information if something looks unclear.</li></ul><blockquote><p>Have your order number and email ready before checking the tracking page.</p></blockquote><hr><h3>Need help?</h3><p>If the tracking page does not answer your question, contact support with the order details and we will help review the next step.</p><p><strong><a href="' . esc_url($tracking_url) . '">Track Your Order</a></strong></p><p><strong><a href="' . esc_url($contact_url) . '">Contact Support</a></strong></p>',
		),
		array(
			'title' => 'Fresh Picks For Everyday Wear',
			'slug' => 'fresh-picks-for-everyday-wear',
			'category' => 'promo',
			'date' => '2026-06-27 12:00:00',
			'excerpt' => 'Browse recent product highlights built around comfort, practical use, and straightforward style.',
			'content' => '<h3>Fresh ideas for daily use</h3><p>Everyday products work best when they are easy to choose, easy to wear, and easy to reorder. This round of OnThread picks focuses on practical style with enough flexibility for repeat use.</p><p>Consider these simple shopping cues:</p><ul><li><strong>Start neutral:</strong> choose pieces that work across more than one outfit or setting.</li><li><strong>Check comfort:</strong> prioritize products that support regular wear.</li><li><strong>Plan ahead:</strong> keep giftable options in mind for birthdays, holidays, and quick thank-yous.</li></ul><blockquote><p>A good everyday pick should feel useful the first time and familiar by the second.</p></blockquote><hr><h3>Find your next favorite</h3><p>Explore the product grid, compare the newest arrivals, and choose the items that make daily routines easier.</p><p><strong><a href="' . esc_url($shop_url) . '">Shop OnThread</a></strong></p>',
		),
		array(
			'title' => 'A Simple Guide To Choosing Everyday Products',
			'slug' => 'a-simple-guide-to-choosing-everyday-products',
			'category' => 'promo',
			'date' => '2026-06-26 09:30:00',
			'excerpt' => 'Use a few practical checks to choose products that fit your routine.',
			'content' => '<h3>Choose with purpose</h3><p>Good everyday products should solve a clear need, fit your style, and feel easy to use again. Before adding an item to cart, consider where it fits into your normal week.</p><ul><li><strong>Use case:</strong> know when and how you will use it.</li><li><strong>Pairing:</strong> choose items that work with what you already own.</li><li><strong>Value:</strong> prioritize products you will reach for often.</li></ul><blockquote><p>The simplest purchase is often the one you can picture using immediately.</p></blockquote><hr><h3>Make shopping easier</h3><p>Start with the most practical pick, then add a fresh detail when the order needs something extra.</p>',
		),
		array(
			'title' => 'Customer Support Hours And Contact Tips',
			'slug' => 'customer-support-hours-and-contact-tips',
			'category' => 'news',
			'date' => '2026-06-25 14:00:00',
			'excerpt' => 'Know when to contact OnThread and what details help support respond faster.',
			'content' => '<h3>How to get help faster</h3><p>Support is easiest when your message includes the details needed to review the request. Keep your order number, email address, and a short description of the question ready before contacting OnThread.</p><ul><li><strong>Order questions:</strong> include the order number and shipping name.</li><li><strong>Product questions:</strong> send the product name or link.</li><li><strong>Delivery questions:</strong> include any tracking information you already have.</li></ul><blockquote><p>A clear first message helps support respond with a clear next step.</p></blockquote><hr><h3>Contact OnThread</h3><p>Our team is available Monday through Friday during business hours.</p><p><strong><a href="' . esc_url($contact_url) . '">Contact Support</a></strong></p>',
		),
		array(
			'title' => 'Limited-Time Promo Picks Worth Checking',
			'slug' => 'limited-time-promo-picks-worth-checking',
			'category' => 'promo',
			'date' => '2026-06-24 13:00:00',
			'excerpt' => 'A quick look at practical OnThread picks to consider during a promo window.',
			'content' => '<h3>Shop the promo with a plan</h3><p>Limited-time offers are easier to use when you already know what you need. Start with practical products, compare the current selection, and choose items that will not sit unused.</p><ul><li><strong>Refresh basics:</strong> replace items you already use often.</li><li><strong>Try one new thing:</strong> use the offer to test a fresh pick.</li><li><strong>Think gifting:</strong> add useful options for upcoming occasions.</li></ul><blockquote><p>A promo is best when the product still makes sense after the discount ends.</p></blockquote><hr><h3>Explore the current selection</h3><p><strong><a href="' . esc_url($shop_url) . '">Browse Products</a></strong></p>',
		),
		array(
			'title' => 'What To Check Before Placing An Order',
			'slug' => 'what-to-check-before-placing-an-order',
			'category' => 'news',
			'date' => '2026-06-23 15:00:00',
			'excerpt' => 'Review a few details before checkout so your OnThread order is accurate.',
			'content' => '<h3>Checkout with confidence</h3><p>Before placing an order, take a moment to review the most important details. A quick check can prevent address issues, wrong quantities, or missed support questions.</p><ul><li><strong>Shipping address:</strong> confirm the street, apartment number, city, and ZIP code.</li><li><strong>Product details:</strong> review quantity and selected options.</li><li><strong>Contact info:</strong> make sure your email and phone number are correct.</li></ul><blockquote><p>Small checkout checks save time after the order is placed.</p></blockquote><hr><h3>After checkout</h3><p>Use the tracking page for updates once your order information is available.</p><p><strong><a href="' . esc_url($tracking_url) . '">Track Your Order</a></strong></p>',
		),
		array(
			'title' => 'How OnThread Keeps Shopping Simple',
			'slug' => 'how-onthread-keeps-shopping-simple',
			'category' => 'discounts',
			'date' => '2026-06-22 16:00:00',
			'excerpt' => 'A short note on clear product browsing, direct support, and useful offers.',
			'content' => '<h3>Simple is the point</h3><p>OnThread is built around straightforward browsing, practical product choices, and support details that are easy to find. The goal is to make shopping feel calm and clear from the homepage to checkout.</p><ul><li><strong>Clear product grids:</strong> compare items without extra clutter.</li><li><strong>Direct policy pages:</strong> review shipping and support information quickly.</li><li><strong>Useful offers:</strong> shop promotions that are easy to understand.</li></ul><blockquote><p>Good shopping experiences remove doubt instead of adding decisions.</p></blockquote><hr><h3>Start browsing</h3><p><strong><a href="' . esc_url($shop_url) . '">Shop OnThread</a></strong></p>',
		),
	);
}

function onthread_seed_blog_content()
{
	$seed_version = '20260630-onthread-blog-content-v2';

	if (get_option('onthread_seeded_blog_content') === $seed_version) {
		return;
	}

	$category_ids = array();

	foreach (onthread_seed_blog_categories() as $slug => $category) {
		$existing = term_exists($slug, 'category');

		if (!$existing) {
			$existing = wp_insert_term(
				$category['name'],
				'category',
				array(
					'slug' => $slug,
					'description' => $category['description'],
				)
			);
		}

		if (!is_wp_error($existing)) {
			$category_ids[$slug] = is_array($existing) ? (int) $existing['term_id'] : (int) $existing;
		}
	}

	foreach (onthread_seed_blog_posts() as $post_data) {
		$existing_post = get_page_by_path($post_data['slug'], OBJECT, 'post');
		$category = empty($category_ids[$post_data['category']]) ? array() : array($category_ids[$post_data['category']]);

		if ($existing_post instanceof WP_Post) {
			wp_update_post(
				array(
					'ID' => $existing_post->ID,
					'post_status' => 'publish',
					'post_date' => $post_data['date'],
					'post_date_gmt' => get_gmt_from_date($post_data['date']),
					'post_category' => $category,
				)
			);
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title' => $post_data['title'],
				'post_name' => $post_data['slug'],
				'post_type' => 'post',
				'post_status' => 'publish',
				'post_excerpt' => $post_data['excerpt'],
				'post_content' => $post_data['content'],
				'post_date' => $post_data['date'],
				'post_date_gmt' => get_gmt_from_date($post_data['date']),
				'post_author' => get_current_user_id() ? get_current_user_id() : 1,
				'post_category' => $category,
			),
			true
		);

		if (!is_wp_error($post_id)) {
			update_post_meta($post_id, '_onthread_seeded_blog_post', '1');
		}
	}

	update_option('onthread_seeded_blog_content', $seed_version);
}

function onthread_get_blog_category_items()
{
	$category_ids = array();

	foreach (array_keys(onthread_seed_blog_categories()) as $slug) {
		$term = get_category_by_slug($slug);

		if ($term) {
			$category_ids[] = (int) $term->term_id;
		}
	}

	if (!$category_ids) {
		return array();
	}

	$categories = get_categories(
		array(
			'hide_empty' => false,
			'include' => $category_ids,
			'orderby' => 'include',
		)
	);

	if ($categories && !is_wp_error($categories)) {
		return array_map(
			function ($category) {
				return array(
					'name' => $category->name,
					'url' => get_category_link($category),
					'count' => (int) $category->count,
				);
			},
			$categories
		);
	}

	return array();
}

function onthread_get_blog_latest_items($limit = 5)
{
	$items = array();
	$query = new WP_Query(
		array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'ignore_sticky_posts' => true,
			'orderby' => 'date',
			'order' => 'DESC',
		)
	);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
			$items[] = array(
				'title' => get_the_title(),
				'url' => get_permalink(),
				'date' => get_the_date('F j, Y'),
				'image' => $image ? $image : onthread_blog_asset('2016/08/dummy-2-600x390.jpg'),
			);
		}

		wp_reset_postdata();

		return $items;
	}

	wp_reset_postdata();

	return array();
}

function onthread_get_current_post_blog_detail()
{
	$categories = array();

	foreach (get_the_category() as $category) {
		$categories[] = array(
			'name' => $category->name,
			'url' => get_category_link($category),
		);
	}

	$image = get_the_post_thumbnail_url(get_the_ID(), 'full');

	return array(
		'title' => get_the_title(),
		'url' => get_permalink(),
		'date' => get_the_date('F j, Y'),
		'date_iso' => get_the_date('c'),
		'author' => get_the_author(),
		'categories' => $categories,
		'excerpt' => get_the_excerpt(),
		'image' => $image ? $image : '',
		'image_alt' => get_the_title(),
		'content' => apply_filters('the_content', get_the_content()),
		'comments_template' => true,
		'previous' => onthread_get_adjacent_blog_item('previous'),
		'next' => onthread_get_adjacent_blog_item('next'),
	);
}

function onthread_get_adjacent_blog_item($direction)
{
	$post = 'previous' === $direction ? get_previous_post() : get_next_post();

	if (!$post) {
		return null;
	}

	return array(
		'title' => get_the_title($post),
		'url' => get_permalink($post),
	);
}

function onthread_enqueue_child_styles()
{
	$stylesheet_path = get_stylesheet_directory() . '/style.css';
	$stylesheet_ver = file_exists($stylesheet_path) ? filemtime($stylesheet_path) : wp_get_theme()->get('Version');

	wp_dequeue_style('flatsome-style');
	wp_enqueue_style('flatsome-style', get_stylesheet_uri(), array('flatsome-main'), $stylesheet_ver, 'all');
}

function onthread_primary_menu_item_classes($classes, $item, $args, $depth)
{
	if (onthread_is_nav_menu_item_active($item)) {
		$classes[] = 'cf-menu-active';
		$classes[] = 'current-menu-item';
	}

	return array_values(array_unique($classes));
}

function onthread_primary_menu_link_attributes($atts, $item, $args, $depth)
{
	if (onthread_is_nav_menu_item_active($item) && empty($atts['aria-current'])) {
		$atts['aria-current'] = 'page';
	}

	return $atts;
}

function onthread_custom_page_title_parts($title)
{
	if (is_page('shipping-policy')) {
		$title['title'] = 'Shipping Policy and Tax';
	} elseif (is_page('about-us')) {
		$title['title'] = 'About Us';
	} elseif (is_page('contact')) {
		$title['title'] = 'Contact us';
	}

	return $title;
}

function onthread_custom_page_wpseo_title($title)
{
	if (is_page('shipping-policy')) {
		return 'Shipping Policy and Tax - OnThread';
	}

	if (is_page('about-us')) {
		return 'About Us - OnThread';
	}

	if (is_page('contact')) {
		return 'Contact us - OnThread';
	}

	return $title;
}

function onthread_contact_page_template($template)
{
	if (!is_page('contact')) {
		return $template;
	}

	$contact_template = get_stylesheet_directory() . '/page-contact.php';

	return file_exists($contact_template) ? $contact_template : $template;
}

function onthread_is_nav_menu_item_active($item)
{
	if (!isset($item->url) || '#' === $item->url) {
		return false;
	}

	$item_path = onthread_normalize_menu_path($item->url);
	$current_path = onthread_current_request_path();

	if ($item_path === $current_path) {
		return true;
	}

	$shop_path = function_exists('wc_get_page_permalink')
		? onthread_normalize_menu_path(wc_get_page_permalink('shop'))
		: onthread_normalize_menu_path(home_url('/shop/'));

	return $shop_path && $item_path === $shop_path && onthread_is_product_menu_context();
}

function onthread_normalize_menu_path($url)
{
	$path = wp_parse_url($url, PHP_URL_PATH);

	if (empty($path)) {
		return '';
	}

	return strtolower(trim(rawurldecode($path), '/'));
}

function onthread_current_request_path()
{
	static $current_path = null;

	if (null !== $current_path) {
		return $current_path;
	}

	$request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
	$current_path = onthread_normalize_menu_path($request_uri);

	return $current_path;
}

function onthread_is_product_menu_context()
{
	if (
		(function_exists('is_shop') && is_shop()) ||
		(function_exists('is_product_taxonomy') && is_product_taxonomy()) ||
		(function_exists('is_product') && is_product())
	) {
		return true;
	}

	$query_post_type = get_query_var('post_type');
	$request_post_type = isset($_GET['post_type']) ? wp_unslash($_GET['post_type']) : '';

	if (is_array($request_post_type)) {
		$request_post_type = reset($request_post_type);
	}

	$request_post_type = sanitize_key($request_post_type);

	return is_search() && (
		'product' === $query_post_type ||
		(is_array($query_post_type) && in_array('product', $query_post_type, true)) ||
		'product' === $request_post_type
	);
}

function onthread_seed_primary_menu()
{
	$menu_version = '20260630-onthread-primary-menu-clean';

	if (get_option('onthread_seeded_primary_menu') === $menu_version) {
		return;
	}

	$lock_name = 'onthread_seed_primary_menu_lock';
	$lock_time = time();

	if (!add_option($lock_name, $lock_time, '', 'no')) {
		$existing_lock = (int) get_option($lock_name);

		if ($existing_lock && ($lock_time - $existing_lock) < MINUTE_IN_SECONDS) {
			return;
		}

		update_option($lock_name, $lock_time, false);
	}

	$locations = get_theme_mod('nav_menu_locations', array());
	$menu_name = 'OnThread Main Menu';
	$menu = wp_get_nav_menu_object($menu_name);
	$menu_id = $menu ? (int) $menu->term_id : wp_create_nav_menu($menu_name);

	if (is_wp_error($menu_id) || !$menu_id) {
		delete_option($lock_name);
		return;
	}

	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
	$items = array(
		array('title' => 'Home', 'url' => home_url('/')),
		array('title' => 'Products', 'url' => $shop_url),
		array('title' => 'Shipping Policy', 'url' => onthread_get_page_url('shipping-policy', home_url('/shipping-policy/'))),
		array('title' => 'Order Tracking', 'url' => onthread_get_page_url('order-tracking', home_url('/order-tracking/'))),
		array('title' => 'About Us', 'url' => onthread_get_page_url('about-us', home_url('/about-us/'))),
		array('title' => 'Contact us', 'url' => onthread_get_page_url('contact', home_url('/contact/'))),
	);

	$existing_items = wp_get_nav_menu_items($menu_id);

	if ($existing_items) {
		foreach ($existing_items as $existing_item) {
			wp_delete_post((int) $existing_item->ID, true);
		}
	}

	foreach ($items as $position => $item) {
		onthread_add_seeded_menu_item($menu_id, $item, 0, $position);
	}

	$locations['primary'] = (int) $menu_id;
	set_theme_mod('nav_menu_locations', $locations);
	update_option('onthread_seeded_primary_menu', $menu_version);
	delete_option($lock_name);
}

function onthread_add_seeded_menu_item($menu_id, $item, $parent_id = 0, $position = 0)
{
	$item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title' => $item['title'],
			'menu-item-url' => $item['url'],
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => $parent_id,
			'menu-item-position' => $position,
			'menu-item-classes' => empty($item['classes']) ? '' : implode(' ', $item['classes']),
		)
	);

	if (is_wp_error($item_id) || empty($item['children'])) {
		return;
	}

	foreach ($item['children'] as $child_position => $child) {
		onthread_add_seeded_menu_item($menu_id, $child, (int) $item_id, $child_position);
	}
}

function onthread_mobile_sidebar_footer()
{
	$account_url = is_user_logged_in()
		? (function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : admin_url('profile.php'))
		: onthread_get_login_url();
	$account_label = is_user_logged_in() ? 'My account' : 'Sign in';
	$contact_url = onthread_get_page_url('contact');
	?>
	<div class="cf-mobile-sidebar-foot">
		<div class="cf-mobile-sidebar-support">
			<a href="<?php echo esc_url($contact_url); ?>">Need help ?</a>
			<p>Company: <strong>OnThread LLC</strong></p>
			<p>Address: 1001 S MAIN ST STE 600, KALISPELL, MT 59901, United States</p>
			<p>Phone: <strong>+1 (406) 434-1931</strong></p>
		</div>
		<a class="cf-mobile-sidebar-login"
			href="<?php echo esc_url($account_url); ?>"><?php echo get_flatsome_icon('icon-user'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html($account_label); ?></span></a>
	</div>
	<?php
}

function onthread_use_collections_category_base()
{
	$permalinks = (array) get_option('woocommerce_permalinks', array());

	if (isset($permalinks['category_base']) && 'collections' === $permalinks['category_base']) {
		return;
	}

	$permalinks['category_base'] = 'collections';

	update_option('woocommerce_permalinks', $permalinks);
	update_option('woocommerce_queue_flush_rewrite_rules', 'yes');
}

function onthread_flat_product_category_rewrite($args)
{
	$rewrite = isset($args['rewrite']) && is_array($args['rewrite']) ? $args['rewrite'] : array();

	$rewrite['slug'] = 'collections';
	$rewrite['with_front'] = false;
	$rewrite['hierarchical'] = false;

	$args['rewrite'] = $rewrite;

	return $args;
}

function onthread_add_collections_rewrite_rules()
{
	add_rewrite_rule('^collections/([^/]+)/page/([0-9]+)/?$', 'index.php?product_cat=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('^collections/([^/]+)/?$', 'index.php?product_cat=$matches[1]', 'top');
}

function onthread_add_auth_rewrite_rules()
{
	add_rewrite_rule('^login/?$', 'index.php?onthread_auth_page=login', 'top');
	add_rewrite_rule('^lost-password/?$', 'index.php?onthread_auth_page=lost-password', 'top');
	add_rewrite_rule('^register/?$', 'index.php?onthread_auth_page=register', 'top');
}

function onthread_maybe_flush_rewrite_rules()
{
	$rewrite_version = '20260630-real-blog-posts';

	if (get_option('onthread_rewrite_version') === $rewrite_version) {
		return;
	}

	flush_rewrite_rules(false);
	update_option('onthread_rewrite_version', $rewrite_version);
}

function onthread_auth_query_vars($vars)
{
	$vars[] = 'onthread_auth_page';
	$vars[] = 'auth_notice';

	return $vars;
}

function onthread_get_login_url()
{
	return home_url('/login/');
}

function onthread_get_register_url()
{
	return home_url('/register/');
}

function onthread_get_lost_password_url()
{
	return home_url('/lost-password/');
}

function onthread_logout_redirect_url()
{
	return onthread_get_login_url();
}

function onthread_get_auth_page_from_request()
{
	$auth_page = get_query_var('onthread_auth_page');

	if ($auth_page) {
		return $auth_page;
	}

	if (empty($_SERVER['REQUEST_URI'])) {
		return '';
	}

	$request_uri = wp_unslash($_SERVER['REQUEST_URI']);
	$path = parse_url($request_uri, PHP_URL_PATH);

	if (!$path) {
		return '';
	}

	$home_path = parse_url(home_url('/'), PHP_URL_PATH);

	if ($home_path && '/' !== $home_path && 0 === strpos($path, $home_path)) {
		$path = substr($path, strlen($home_path));
	}

	$path = trim($path, '/');

	if (in_array($path, array('login', 'lost-password', 'register'), true)) {
		return $path;
	}

	return '';
}

function onthread_get_account_redirect_url()
{
	return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : admin_url('profile.php');
}

function onthread_auth_redirect_with_notice($page, $notice)
{
	wp_safe_redirect(
		add_query_arg(
			'auth_notice',
			$notice,
			home_url('/' . trim($page, '/') . '/')
		)
	);
	exit;
}

function onthread_handle_auth_actions()
{
	$auth_page = onthread_get_auth_page_from_request();

	if (!$auth_page || 'POST' !== $_SERVER['REQUEST_METHOD']) {
		return;
	}

	if ('login' === $auth_page) {
		if (empty($_POST['onthread_login_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['onthread_login_nonce'])), 'onthread_login')) {
			onthread_auth_redirect_with_notice('login', 'Security check failed. Please try again.');
		}

		$creds = array(
			'user_login' => isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '',
			'user_password' => isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '',
			'remember' => !empty($_POST['rememberme']),
		);
		$user = wp_signon($creds, is_ssl());

		if (is_wp_error($user)) {
			onthread_auth_redirect_with_notice('login', $user->get_error_message());
		}

		wp_safe_redirect(onthread_get_account_redirect_url());
		exit;
	}

	if ('register' === $auth_page) {
		if (empty($_POST['onthread_register_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['onthread_register_nonce'])), 'onthread_register')) {
			onthread_auth_redirect_with_notice('register', 'Security check failed. Please try again.');
		}

		$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
		$username = isset($_POST['username']) ? sanitize_user(wp_unslash($_POST['username'])) : '';
		$password = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';

		if (!$username && $email) {
			$username = sanitize_user(current(explode('@', $email)), true);
		}

		if (!is_email($email)) {
			onthread_auth_redirect_with_notice('register', 'Please enter a valid email address.');
		}

		if (!$username || username_exists($username)) {
			onthread_auth_redirect_with_notice('register', 'Please choose a different username.');
		}

		if (email_exists($email)) {
			onthread_auth_redirect_with_notice('register', 'An account already exists with this email address.');
		}

		if (strlen($password) < 8) {
			onthread_auth_redirect_with_notice('register', 'Please use a password with at least 8 characters.');
		}

		$user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($user_id)) {
			onthread_auth_redirect_with_notice('register', $user_id->get_error_message());
		}

		wp_update_user(
			array(
				'ID' => $user_id,
				'display_name' => $username,
			)
		);

		$user = new WP_User($user_id);

		if (get_role('customer')) {
			$user->set_role('customer');
		}

		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id, true);
		wp_safe_redirect(onthread_get_account_redirect_url());
		exit;
	}

	if ('lost-password' === $auth_page) {
		if (empty($_POST['onthread_lost_password_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['onthread_lost_password_nonce'])), 'onthread_lost_password')) {
			onthread_auth_redirect_with_notice('lost-password', 'Security check failed. Please try again.');
		}

		$user_login = isset($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : '';
		$user = is_email($user_login) ? get_user_by('email', $user_login) : get_user_by('login', $user_login);

		if ($user) {
			$reset_key = get_password_reset_key($user);

			if (is_wp_error($reset_key)) {
				onthread_auth_redirect_with_notice('lost-password', $reset_key->get_error_message());
			}

			$reset_url = network_site_url(
				'wp-login.php?action=rp&key=' . rawurlencode($reset_key) . '&login=' . rawurlencode($user->user_login),
				'login'
			);

			wp_mail(
				$user->user_email,
				sprintf('[%s] Password reset', wp_specialchars_decode(get_option('blogname'), ENT_QUOTES)),
				"Someone requested a password reset for your account.\n\nReset your password here:\n\n" . $reset_url
			);
		}

		onthread_auth_redirect_with_notice('lost-password', 'Password reset instructions have been sent if the account exists.');
	}
}

function onthread_render_auth_pages()
{
	$auth_page = onthread_get_auth_page_from_request();

	if (!$auth_page) {
		return;
	}

	if (is_user_logged_in()) {
		wp_safe_redirect(onthread_get_account_redirect_url());
		exit;
	}

	status_header(200);
	include get_stylesheet_directory() . '/template-parts/auth/auth-page.php';
	exit;
}

function onthread_collection_products_per_page($per_page)
{
	if (
		(function_exists('is_shop') && is_shop()) ||
		(function_exists('is_product_category') && is_product_category())
	) {
		return 16;
	}

	return $per_page;
}

function onthread_collection_archive_query($query)
{
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	$post_type = $query->get('post_type');
	$is_product_search = $query->is_search() && (
		'product' === $post_type ||
		(is_array($post_type) && in_array('product', $post_type, true))
	);

	if (!$query->is_tax('product_cat') && !$is_product_search) {
		return;
	}

	$query->set('posts_per_page', 16);
}

function onthread_flat_product_category_link($termlink, $term, $taxonomy)
{
	if ('product_cat' !== $taxonomy || empty($term->slug)) {
		return $termlink;
	}

	return home_url(user_trailingslashit('collections/' . $term->slug));
}

function onthread_deleted_child_category_parent_map()
{
	return array(
		'albums' => 'music',
		'hoodies' => 'clothing',
		'jeans' => 'women',
		'singles' => 'music',
		't-shirts' => 'men',
		'tops' => 'women',
	);
}

function onthread_redirect_product_category_urls()
{
	if (is_admin() || wp_doing_ajax() || empty($_SERVER['REQUEST_URI'])) {
		return;
	}

	$request_uri = wp_unslash($_SERVER['REQUEST_URI']);
	$current_path = parse_url($request_uri, PHP_URL_PATH);

	if (!$current_path) {
		return;
	}

	$original_path = '/' . trim($current_path, '/');
	$home_path = parse_url(home_url('/'), PHP_URL_PATH);

	if ($home_path && '/' !== $home_path && 0 === strpos($current_path, $home_path)) {
		$current_path = substr($current_path, strlen($home_path));
	}

	$segments = array_values(array_filter(explode('/', trim($current_path, '/'))));

	if (count($segments) < 2) {
		return;
	}

	$base = array_shift($segments);

	if (!in_array($base, array('product-category', 'collections'), true)) {
		return;
	}

	$paged = '';
	$page_position = array_search('page', $segments, true);

	if (false !== $page_position) {
		$paged = isset($segments[$page_position + 1]) ? absint($segments[$page_position + 1]) : '';
		$segments = array_slice($segments, 0, $page_position);
	}

	if (empty($segments)) {
		return;
	}

	$slug = sanitize_title(end($segments));
	$term = get_term_by('slug', $slug, 'product_cat');

	if (!$term || is_wp_error($term)) {
		$deleted_child_map = onthread_deleted_child_category_parent_map();

		if (empty($deleted_child_map[$slug])) {
			return;
		}

		$term = get_term_by('slug', $deleted_child_map[$slug], 'product_cat');

		if (!$term || is_wp_error($term)) {
			return;
		}
	}

	$target = onthread_get_product_category_url($term->slug);

	if ($paged) {
		$target = trailingslashit($target) . 'page/' . $paged . '/';
	}

	$target_path = parse_url($target, PHP_URL_PATH);

	if (untrailingslashit($original_path) === untrailingslashit($target_path)) {
		return;
	}

	$query_string = parse_url($request_uri, PHP_URL_QUERY);

	if ($query_string) {
		$target .= (false === strpos($target, '?') ? '?' : '&') . $query_string;
	}

	wp_safe_redirect($target, 301);
	exit;
}

function onthread_get_page_url($slug, $fallback = '')
{
	$page = get_page_by_path($slug);

	if ($page) {
		return get_permalink($page);
	}

	return $fallback ? $fallback : home_url('/');
}

function onthread_get_product_category_url($slug)
{
	$term = get_term_by('slug', $slug, 'product_cat');

	if ((!$term || is_wp_error($term)) && function_exists('onthread_deleted_child_category_parent_map')) {
		$deleted_child_map = onthread_deleted_child_category_parent_map();

		if (!empty($deleted_child_map[$slug])) {
			$term = get_term_by('slug', $deleted_child_map[$slug], 'product_cat');
		}
	}

	if ($term && !is_wp_error($term)) {
		$link = get_term_link($term);

		if (!is_wp_error($link)) {
			return $link;
		}
	}

	return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}

function onthread_get_cart_count()
{
	if (function_exists('WC') && WC()->cart) {
		return WC()->cart->get_cart_contents_count();
	}

	return 0;
}

function onthread_account_menu_items($items)
{
	unset($items['dashboard'], $items['downloads']);

	return $items;
}

function onthread_redirect_account_dashboard()
{
	if (is_admin() || wp_doing_ajax() || !function_exists('is_account_page') || !is_account_page() || !is_user_logged_in()) {
		return;
	}

	if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url()) {
		return;
	}

	wp_safe_redirect(wc_get_account_endpoint_url('orders'));
	exit;
}

function onthread_logout_modal()
{
	if (!is_user_logged_in()) {
		return;
	}
	?>
	<div class="cf-logout-modal" id="cf-logout-modal" aria-hidden="true">
		<div class="cf-logout-modal__backdrop" data-cf-logout-close></div>
		<div class="cf-logout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cf-logout-modal-title">
			<button class="cf-logout-modal__close" type="button" aria-label="Close" data-cf-logout-close>&times;</button>
			<p class="cf-eyebrow">Account</p>
			<h2 id="cf-logout-modal-title">Log out?</h2>
			<p class="cf-logout-modal__text">Are you sure you want to log out of your account?</p>
			<div class="cf-logout-modal__actions">
				<button class="cf-logout-modal__cancel" type="button" data-cf-logout-close>Cancel</button>
				<a class="cf-logout-modal__confirm" id="cf-logout-confirm"
					href="<?php echo esc_url(wc_get_account_endpoint_url('customer-logout')); ?>">Confirm and log
					out</a>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var modal = document.getElementById('cf-logout-modal');
			var confirmLink = document.getElementById('cf-logout-confirm');

			if (!modal || !confirmLink) {
				return;
			}

			var openModal = function (href) {
				confirmLink.setAttribute('href', href || confirmLink.getAttribute('href'));
				modal.classList.add('is-open');
				modal.setAttribute('aria-hidden', 'false');
				document.documentElement.classList.add('cf-logout-modal-open');
			};

			var closeModal = function () {
				modal.classList.remove('is-open');
				modal.setAttribute('aria-hidden', 'true');
				document.documentElement.classList.remove('cf-logout-modal-open');
			};

			document.addEventListener('click', function (event) {
				var logoutLink = event.target.closest('.cf-logout-trigger, .woocommerce-MyAccount-navigation-link--customer-logout a, a[href*="customer-logout"]');

				if (!logoutLink || logoutLink === confirmLink) {
					return;
				}

				event.preventDefault();
				openModal(logoutLink.getAttribute('href'));
			});

			modal.addEventListener('click', function (event) {
				if (event.target.closest('[data-cf-logout-close]')) {
					closeModal();
				}
			});

			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && modal.classList.contains('is-open')) {
					closeModal();
				}
			});
		});
	</script>
	<?php
}

function onthread_footer()
{
	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
	$contact_url = onthread_get_page_url('contact');
	$about_url = onthread_get_page_url('about-us');
	$blog_page_id = (int) get_option('page_for_posts');
	$blog_url = $blog_page_id ? get_permalink($blog_page_id) : home_url('/blog/');
	$order_tracking_url = onthread_get_page_url('order-tracking', home_url('/order-tracking/'));
	$sitemap_url = onthread_get_page_url('sitemap', home_url('/sitemap_index.xml'));
	$return_url = onthread_get_page_url('return-and-refund-policy');
	$cancellation_url = onthread_get_page_url('order-changes-and-cancellations', onthread_get_page_url('cancellation-and-modification-policy'));
	$shipping_url = onthread_get_page_url('shipping-policy', home_url('/shipping-policy/'));
	$privacy_url = onthread_get_page_url('privacy-policy');
	$ca_privacy_url = onthread_get_page_url('ca-do-not-sell-my-personal-information', home_url('/ca-do-not-sell-my-personal-information/'));
	$terms_url = onthread_get_page_url('terms-and-conditions', onthread_get_page_url('terms-of-service'));
	$year = date_i18n('Y');
	?>
	<div class="cf-site-footer">
		<section class="cf-footer-main" aria-label="Footer">
			<div class="cf-container cf-footer-grid">
				<div class="cf-footer-column cf-footer-brand">
					<h3>Onthread is owned by OnThread LLC</h3>
					<p>From thoughtful everyday products to easy customer support, Onthread keeps shopping simple, clear, and reliable.</p>
					<p>Monday - Friday: 8:30AM-4:30PM</p>
					<p><strong>Phone:</strong> +1 (406) 434-1931</p>
					<p><strong>Address:</strong> 1001 S MAIN ST STE 600, KALISPELL, MT 59901, United States</p>
					<p><strong>Company:</strong> OnThread LLC</p>
					<div class="cf-socials" aria-label="Social links">
						<a href="#" aria-label="Facebook"><i class="icon-facebook"></i></a>
						<a href="#" aria-label="Pinterest"><i class="icon-pinterest"></i></a>
					</div>
				</div>

				<div class="cf-footer-column">
					<h3>Our Information</h3>
					<ul>
						<li><a href="<?php echo esc_url($blog_url ? $blog_url : home_url('/blog/')); ?>">Blog</a></li>
						<li><a href="<?php echo esc_url($about_url); ?>">About Us</a></li>
						<li><a href="<?php echo esc_url($contact_url); ?>">Contact us</a></li>
						<li><a href="<?php echo esc_url($order_tracking_url); ?>">Order Tracking</a></li>
						<li><a href="<?php echo esc_url($sitemap_url); ?>">Sitemap</a></li>
					</ul>
				</div>

				<div class="cf-footer-column">
					<h3>Our Policies</h3>
					<ul>
						<li><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a></li>
						<li><a href="<?php echo esc_url($ca_privacy_url); ?>">CA: Do Not Sell My Personal Information</a></li>
						<li><a href="<?php echo esc_url($shipping_url); ?>">Shipping Policy and Tax</a></li>
						<li><a href="<?php echo esc_url($return_url); ?>">Return And Refund Policy</a></li>
						<li><a href="<?php echo esc_url($terms_url); ?>">Terms And Conditions</a></li>
						<li><a href="<?php echo esc_url($cancellation_url); ?>">Order Changes And Cancellations</a></li>
					</ul>
				</div>
			</div>
		</section>

		<div class="cf-footer-bottom">
			<div class="cf-container">
				<p>&copy; <?php echo esc_html($year); ?> Onthread.net | OnThread LLC</p>
			</div>
		</div>
	</div>
	<?php
}
