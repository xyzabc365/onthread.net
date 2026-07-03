<?php
/**
 * OnThread single product layout.
 *
 * @package Flatsome_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

if ( ! function_exists( 'onthread_single_product_related_card' ) ) {
	function onthread_single_product_related_card( WC_Product $related_product ) {
		$image_id  = $related_product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';

		if ( ! $image_url && function_exists( 'wc_placeholder_img_src' ) ) {
			$image_url = wc_placeholder_img_src( 'woocommerce_thumbnail' );
		}

		$category = function_exists( 'onthread_product_card_category' ) ? onthread_product_card_category( $related_product ) : array( 'name' => 'Products', 'url' => '' );
		$excerpt  = function_exists( 'onthread_product_card_excerpt' ) ? onthread_product_card_excerpt( $related_product, 20 ) : '';
		?>
		<article <?php wc_product_class( 'cf-product-item', $related_product ); ?>>
			<a class="cf-product-item__media" href="<?php echo esc_url( get_permalink( $related_product->get_id() ) ); ?>" aria-label="<?php echo esc_attr( $related_product->get_name() ); ?>">
				<?php if ( $related_product->is_on_sale() ) : ?>
					<span class="cf-product-item__badge">Sale</span>
				<?php endif; ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $related_product->get_name() ); ?>" loading="lazy">
			</a>
			<div class="cf-product-item__info">
				<?php if ( ! empty( $category['url'] ) ) : ?>
					<a class="cf-product-item__category" href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a>
				<?php else : ?>
					<span class="cf-product-item__category"><?php echo esc_html( $category['name'] ); ?></span>
				<?php endif; ?>
				<a class="cf-product-item__title" href="<?php echo esc_url( get_permalink( $related_product->get_id() ) ); ?>"><?php echo esc_html( $related_product->get_name() ); ?></a>
				<?php if ( $excerpt ) : ?>
					<p class="cf-product-item__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<div class="cf-product-item__price"><?php echo wp_kses_post( $related_product->get_price_html() ); ?></div>
			</div>
		</article>
		<?php
	}
}

$description       = $product->get_description();
$short_description = $product->get_short_description();
$category_list     = wc_get_product_category_list( $product->get_id(), ', ' );
$primary_category  = function_exists( 'onthread_product_card_category' ) ? onthread_product_card_category( $product ) : array( 'name' => 'Products', 'url' => '' );
$shipping_url      = function_exists( 'onthread_get_page_url' ) ? onthread_get_page_url( 'shipping-policy', home_url( '/shipping-policy/' ) ) : home_url( '/shipping-policy/' );
$return_url        = function_exists( 'onthread_get_page_url' ) ? onthread_get_page_url( 'return-and-refund-policy', home_url( '/return-and-refund-policy/' ) ) : home_url( '/return-and-refund-policy/' );
$has_attributes    = $product->has_attributes() || $product->has_weight() || $product->has_dimensions();
$related_ids       = wc_get_related_products( $product->get_id(), 5 );
?>

<div class="cf-product-page">
	<section class="cf-product-main">
		<div class="cf-container">
			<div class="cf-product-layout">
				<div class="cf-product-gallery">
					<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 *
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>

				<div class="cf-product-info summary entry-summary">
					<nav class="cf-product-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'woocommerce' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
						<span aria-hidden="true">/</span>
						<?php if ( ! empty( $primary_category['url'] ) ) : ?>
							<a href="<?php echo esc_url( $primary_category['url'] ); ?>"><?php echo esc_html( $primary_category['name'] ); ?></a>
							<span aria-hidden="true">/</span>
						<?php endif; ?>
						<span><?php the_title(); ?></span>
					</nav>

					<h1 class="cf-product-title"><?php the_title(); ?></h1>

					<?php if ( wc_review_ratings_enabled() && $product->get_rating_count() > 0 ) : ?>
						<div class="cf-product-rating">
							<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<a href="#cf-product-reviews"><?php echo esc_html( sprintf( '%s customer reviews', number_format_i18n( $product->get_rating_count() ) ) ); ?></a>
						</div>
					<?php endif; ?>

					<div class="cf-product-price">
						<?php woocommerce_template_single_price(); ?>
					</div>

					<?php if ( $short_description ) : ?>
						<div class="cf-product-excerpt">
							<?php echo wp_kses_post( wpautop( $short_description ) ); ?>
						</div>
					<?php endif; ?>

					<div class="cf-product-cart">
						<?php woocommerce_template_single_add_to_cart(); ?>
					</div>

					<div class="cf-product-meta">
						<?php woocommerce_template_single_meta(); ?>
					</div>

					<div class="cf-product-trust" aria-label="Product benefits">
						<div class="cf-product-trust__item">
							<span aria-hidden="true"></span>
							<div>
								<strong>Free And Fast Shipping</strong>
								<p>Shop online with ease and qualify for free shipping on all orders.</p>
							</div>
						</div>
					</div>

					<div class="cf-product-payments">
						<p>
							<strong>Shipping Policy</strong> <a href="<?php echo esc_url( $shipping_url ); ?>">Details</a><br>
							Free Shipping<br>
							Handling time: 1 - 3 business days<br>
							Transit Time: 4 - 7 business days<br>
							Total Shipping Time: 5 - 10 business days<br>
							<strong>Refund And Return Policy:</strong> <a href="<?php echo esc_url( $return_url ); ?>">Details</a><br>
							Returns and exchanges accepted<br>
							Eligible for Return and Refund within 30 days from the date of delivery
						</p>
					</div>
				</div>
			</div>

			<section class="cf-product-accordions" aria-label="Product information">
				<div class="cf-product-tabs-row">
					<div class="cf-product-tabs-col cf-product-tabs-col--main">
						<h2 class="cf-product-tabs-title">Description</h2>
						<div class="cf-product-accordion-content">
							<?php
							if ( $description ) {
								echo wp_kses_post( apply_filters( 'the_content', $description ) );
							} elseif ( $short_description ) {
								echo wp_kses_post( wpautop( $short_description ) );
							} else {
								echo '<p>This product is selected for everyday comfort, distinctive style, and easy pairing with your favorite outfits.</p>';
							}
							?>
						</div>

						<h2 class="cf-product-tabs-title">Additional information</h2>
						<div class="cf-product-accordion-content">
							<?php if ( $has_attributes ) : ?>
								<?php wc_display_product_attributes( $product ); ?>
							<?php else : ?>
								<table class="woocommerce-product-attributes shop_attributes" aria-label="<?php esc_attr_e( 'Product Details', 'woocommerce' ); ?>">
									<tbody>
										<tr class="woocommerce-product-attributes-item">
											<th class="woocommerce-product-attributes-item__label" scope="row">Categories</th>
											<td class="woocommerce-product-attributes-item__value">
												<p><?php echo $category_list ? wp_kses_post( $category_list ) : esc_html__( 'Products', 'woocommerce' ); ?></p>
											</td>
										</tr>
									</tbody>
								</table>
							<?php endif; ?>
						</div>
					</div>

					<div class="cf-product-tabs-col cf-product-tabs-col--reviews" id="cf-product-reviews">
						<h2 class="cf-product-tabs-title">Reviews</h2>
						<div class="cf-product-accordion-content">
							<?php if ( comments_open() || get_comments_number() ) : ?>
								<?php comments_template(); ?>
							<?php else : ?>
								<p>There are no reviews yet.</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
		</div>
	</section>

	<?php if ( ! empty( $related_ids ) ) : ?>
		<section class="cf-product-related">
			<div class="cf-container">
				<div class="cf-product-section-heading">
					<h2>Related products</h2>
				</div>
				<div class="cf-product-related-grid">
					<?php foreach ( $related_ids as $related_id ) : ?>
						<?php
						$related_product = wc_get_product( $related_id );

						if ( ! $related_product ) {
							continue;
						}

						onthread_single_product_related_card( $related_product );
						?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
</div>
