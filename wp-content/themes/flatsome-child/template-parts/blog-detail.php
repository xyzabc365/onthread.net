<?php
/**
 * Blog detail layout.
 *
 * @package Flatsome_Child
 */

defined('ABSPATH') || exit;

if (empty($onthread_blog_detail) || !is_array($onthread_blog_detail)) {
	return;
}

$detail = $onthread_blog_detail;
$categories = empty($detail['categories']) || !is_array($detail['categories']) ? array() : $detail['categories'];
$share_url = rawurlencode($detail['url']);
$share_title = rawurlencode(wp_strip_all_tags($detail['title']));
$share_image = empty($detail['image']) ? '' : rawurlencode($detail['image']);
$latest_items = function_exists('onthread_get_blog_latest_items') ? onthread_get_blog_latest_items(5) : array();
$category_items = function_exists('onthread_get_blog_category_items') ? onthread_get_blog_category_items() : array();
?>

<div class="cf-blog-detail-page">
	<div class="cf-container cf-blog-detail-layout">
		<main class="cf-blog-detail-main">
			<article class="cf-blog-detail-article">
				<?php if (!empty($detail['image'])) : ?>
					<figure class="cf-blog-detail-media">
						<img src="<?php echo esc_url($detail['image']); ?>" alt="<?php echo esc_attr(empty($detail['image_alt']) ? $detail['title'] : $detail['image_alt']); ?>" loading="lazy">
					</figure>
				<?php endif; ?>

				<header class="cf-blog-detail-header">
					<h1><?php echo esc_html($detail['title']); ?></h1>
					<ul class="cf-blog-detail-meta">
						<li><span>by</span> <?php echo esc_html($detail['author']); ?></li>
						<li><time datetime="<?php echo esc_attr($detail['date_iso']); ?>"><?php echo esc_html($detail['date']); ?></time></li>
						<?php if ($categories) : ?>
							<li>
								<?php foreach ($categories as $index => $category) : ?>
									<?php
									$category_name = is_array($category) ? $category['name'] : $category;
									$category_url = is_array($category) && !empty($category['url']) ? $category['url'] : '';
									?>
									<?php if ($category_url) : ?>
										<a href="<?php echo esc_url($category_url); ?>"><?php echo esc_html($category_name); ?></a><?php echo $index + 1 < count($categories) ? esc_html(', ') : ''; ?>
									<?php else : ?>
										<span><?php echo esc_html($category_name); ?></span><?php echo $index + 1 < count($categories) ? esc_html(', ') : ''; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</li>
						<?php endif; ?>
					</ul>
				</header>

				<div class="cf-blog-detail-content entry-content">
					<?php echo wp_kses_post($detail['content']); ?>
				</div>

				<footer class="cf-blog-detail-share">
					<span>Share</span>
					<div>
						<a href="<?php echo esc_url('https://www.facebook.com/sharer.php?u=' . $share_url); ?>" target="_blank" rel="noopener">Facebook</a>
						<a href="<?php echo esc_url('https://twitter.com/share?url=' . $share_url); ?>" target="_blank" rel="noopener">X</a>
						<a href="<?php echo esc_url('https://pinterest.com/pin/create/button/?url=' . $share_url . ($share_image ? '&media=' . $share_image : '') . '&description=' . $share_title); ?>" target="_blank" rel="noopener">Pinterest</a>
						<a href="<?php echo esc_url('https://wa.me/?text=' . $share_url); ?>" target="_blank" rel="noopener">WhatsApp</a>
					</div>
				</footer>
			</article>

			<?php if (!empty($detail['previous']) || !empty($detail['next'])) : ?>
				<nav class="cf-blog-detail-nav" aria-label="Post navigation">
					<?php if (!empty($detail['previous'])) : ?>
						<a class="cf-blog-detail-nav__item cf-blog-detail-nav__item--prev" href="<?php echo esc_url($detail['previous']['url']); ?>">
							<small>Previous post</small>
							<span><?php echo esc_html($detail['previous']['title']); ?></span>
						</a>
					<?php endif; ?>
					<?php if (!empty($detail['next'])) : ?>
						<a class="cf-blog-detail-nav__item cf-blog-detail-nav__item--next" href="<?php echo esc_url($detail['next']['url']); ?>">
							<small>Next post</small>
							<span><?php echo esc_html($detail['next']['title']); ?></span>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<?php if (!empty($detail['comments_template'])) : ?>
				<div class="cf-blog-comments">
					<?php comments_template(); ?>
				</div>
			<?php else : ?>
				<section class="cf-blog-comments cf-blog-comments--preview">
					<h3>Post a Comment</h3>
					<p>Comments are available for published blog posts.</p>
				</section>
			<?php endif; ?>
		</main>

		<aside class="cf-blog-sidebar" aria-label="Blog sidebar">
			<section class="cf-blog-sidebar-widget">
				<h2>Categories</h2>
				<ul class="cf-blog-category-list">
					<?php foreach ($category_items as $category_item) : ?>
						<li>
							<a href="<?php echo esc_url($category_item['url']); ?>"><?php echo esc_html($category_item['name']); ?></a>
							<span><?php echo esc_html($category_item['count']); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<?php if ($latest_items) : ?>
				<section class="cf-blog-sidebar-widget">
					<h2>Latest Posts</h2>
					<ul class="cf-blog-latest-list">
						<?php foreach ($latest_items as $latest_item) : ?>
							<li>
								<a class="cf-blog-latest-list__thumb" href="<?php echo esc_url($latest_item['url']); ?>">
									<img src="<?php echo esc_url($latest_item['image']); ?>" alt="<?php echo esc_attr($latest_item['title']); ?>" loading="lazy">
								</a>
								<div>
									<a href="<?php echo esc_url($latest_item['url']); ?>"><?php echo esc_html($latest_item['title']); ?></a>
									<time><?php echo esc_html($latest_item['date']); ?></time>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<section class="cf-blog-sidebar-widget">
				<h2>Search</h2>
				<form class="cf-blog-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
					<label class="screen-reader-text" for="cf-blog-search-field">Search for:</label>
					<input id="cf-blog-search-field" type="search" name="s" placeholder="Search ..." value="<?php echo esc_attr(get_search_query()); ?>">
					<button type="submit">Search</button>
				</form>
			</section>
		</aside>
	</div>
</div>
