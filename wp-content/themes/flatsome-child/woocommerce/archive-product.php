<?php
/**
 * OnThread product category archive.
 *
 * @package Flatsome_Child
 */

defined( 'ABSPATH' ) || exit;

$query_post_type = get_query_var( 'post_type' );
$request_post_type = isset( $_GET['post_type'] ) ? wp_unslash( $_GET['post_type'] ) : '';

if ( is_array( $request_post_type ) ) {
	$request_post_type = reset( $request_post_type );
}

$is_product_search = is_search() && (
	'product' === $query_post_type ||
	( is_array( $query_post_type ) && in_array( 'product', $query_post_type, true ) ) ||
	'product' === sanitize_key( $request_post_type )
);
$is_shop_archive = function_exists( 'is_shop' ) && is_shop();
$is_collection_archive = is_product_category();

if ( ! $is_shop_archive && ! $is_collection_archive && ! $is_product_search ) {
	include get_template_directory() . '/woocommerce/archive-product.php';
	return;
}

global $wp_query;

$shop_url      = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$total         = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
$current_page  = max( 1, (int) get_query_var( 'paged' ) );
$per_page      = isset( $wp_query->query_vars['posts_per_page'] ) ? (int) $wp_query->query_vars['posts_per_page'] : 16;
$shown_first   = $total ? ( ( $current_page - 1 ) * $per_page ) + 1 : 0;
$shown_last    = $total && $per_page > 0 ? min( $total, $current_page * $per_page ) : $total;
$term          = $is_collection_archive ? get_queried_object() : null;
$term_name     = $term instanceof WP_Term ? $term->name : woocommerce_page_title( false );
$term_desc     = $term instanceof WP_Term ? term_description( $term->term_id, 'product_cat' ) : '';
$search_query  = get_search_query( false );
$shop_page_content = '';
$category_list = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'orderby'    => 'name',
		'parent'     => 0,
	)
);

if ( $is_product_search ) {
	$term_name = $search_query
		? sprintf( '%1$s search results for: "%2$s"', number_format_i18n( $total ), $search_query )
		: 'Product search results';
	$term_desc = '';
} elseif ( $is_shop_archive ) {
	$term_name = woocommerce_page_title( false );
	$term_desc = '';

	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = $shop_page_id > 0 ? get_post( $shop_page_id ) : null;

		if ( $shop_page instanceof WP_Post && ! empty( $shop_page->post_content ) ) {
			$shop_page_content = apply_filters( 'the_content', $shop_page->post_content );
		}
	}
}

if ( ! function_exists( 'onthread_collection_product_card' ) ) {
	function onthread_collection_product_card() {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : null;

		if ( ! $product ) {
			return;
		}

		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';

		if ( ! $image_url && function_exists( 'wc_placeholder_img_src' ) ) {
			$image_url = wc_placeholder_img_src( 'woocommerce_thumbnail' );
		}

		$category = function_exists( 'onthread_product_card_category' ) ? onthread_product_card_category( $product ) : array( 'name' => 'Products', 'url' => '' );
		$excerpt  = function_exists( 'onthread_product_card_excerpt' ) ? onthread_product_card_excerpt( $product, 20 ) : '';
		?>
		<article <?php wc_product_class( 'cf-product-item', $product ); ?>>
			<a class="cf-product-item__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<?php if ( $product->is_on_sale() ) : ?>
					<span class="cf-product-item__badge">Sale</span>
				<?php endif; ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
			</a>
			<div class="cf-product-item__info">
				<?php if ( ! empty( $category['url'] ) ) : ?>
					<a class="cf-product-item__category" href="<?php echo esc_url( $category['url'] ); ?>"><?php echo esc_html( $category['name'] ); ?></a>
				<?php else : ?>
					<span class="cf-product-item__category"><?php echo esc_html( $category['name'] ); ?></span>
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

if ( ! function_exists( 'onthread_collection_result_count' ) ) {
	function onthread_collection_result_count( $total, $first, $last ) {
		if ( 1 === $total ) {
			return 'Showing 1 product';
		}

		if ( $total <= $last ) {
			return sprintf( 'Showing all %s products', number_format_i18n( $total ) );
		}

		return sprintf(
			'Showing %1$s-%2$s of %3$s products',
			number_format_i18n( $first ),
			number_format_i18n( $last ),
			number_format_i18n( $total )
		);
	}
}

if ( ! function_exists( 'onthread_collection_ordering' ) ) {
	function onthread_collection_ordering() {
		$selected = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$options  = array(
			'menu_order' => 'Featured',
			'popularity' => 'Best selling',
			'rating'     => 'Top rated',
			'date'       => 'Newest',
			'price'      => 'Price: low to high',
			'price-desc' => 'Price: high to low',
		);
		?>
		<form class="cf-collection-sort" method="get">
			<label class="screen-reader-text" for="cf-collection-orderby">Sort</label>
			<select id="cf-collection-orderby" name="orderby" onchange="this.form.submit()">
				<?php foreach ( $options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="paged" value="1">
			<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
			<button type="submit">Apply</button>
		</form>
		<?php
	}
}

if ( ! function_exists( 'onthread_collection_price_filter' ) ) {
	function onthread_collection_price_filter( $suffix = 'desktop' ) {
		if ( ! class_exists( 'WC_Widget_Price_Filter' ) ) {
			return;
		}

		the_widget(
			'WC_Widget_Price_Filter',
			array(
				'title' => 'Price',
			),
			array(
				'before_widget' => '<aside id="woocommerce_price_filter-' . esc_attr( $suffix ) . '" class="widget woocommerce widget_price_filter">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			)
		);
	}
}

if ( ! function_exists( 'onthread_collection_attribute_filter' ) ) {
	function onthread_collection_attribute_filter( $shop_url ) {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$attribute            = null;

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $taxonomy ) {
				if ( 'color' === $taxonomy->attribute_name ) {
					$attribute = $taxonomy;
					break;
				}
			}

			if ( ! $attribute ) {
				$attribute = reset( $attribute_taxonomies );
			}
		}

		if ( ! $attribute ) {
			return;
		}

		$taxonomy_name = wc_attribute_taxonomy_name( $attribute->attribute_name );
		$terms         = get_terms(
			array(
				'taxonomy'   => $taxonomy_name,
				'hide_empty' => true,
				'orderby'    => 'name',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		$query_key = 'filter_' . sanitize_key( $attribute->attribute_name );
		$active    = isset( $_GET[ $query_key ] ) ? wc_clean( wp_unslash( $_GET[ $query_key ] ) ) : '';
		?>
		<aside class="widget woocommerce widget_layered_nav woocommerce-widget-layered-nav">
			<div class="widget-title"><?php echo esc_html( $attribute->attribute_label ); ?></div>
			<ul class="c-ip-attribute-filter__list">
				<?php foreach ( $terms as $attribute_term ) : ?>
					<li class="c-ip-attribute-filter__item <?php echo $active === $attribute_term->slug ? 'chosen' : ''; ?>">
						<a rel="nofollow" href="<?php echo esc_url( add_query_arg( $query_key, $attribute_term->slug, $shop_url ) ); ?>">
							<span class="c-ip-attribute-filter__sw c-ip-attribute-filter__sw--checkbox" aria-hidden="true"></span>
							<?php echo esc_html( $attribute_term->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
		<?php
	}
}

if ( ! function_exists( 'onthread_collection_category_filter' ) ) {
	function onthread_collection_category_filter( $suffix = 'desktop' ) {
		if ( ! class_exists( 'WC_Widget_Product_Categories' ) ) {
			return;
		}

		ob_start();
		the_widget(
			'WC_Widget_Product_Categories',
			array(
				'title'              => 'Categories',
				'orderby'            => 'name',
				'dropdown'           => 0,
				'count'              => 1,
				'hierarchical'       => 1,
				'show_children_only' => 0,
				'hide_empty'         => 0,
				'max_depth'          => '',
			),
			array(
				'before_widget' => '<aside id="woocommerce_product_categories-' . esc_attr( $suffix ) . '" class="widget woocommerce widget_product_categories">',
				'after_widget'  => '</aside>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			)
		);
		$output = ob_get_clean();
		$output = preg_replace( '/<span class="count">\(([^<]+)\)<\/span>/', '<span class="count">$1</span>', $output );

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'onthread_collection_sidebar' ) ) {
	function onthread_collection_sidebar( $category_list, $shop_url, $term, $is_shop_archive, $suffix = 'desktop' ) {
		?>
		<div class="cf-shop-sidebar__wrap c-shop-sidebar__wrap">
			<div class="cf-shop-sidebar__content c-shop-sidebar__content c-shop-sidebar__content--boxed c-shop-sidebar__content--4-per-row c-shop-sidebar__content--<?php echo esc_attr( $suffix ); ?>">
				<div class="c-sidebar__wrap">
					<?php
					onthread_collection_category_filter( $suffix );
					onthread_collection_price_filter( $suffix );
					onthread_collection_attribute_filter( $shop_url );
					?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'onthread_collection_pagination' ) ) {
	function onthread_collection_pagination() {
		$total   = isset( $GLOBALS['wp_query']->max_num_pages ) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
		$current = max( 1, (int) get_query_var( 'paged' ) );

		if ( $total <= 1 ) {
			return;
		}

		$pages = paginate_links(
			array(
				'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
				'current'   => $current,
				'total'     => $total,
				'prev_text' => '<span aria-hidden="true">&#8249;</span>',
				'next_text' => '<span aria-hidden="true">&#8250;</span>',
				'type'      => 'array',
				'end_size'  => 1,
				'mid_size'  => 2,
			)
		);

		if ( ! is_array( $pages ) ) {
			return;
		}
		?>
		<nav class="cf-collection-pagination" aria-label="Product pagination">
			<ul>
				<?php foreach ( $pages as $page ) : ?>
					<li><?php echo wp_kses_post( $page ); ?></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<?php
	}
}

remove_action( 'flatsome_after_header', 'flatsome_category_header' );

get_header( 'shop' );
?>

<main class="cf-collection-page cf-collection-page--sixshop">
	<?php if ( ! $is_shop_archive || $is_product_search || $term_desc ) : ?>
		<section class="cf-collection-heading">
			<div class="cf-container">
				<div class="cf-collection-heading__content">
					<p class="cf-eyebrow"><?php echo esc_html( $is_product_search ? 'Search' : 'Collection' ); ?></p>
					<h1><?php echo esc_html( $term_name ); ?></h1>
					<?php if ( $is_product_search ) : ?>
						<form role="search" method="get" class="cf-mobile-search-page-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label class="screen-reader-text" for="cf-mobile-search-page-input">Search products:</label>
							<input id="cf-mobile-search-page-input" type="search" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Search products">
							<input type="hidden" name="post_type" value="product">
							<button type="submit" aria-label="Search"><?php echo get_flatsome_icon( 'icon-search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						</form>
					<?php endif; ?>
					<?php if ( $term_desc ) : ?>
						<div class="cf-collection-description"><?php echo wp_kses_post( wpautop( $term_desc ) ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="cf-collection-main">
		<div class="cf-container">
			<?php wc_print_notices(); ?>

			<input class="cf-collection-filter-toggle" id="cf-collection-filter-toggle" type="checkbox" hidden>
			<label class="cf-collection-filter-backdrop" for="cf-collection-filter-toggle" aria-hidden="true"></label>
			<aside class="cf-collection-filter-drawer cf-shop-sidebar cf-shop-sidebar--mobile c-sidebar c-sidebar--collapse c-shop-sidebar" aria-label="Product filters">
				<div class="cf-collection-filter-header">
					<span>Filters</span>
					<label for="cf-collection-filter-toggle" aria-label="Close filters">&times;</label>
				</div>
				<?php onthread_collection_sidebar( $category_list, $shop_url, $term, $is_shop_archive, 'mobile' ); ?>
			</aside>

			<div class="cf-shop-layout">
				<aside class="cf-shop-sidebar cf-shop-sidebar--desktop c-sidebar c-sidebar--collapse c-shop-sidebar c-shop-sidebar--single c-shop-sidebar--desktop-sidebar" aria-label="Product filters">
					<?php onthread_collection_sidebar( $category_list, $shop_url, $term, $is_shop_archive, 'desktop' ); ?>
				</aside>

				<div class="cf-collection-content">
					<div class="cf-collection-toolbar">
						<div class="cf-collection-toolbar__filter">
							<label class="cf-collection-filter-button" for="cf-collection-filter-toggle">
								<span aria-hidden="true"></span>
								<span>Filters</span>
							</label>
							<p><?php echo esc_html( onthread_collection_result_count( $total, $shown_first, $shown_last ) ); ?></p>
						</div>
					</div>

					<?php if ( woocommerce_product_loop() ) : ?>
						<div id="cf-collection-grid" class="cf-collection-grid" style="--cf-collection-cols: 4;">
							<?php
							while ( have_posts() ) :
								the_post();
								do_action( 'woocommerce_shop_loop' );
								onthread_collection_product_card();
							endwhile;
							?>
						</div>
						<?php onthread_collection_pagination(); ?>
					<?php else : ?>
						<div class="cf-collection-empty">
							<p><?php echo esc_html( $is_product_search ? 'No products matched this search.' : 'No matching products found in this category.' ); ?></p>
							<a class="cf-button cf-button-primary" href="<?php echo esc_url( $shop_url ); ?>">View all products</a>
						</div>
					<?php endif; ?>

					<?php if ( $shop_page_content ) : ?>
						<div class="cf-collection-cat-desc">
							<?php echo wp_kses_post( $shop_page_content ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var initShopFilters = function (root) {
				var sidebars = (root || document).querySelectorAll('.cf-shop-sidebar');

				sidebars.forEach(function (sidebar) {
					if (sidebar.getAttribute('data-cf-filter-collapse-init')) {
						return;
					}

					sidebar.setAttribute('data-cf-filter-collapse-init', 'true');
					sidebar.classList.add('cf-filter-collapse-enabled');

					sidebar.querySelectorAll('.widget_product_categories, .widget_price_filter, .woocommerce-widget-layered-nav').forEach(function (widget, index) {
						var title = widget.querySelector(':scope > .widget-title');
						var panel = Array.prototype.find.call(widget.children, function (child) {
							return child.matches('ul, form');
						});

						if (!title || !panel) {
							return;
						}

						if (!panel.id) {
							panel.id = 'cf-filter-panel-' + sidebar.className.replace(/\s+/g, '-') + '-' + index;
						}

						title.setAttribute('role', 'button');
						title.setAttribute('tabindex', '0');
						title.setAttribute('aria-controls', panel.id);
						title.setAttribute('aria-expanded', 'false');

						var toggleWidget = function () {
							var expanded = widget.classList.toggle('expanded');
							title.setAttribute('aria-expanded', expanded ? 'true' : 'false');
						};

						title.addEventListener('click', toggleWidget);
						title.addEventListener('keydown', function (event) {
							if ('Enter' === event.key || ' ' === event.key) {
								event.preventDefault();
								toggleWidget();
							}
						});
					});
				});
			};

			initShopFilters(document);
			document.body.addEventListener('experimental-flatsome-pjax-request-done', function () {
				initShopFilters(document);
			});
		});
	</script>
</main>

<?php
get_footer( 'shop' );
