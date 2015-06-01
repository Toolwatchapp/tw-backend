<?php
/**
 * The checkbox customize control extends the WP_Customize_Control class.  This class allows 
 * developers to create checkbox settings within the WordPress theme customizer.
  */

/**
 * Checkbox customize control class.
 *
 * @since 0.9.11
 */
class Omega_Customize_Control_Checkbox extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 0.9.11
	 */
	public $type = 'checkbox';
	public $extra = ''; // we add this for the extra description

	/**
	 * Displays the checkbox on the customize screen.
	 *
	 * @since 0.9.11
	 */
	public function render_content() { ?>
		<label>
			<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
			<?php echo esc_html( $this->label ); ?>
			<p class="description"><?php echo esc_html( $this->extra ); ?></p>
		</label>
	<?php }
}

?>