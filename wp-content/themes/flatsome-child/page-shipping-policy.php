<?php
/**
 * Shipping policy page.
 *
 * @package Flatsome_Child
 */

defined('ABSPATH') || exit;

get_header();

$home_url = home_url('/');
$tracking_url = function_exists('onthread_get_page_url')
	? onthread_get_page_url('order-tracking', home_url('/order-tracking/'))
	: home_url('/order-tracking/');
$contact_url = function_exists('onthread_get_page_url')
	? onthread_get_page_url('contact', home_url('/contact/'))
	: home_url('/contact/');
?>

<div class="cf-policy-page">
	<section class="cf-policy-hero">
		<div class="cf-container">
			<nav class="cf-policy-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'flatsome-child'); ?>">
				<a href="<?php echo esc_url($home_url); ?>">Home</a>
				<span aria-hidden="true">/</span>
				<span>Shipping Policy and Tax</span>
			</nav>
			<h1>Shipping Policy and Tax</h1>
		</div>
	</section>

	<section class="cf-policy-body">
		<div class="cf-container">
			<article class="cf-policy-content">
				<nav class="cf-policy-inline-breadcrumbs" aria-label="<?php esc_attr_e('Page path', 'flatsome-child'); ?>">
					<a href="<?php echo esc_url($home_url); ?>">Home</a>
					<span>Shipping Policy and Tax</span>
				</nav>

				<h2><strong>Order confirmation:</strong></h2>
				<p>After your order is placed, OnThread will send an email confirming that the order has been received and processed. Please check your inbox and spam folder for this confirmation.</p>

				<p><strong>Delivery location:</strong> At the moment, we ship within the United States.</p>
				<p>We are unable to ship to US military bases at this time. We also do not support shipping to multiple addresses within the same order.</p>

				<p><strong>Cut-off time:</strong> 12:00 AM (GMT - 6)</p>
				<p><strong>Shipping Time:</strong> Delivery time = Handling time + Transit time</p>
				<p><strong>Handling time:</strong> Orders usually take 1 - 3 business days to prepare before shipment.</p>
				<p>If your order has not shipped yet, it is still being prepared. If anything unusual affects fulfillment, we will notify you. Once your order ships, tracking information will be sent so you can follow the delivery progress.</p>
				<p><strong>Transit time:</strong> Delivery usually takes 4 - 7 business days after the carrier receives the package.</p>

				<p><strong>*Please note:</strong><br>
					Business days are Monday through Friday and do not include Saturday or Sunday.<br>
					During peak seasons or promotional periods, delivery times may be slightly extended.
				</p>

				<p><strong>Shipping fee:</strong> We offer free shipping on every order.</p>
				<p><strong>Shipping carriers:</strong> We work with reliable carriers such as UPS, FedEx, DHL, and USPS.</p>

				<h2>Track your order</h2>
				<p>To track an order, visit our <a href="<?php echo esc_url($tracking_url); ?>">Order Tracking</a> page and enter your order details.</p>
				<p>Tracking updates can take 5 - 7 business days to refresh for some carriers. If your order was placed more than 10 business days ago and you have not received tracking information, please contact us.</p>

				<h2>Tax policy</h2>
				<p>Applicable sales tax, when required, is calculated and displayed during checkout. Any tax shown in the order total is based on the qualifying items and shipping destination.</p>

				<h2>Shipping delay and item not receive</h2>
				<p>Shipping estimates begin on the shipment date, not the order date. Incorrect addresses, carrier delays, weather events, and other unexpected issues can extend delivery time.</p>
				<p>If tracking shows that your order was delivered but you have not received it, please contact the carrier first so they can help locate the package. If the issue continues, contact OnThread and we will help review the case.</p>
				<p>Customers are responsible for providing accurate billing and shipping information. If you notice an address error, contact us as soon as possible. Once an order has shipped, we may not be able to update the delivery address.</p>

				<p>If you have any questions, please contact us through the <a href="<?php echo esc_url($contact_url); ?>">Contact us</a> page or call +1 (406) 434-1931.</p>

				<div class="cf-policy-company">
					<h2>Onthread is owned by OnThread LLC</h2>
					<p>Monday - Friday: 8:30AM-4:30PM</p>
					<p><strong>Phone:</strong> +1 (406) 434-1931</p>
					<p><strong>Address:</strong> 1001 S MAIN ST STE 600, KALISPELL, MT 59901, United States</p>
					<p><strong>Company:</strong> OnThread LLC</p>
				</div>
			</article>
		</div>
	</section>
</div>

<?php
get_footer();
