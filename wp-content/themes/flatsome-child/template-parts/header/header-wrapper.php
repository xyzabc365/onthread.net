<?php
/**
 * OnThread header layout for the child theme.
 *
 * @package Flatsome_Child
 */

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$account_url = is_user_logged_in()
	? (function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : admin_url('profile.php'))
	: onthread_get_login_url();
$account_label = is_user_logged_in() ? 'My account' : 'Sign in';
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : onthread_get_page_url('cart');
$contact_url = onthread_get_page_url('contact');
$cart_count = onthread_get_cart_count();
?>

<div class="cf-site-header">
	<div class="cf-announcement">
		<div class="cf-container">
			<div class="cf-announcement-spacer" aria-hidden="true"></div>
			<p><span class="cf-desktop-copy">FATHER'S DAY BIG SALE - 10% OFF SITEWIDE</span><span
					class="cf-mobile-copy">10% OFF SITEWIDE</span></p>
		</div>
	</div>

	<div class="cf-mainbar">
		<div class="cf-container">
			<div class="cf-brand-group">
				<a href="#" data-open="#main-menu" data-pos="<?php echo esc_attr(flatsome_option('mobile_overlay')); ?>"
					data-bg="main-menu-overlay"
					data-color="<?php echo esc_attr(flatsome_option('mobile_overlay_color')); ?>"
					class="cf-mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'flatsome'); ?>"
					aria-controls="main-menu" aria-expanded="false">
					<?php echo get_flatsome_icon('icon-menu'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>

				<a class="cf-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?>">
					onthread
				</a>
			</div>


			<nav class="cf-nav" aria-label="Main menu">
				<?php if (has_nav_menu('primary')): ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container' => false,
							'menu_id' => 'cf-primary-menu',
							'menu_class' => '',
							'items_wrap' => '<ul id="%1$s">%3$s</ul>',
							'depth' => 2,
							'fallback_cb' => false,
						)
					);
					?>
				<?php else: ?>
					<ul>
						<li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
						<li><a href="<?php echo esc_url($shop_url); ?>">Products</a></li>
						<li><a
								href="<?php echo esc_url(onthread_get_page_url('shipping-policy', home_url('/shipping-policy/'))); ?>">Shipping
								Policy</a></li>
						<li><a
								href="<?php echo esc_url(onthread_get_page_url('order-tracking', home_url('/order-tracking/'))); ?>">Order
								Tracking</a></li>
						<li><a
								href="<?php echo esc_url(onthread_get_page_url('about-us', home_url('/about-us/'))); ?>">About
								Us</a></li>
						<li><a href="<?php echo esc_url($contact_url); ?>">Contact us</a></li>
					</ul>
				<?php endif; ?>
			</nav>

			<div class="cf-header-wrap-actions">
				<form role="search" method="get" class="cf-search-form" action="<?php echo esc_url(home_url('/')); ?>">
					<label class="screen-reader-text" for="cf-product-search">Search products:</label>
					<input id="cf-product-search" type="search" name="s"
						value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Search">
					<input type="hidden" name="post_type" value="product">
					<button type="submit" aria-label="Search">
						<?php echo get_flatsome_icon('icon-search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</form>

				<div class="cf-header-actions">
					<a class="cf-action cf-account <?php echo is_user_logged_in() ? 'is-logged-in' : 'is-logged-out'; ?>"
						href="<?php echo esc_url($account_url); ?>">
						<?php echo get_flatsome_icon('icon-user'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span>
							<?php echo esc_html($account_label); ?>
						</span>
					</a>
					<a class="cf-action cf-mobile-search-action"
						href="<?php echo esc_url(home_url('/?s=&post_type=product')); ?>" aria-label="Search">
						<?php echo get_flatsome_icon('icon-search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="cf-cart" href="<?php echo esc_url($cart_url); ?>" aria-label="Cart">
						<svg class="cf-cart-icon" aria-hidden="true" viewBox="0 0 24 24" focusable="false">
							<path d="M7.25 8.5h9.5l.72 11H6.53l.72-11Z"></path>
							<path d="M9 8.5V7a3 3 0 0 1 6 0v1.5"></path>
						</svg>
						<span class="cf-cart-count">
							<?php echo esc_html($cart_count); ?>
						</span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php do_action('flatsome_header_wrapper'); ?>
</div>
