<?php
/**
 * SixFootOak-inspired front page.
 *
 * @package Flatsome_Child
 */

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

if ( ! function_exists( 'onthread_home_asset' ) ) {
	function onthread_home_asset( $path ) {
		return content_url( 'uploads/' . ltrim( $path, '/' ) );
	}
}

if ( ! function_exists( 'onthread_home_product_query' ) ) {
	function onthread_home_product_query( $category = '', $limit = 5 ) {
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'orderby'            => 'date',
			'order'              => 'DESC',
		);

		if ( $category ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}

		return new WP_Query( $args );
	}
}

if ( ! function_exists( 'onthread_home_trim_excerpt' ) ) {
	function onthread_home_trim_excerpt( $text, $words = 22 ) {
		$text = wp_strip_all_tags( $text );

		if ( ! $text ) {
			return '';
		}

		return wp_trim_words( $text, $words, '...' );
	}
}

if ( ! function_exists( 'onthread_home_product_card' ) ) {
	function onthread_home_product_card() {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;

		if ( ! $product ) {
			return;
		}

		$image = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_thumbnail' );

		if ( ! $image && function_exists( 'wc_placeholder_img_src' ) ) {
			$image = wc_placeholder_img_src( 'woocommerce_thumbnail' );
		}

		$terms       = get_the_terms( get_the_ID(), 'product_cat' );
		$term        = ( $terms && ! is_wp_error( $terms ) ) ? reset( $terms ) : null;
		$category    = $term ? $term->name : 'Products';
		$category_url = $term ? get_term_link( $term ) : '';
		$excerpt     = onthread_home_trim_excerpt( $product->get_short_description() ? $product->get_short_description() : get_the_excerpt(), 20 );
		?>
		<article class="cf-product-item">
			<a class="cf-product-item__media" href="<?php the_permalink(); ?>">
				<?php if ( $product->is_on_sale() ) : ?>
					<span class="cf-product-item__badge">Sale</span>
				<?php endif; ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
			</a>
			<div class="cf-product-item__info">
				<?php if ( $category_url && ! is_wp_error( $category_url ) ) : ?>
					<a class="cf-product-item__category" href="<?php echo esc_url( $category_url ); ?>"><?php echo esc_html( $category ); ?></a>
				<?php else : ?>
					<span class="cf-product-item__category"><?php echo esc_html( $category ); ?></span>
				<?php endif; ?>
				<a class="cf-product-item__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php if ( $excerpt ) : ?>
					<p class="cf-product-item__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<div class="cf-product-item__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			</div>
		</article>
		<?php
	}
}

if ( ! function_exists( 'onthread_home_product_section' ) ) {
	function onthread_home_product_section() {
		$query = onthread_home_product_query( '', 5 );
		?>
		<section class="cf-home-section cf-products-section">
			<div class="cf-container">
				<div class="cf-section-heading cf-section-heading--center">
					<h2>Our Products</h2>
				</div>

				<div class="cf-product-grid">
					<?php
					if ( $query->have_posts() ) :
						while ( $query->have_posts() ) :
							$query->the_post();
							onthread_home_product_card();
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<p class="cf-empty-products">No matching products found.</p>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

if ( ! function_exists( 'onthread_home_news_query' ) ) {
	function onthread_home_news_query() {
		return new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
				'orderby'            => 'date',
				'order'              => 'DESC',
			)
		);
	}
}

if ( ! function_exists( 'onthread_home_news_card' ) ) {
	function onthread_home_news_card() {
		$image   = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
		$image   = $image ? $image : onthread_home_asset( '2016/08/dummy-2-600x390.jpg' );
		$excerpt = onthread_home_trim_excerpt( get_the_excerpt(), 24 );
		?>
		<article class="cf-news-card">
			<a class="cf-news-card__media" href="<?php the_permalink(); ?>">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
			</a>
			<div class="cf-news-card__text">
				<h3 class="cf-news-card__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h3>
				<div class="cf-news-card__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></time>
					<span><?php comments_number( 'No Comments', '1 Comment', '% Comments' ); ?></span>
				</div>
				<?php if ( $excerpt ) : ?>
					<p><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<a class="cf-news-card__read-more" href="<?php the_permalink(); ?>">Read More &raquo;</a>
			</div>
		</article>
		<?php
	}
}

if ( ! function_exists( 'onthread_home_news_section' ) ) {
	function onthread_home_news_section() {
		$query = onthread_home_news_query();

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return;
		}
		?>
		<section class="cf-home-section cf-news-section">
			<div class="cf-container">
				<div class="cf-section-heading cf-section-heading--center">
					<h2>Our News</h2>
				</div>

				<div class="cf-news-grid">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						onthread_home_news_card();
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
?>

<div class="cf-home">
	<section class="cf-home-hero">
		<div class="cf-container">
			<a class="cf-hero-panel" href="<?php echo esc_url( $shop_url ); ?>" aria-label="Shop OnThread products">
				<span class="cf-hero-kicker">New product</span>
				<span class="cf-hero-brand">OnThread</span>
				<span class="cf-hero-word">Threads</span>
				<span class="cf-hero-script">Black</span>
				<span class="cf-hero-product">
					<img class="cf-hero-photo cf-hero-photo-main" src="<?php echo esc_url( onthread_home_asset( 'onthread/home-hero-banner.jpg' ) ); ?>" alt="" aria-hidden="true">
				</span>
			</a>
		</div>
	</section>

	<?php
	onthread_home_product_section();
	onthread_home_news_section();
	?>
</div>

<?php
get_footer();
