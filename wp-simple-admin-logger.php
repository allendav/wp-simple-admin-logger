<?php
/*
Plugin Name: WP Simple Admin Logger
*/

class WP_Simple_Admin_Logger {
	private static $instance;

	public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	private function __clone() {
	}

	private function __wakeup() {
	}

	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		add_submenu_page(
			'tools.php',
			__( 'WP Simple Admin Logger', 'wpsal' ),
			__( 'WP Simple Admin Logger', 'wpsal' ),
			'manage_options',
			'wp-simple-admin-logger-page',
			array( $this, 'page' )
		);
	}

	public function page() {
		global $title;

		$log_lines = get_option( 'wpsal_log', false );
		if ( ! is_array( $log_lines ) ) {
			$log_lines = array( __( 'Log is empty', 'wpsal' ) );
		}
		$log_text = implode( "\n", $log_lines );

		?>
			<h2>
				<?php echo esc_html( $title ); ?>
			</h2>
			<p>
				<?php esc_html_e( "To add an entry, do something like: wp_simple_admin_logger( 'wut' );" ); ?>
			</p>
			<p>
				<em>
					<?php esc_html_e( 'Note: Log entries are in REVERSE chronological order.', 'wpsal' ); ?>
				</em>
			</p>
			<div style="font-family: monospace;">
				<?php echo nl2br( esc_html( $log_text ) ); ?>
			</div>
		<?php
	}

}

WP_Simple_Admin_Logger::getInstance();

function wp_simple_admin_logger( $message ) {
	$log_lines = get_option( 'wpsal_log', array() );
	if ( ! is_array( $log_lines ) ) {
		$log_lines = array();
	}

	$entry_datetime = date( 'c', current_time( 'timestamp' ) );
	array_unshift( $log_lines, "{$entry_datetime} {$message}" );

	$log_lines = array_slice( $log_lines, 0, 100 );
	update_option( 'wpsal_log', $log_lines );
}
