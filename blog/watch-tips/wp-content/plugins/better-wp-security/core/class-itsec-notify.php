<?php

/**
 * Handles sending notifications to users
 *
 * @package iThemes-Security
 * @since   4.5
 */
class ITSEC_Notify {

	private
		$queue;

	function __construct() {

		global $itsec_globals;

		$this->queue = get_site_option( 'itsec_message_queue' );

		if ( isset( $itsec_globals['settings']['digest_email'] ) && $itsec_globals['settings']['digest_email'] === true ) {

			if ( defined( 'ITSEC_NOTIFY_USE_CRON' ) && true === ITSEC_NOTIFY_USE_CRON ) {

				add_action( 'itsec_digest_email', array( $this, 'init' ) ); //Action to execute during a cron run.

				//schedule digest email
				if ( false === wp_next_scheduled( 'itsec_digest_email' ) ) {
					wp_schedule_event( time(), 'daily', 'itsec_digest_email' );
				}

			} else {

				//Send digest if it has been 24 hours
				if (
					get_site_transient( 'itsec_notification_running' ) === false && (
						$this->queue === false ||
						(
							is_array( $this->queue ) &&
							isset( $this->queue['last_sent'] ) &&
							$this->queue['last_sent'] < ( $itsec_globals['current_time_gmt'] - 86400 )
						)
					)
				) {
					add_action( 'init', array( $this, 'init' ) );
				}

			}

		}

	}

	/**
	 * Processes and sends daily digest message
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function init() {

		global $itsec_globals, $itsec_lockout;

		if ( is_404() || ( ( ! defined( 'ITSEC_NOTIFY_USE_CRON' ) || false === ITSEC_NOTIFY_USE_CRON ) && get_site_transient( 'itsec_notification_running' ) !== false ) ) {
			return;
		}

		if ( ( ! defined( 'ITSEC_NOTIFY_USE_CRON' ) || false === ITSEC_NOTIFY_USE_CRON ) ) {
			set_site_transient( 'itsec_notification_running', true, 3600 );
		}

		$messages     = false;
		$has_lockouts = true; //assume a lockout has occured by default

		if ( isset( $this->queue['messages'] ) && sizeof( $this->queue['messages'] ) > 0 ) {
			$messages = $this->queue['messages'];
		}

		$host_count = sizeof( $itsec_lockout->get_lockouts( 'host', true ) );
		$user_count = sizeof( $itsec_lockout->get_lockouts( 'user', true ) );

		if ( $host_count == 0 && $user_count == 0 ) {

			$has_lockouts    = false;
			$lockout_message = __( 'There have been no lockouts since the last email check.', 'it-l10n-better-wp-security' );

		} elseif ( $host_count === 0 && $user_count > 1 ) {

			$lockout_message = sprintf(
				'%s %s %s',
				__( 'There have been', 'it-l10n-better-wp-security' ),
				$user_count,
				__( 'users or usernames locked out for attempting to log in with incorrect credentials.', 'it-l10n-better-wp-security' )
			);

		} elseif ( $host_count === 0 && $user_count == 1 ) {

			$lockout_message = sprintf(
				'%s %s %s',
				__( 'There has been', 'it-l10n-better-wp-security' ),
				$user_count,
				__( 'user or username locked out for attempting to log in with incorrect credentials.', 'it-l10n-better-wp-security' )
			);

		} elseif ( $host_count == 1 && $user_count === 0 ) {

			$lockout_message = sprintf(
				'%s %s %s',
				__( 'There has been', 'it-l10n-better-wp-security' ),
				$host_count,
				__( 'host locked out.', 'it-l10n-better-wp-security' )
			);

		} elseif ( $host_count > 1 && $user_count === 0 ) {

			$lockout_message = sprintf(
				'%s %s %s',
				__( 'There have been', 'it-l10n-better-wp-security' ),
				$host_count,
				__( 'hosts locked out.', 'it-l10n-better-wp-security' )
			);

		} else {

			$lockout_message = sprintf(
				'%s %s %s %s %s %s %s',
				__( 'There have been', 'it-l10n-better-wp-security' ),
				$user_count + $host_count,
				__( 'lockout(s) including', 'it-l10n-better-wp-security' ),
				$user_count,
				__( 'user(s) and', 'it-l10n-better-wp-security' ),
				$host_count,
				__( 'host(s) locked out of your site.', 'it-l10n-better-wp-security' )
			);

		}

		if ( $has_lockouts !== false || $messages !== false ) {

			$module_message = '';

			if ( is_array( $messages ) ) {

				foreach ( $messages as $message ) {

					if ( is_string( $message ) ) {
						$module_message .= '<p>' . $message . '</p>';
					}

				}

			}

			$body = sprintf(
				'<p>%s,</p><p>%s <a href="%s">%s</a></p><p><strong>%s: </strong>%s</p>%s<p>%s %s</p><p>%s <a href="%s">%s</a>.</p>',
				__( 'Dear Site Admin', 'it-l10n-better-wp-security' ),
				__( 'The following is a summary of security related activity on your site. For details please visit', 'it-l10n-better-wp-security' ),
				wp_login_url( get_admin_url( '', 'admin.php?page=toplevel_page_itsec_logs' ) ),
				__( 'the security logs', 'it-l10n-better-wp-security' ),
				__( 'Lockouts', 'it-l10n-better-wp-security' ),
				$lockout_message,
				$module_message,
				__( 'This email was generated automatically by' ),
				$itsec_globals['plugin_name'],
				__( 'To change your email preferences please visit', 'it-l10n-better-wp-security' ),
				wp_login_url( get_admin_url( '', 'admin.php?page=toplevel_page_itsec_settings' ) ),
				__( 'the plugin settings', 'it-l10n-better-wp-security' )
			);

			//Setup the remainder of the email
			$subject = '[' . get_option( 'siteurl' ) . '] ' . __( 'Daily Security Digest', 'it-l10n-better-wp-security' );
			$subject = apply_filters( 'itsec_lockout_email_subject', $subject );
			$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";

			$this->send_mail( $subject, $body, $headers );

		}

		$this->queue = array(
			'last_sent' => $itsec_globals['current_time_gmt'],
			'messages'  => array(),
		);

		update_site_option( 'itsec_message_queue', $this->queue );

	}

	/**
	 * Enqueue or send notification accordingly
	 *
	 * @since 4.5
	 *
	 * @param int        $type 1 for lockout or 2 for custom message
	 * @param null|array $body Custom message information to send
	 *
	 * @return bool whether the message was successfully enqueue or sent
	 */
	public function notify( $body = null ) {

		global $itsec_globals;

		$allowed_tags = array(
			'a'      => array(
				'href' => array(),
			),
			'em'     => array(),
			'p'      => array(),
			'strong' => array(),
			'table'  => array(
				'border' => array(),
				'style'  => array(),
			),
			'tr'     => array(),
			'td'     => array(
				'colspan' => array(),
			),
			'th'     => array(),
			'br'     => array(),
			'h4'     => array(),
		);

		if ( isset( $itsec_globals['settings']['digest_email'] ) && $itsec_globals['settings']['digest_email'] === true ) {

			if ( ! in_array( wp_kses( $body, $allowed_tags ), $this->queue['messages'] ) ) {

				$this->queue['messages'][] = wp_kses( $body, $allowed_tags );

				update_site_option( 'itsec_message_queue', $this->queue );

			}

			return true;

		} elseif ( isset( $itsec_globals['settings']['email_notifications'] ) && $itsec_globals['settings']['email_notifications'] === true ) {

			$subject = trim( sanitize_text_field( $body['subject'] ) );
			$message = wp_kses( $body['message'], $allowed_tags );

			if ( isset( $body['headers'] ) ) {

				$headers = $body['headers'];

			} else {

				$headers = '';

			}

			$attachments = isset( $body['attachments'] ) && is_array( $body['attachments'] ) ? $body['attachments'] : array();

			return $this->send_mail( $subject, $message, $headers, $attachments );

		}

		return true;

	}

	/**
	 * Sends email to recipient
	 *
	 * @since 4.5
	 *
	 * @param string       $subject     Email subject
	 * @param string       $message     Message contents
	 * @param string|array $headers     Optional. Additional headers.
	 * @param string|array $attachments Optional. Files to attach.
	 *
	 * @return bool Whether the email contents were sent successfully.
	 */
	private function send_mail( $subject, $message, $headers = '', $attachments = array() ) {

		global $itsec_globals;

		$recipients  = $itsec_globals['settings']['notification_email'];
		$all_success = true;

		add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

		foreach ( $recipients as $recipient ) {

			if ( is_email( trim( $recipient ) ) ) {

				if ( defined( 'ITSEC_DEBUG' ) && ITSEC_DEBUG === true ) {
					$message .= '<p>' . __( 'Debug info (source page): ' . esc_url( $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ) . '</p>';
				}

				$success = wp_mail( trim( $recipient ), $subject, '<html>' . $message . '</html>', $headers );

				if ( $all_success === true && $success === false ) {
					$all_success = false;
				}

			}

		}

		remove_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

		return $all_success;

	}

	/**
	 * Set HTML content type for email
	 *
	 * @since 4.5
	 *
	 * @return string html content type
	 */
	public function wp_mail_content_type() {

		return 'text/html';

	}

}
