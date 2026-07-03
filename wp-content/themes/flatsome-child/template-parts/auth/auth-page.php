<?php
/**
 * Custom account access pages.
 *
 * @package Flatsome_Child
 */

defined( 'ABSPATH' ) || exit;

$auth_page    = onthread_get_auth_page_from_request();
$notice       = get_query_var( 'auth_notice' );
$notice       = $notice ? wp_kses_post( rawurldecode( $notice ) ) : '';
$is_register  = 'register' === $auth_page;
$is_lost      = 'lost-password' === $auth_page;
$title        = 'Welcome back';
$eyebrow      = 'Account access';
$intro        = 'Sign in to view your orders, saved details, and customer support updates.';
$submit_label = 'Sign in';

if ( $is_register ) {
	$title        = 'Create your account';
	$intro        = 'Save your details, track future orders, and checkout faster next time.';
	$submit_label = 'Create account';
} elseif ( $is_lost ) {
	$title        = 'Reset your password';
	$intro        = 'Enter your username or email address and we will send password reset instructions.';
	$submit_label = 'Send reset link';
}

get_header();
?>

<main class="cf-auth-page">
	<section class="cf-auth-hero">
		<div class="cf-container">
			<div class="cf-auth-layout">
				<div class="cf-auth-copy">
					<p class="cf-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php echo esc_html( $intro ); ?></p>

					<div class="cf-auth-benefits" aria-label="Account benefits">
						<div>
							<strong>Fast checkout</strong>
							<span>Keep your account details ready for the next order.</span>
						</div>
						<div>
							<strong>Order history</strong>
							<span>Review past purchases and account activity in one place.</span>
						</div>
						<div>
							<strong>Customer support</strong>
							<span>Use your account details to make support requests easier.</span>
						</div>
					</div>
				</div>

				<div class="cf-auth-panel">
					<div class="cf-auth-tabs" aria-label="Account actions">
						<a class="<?php echo 'login' === $auth_page ? 'is-active' : ''; ?>" href="<?php echo esc_url( onthread_get_login_url() ); ?>">Login</a>
						<a class="<?php echo $is_register ? 'is-active' : ''; ?>" href="<?php echo esc_url( onthread_get_register_url() ); ?>">Register</a>
					</div>

					<?php if ( $notice ) : ?>
						<div class="cf-auth-notice"><?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>

					<?php if ( $is_register ) : ?>
						<form class="cf-auth-form" method="post" action="<?php echo esc_url( onthread_get_register_url() ); ?>">
							<?php wp_nonce_field( 'onthread_register', 'onthread_register_nonce' ); ?>

							<label for="cf-register-username">Username</label>
							<input id="cf-register-username" name="username" type="text" autocomplete="username" required>

							<label for="cf-register-email">Email address</label>
							<input id="cf-register-email" name="email" type="email" autocomplete="email" required>

							<label for="cf-register-password">Password</label>
							<input id="cf-register-password" name="password" type="password" autocomplete="new-password" minlength="8" required>

							<button class="cf-button cf-button-primary" type="submit"><?php echo esc_html( $submit_label ); ?></button>
						</form>

						<p class="cf-auth-switch">Already have an account? <a href="<?php echo esc_url( onthread_get_login_url() ); ?>">Sign in</a></p>
					<?php elseif ( $is_lost ) : ?>
						<form class="cf-auth-form" method="post" action="<?php echo esc_url( onthread_get_lost_password_url() ); ?>">
							<?php wp_nonce_field( 'onthread_lost_password', 'onthread_lost_password_nonce' ); ?>

							<label for="cf-lost-login">Username or email address</label>
							<input id="cf-lost-login" name="user_login" type="text" autocomplete="username" required>

							<button class="cf-button cf-button-primary" type="submit"><?php echo esc_html( $submit_label ); ?></button>
						</form>

						<p class="cf-auth-switch">Remembered your password? <a href="<?php echo esc_url( onthread_get_login_url() ); ?>">Back to login</a></p>
					<?php else : ?>
						<form class="cf-auth-form" method="post" action="<?php echo esc_url( onthread_get_login_url() ); ?>">
							<?php wp_nonce_field( 'onthread_login', 'onthread_login_nonce' ); ?>

							<label for="cf-login-username">Username or email address</label>
							<input id="cf-login-username" name="username" type="text" autocomplete="username" required>

							<label for="cf-login-password">Password</label>
							<input id="cf-login-password" name="password" type="password" autocomplete="current-password" required>

							<div class="cf-auth-row">
								<label class="cf-auth-check" for="cf-login-remember">
									<input id="cf-login-remember" name="rememberme" type="checkbox" value="forever">
									<span>Remember me</span>
								</label>
								<a href="<?php echo esc_url( onthread_get_lost_password_url() ); ?>">Forgot password?</a>
							</div>

							<button class="cf-button cf-button-primary" type="submit"><?php echo esc_html( $submit_label ); ?></button>
						</form>

						<p class="cf-auth-switch">New here? <a href="<?php echo esc_url( onthread_get_register_url() ); ?>">Create an account</a></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
