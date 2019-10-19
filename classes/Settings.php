<?php


namespace Palasthotel\WordPress\Datable;


class Settings {

	const MENU_SLUG = "datable_settings";

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_init(){
		register_setting( self::MENU_SLUG, Plugin::OPTION_POST_TYPES );
		register_setting( self::MENU_SLUG, Plugin::OPTION_POST_TYPE_FOR_AUTO_GENERATION );
		register_setting( self::MENU_SLUG, Plugin::OPTION_POST_AUTO_GENERATE_NUMBER );
	}

	public function admin_menu() {
		add_submenu_page(
			"edit.php?post_type=" . $this->plugin->post_types->getDatableSlug(),
			'Settings ‹ Datables',
			'Settings',
			'manage_options',
			self::MENU_SLUG,
			array(
				$this,
				"render_settings",
			) );
	}

	public function getPostTypes(){
		$selected = get_option(Plugin::OPTION_POST_TYPES);
		return (is_array($selected))? $selected: array();
	}

	public function isDatablePostType($post_type_name){
		return in_array($post_type_name, $this->getPostTypes());
	}

	public function render_settings() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Settings ‹ Datables', Plugin::DOMAIN ); ?></h2>

			<form method="post" action="options.php">

				<table class="form-table">
					<tr>

						<th scope="row">
							<label for="<?php echo Plugin::OPTION_POST_TYPES; ?>"><?php _e("Post types", Plugin::DOMAIN); ?></label>
							<p class="description"><?php _e("Select all post types that should be datable."); ?></p>
						</th>
						<td>
							<?php
							settings_fields( self::MENU_SLUG );
							$post_types = get_post_types( array('public' => true, 'show_ui' => true), 'objects' );
							$option_name = Plugin::OPTION_POST_TYPES;
							echo "<p>";
							foreach ( $post_types as $key => $post_type ) {
								$name = $post_type->name;
								$label = $post_type->label;
								$checked = ($this->isDatablePostType($name))? "checked='checked'":"";
								echo "<label><input id='$option_name' type='checkbox' name='{$option_name}[]' $checked value='$name' /> $label </label><br>";
							}
							echo "</p>";
							?>
						</td>
					</tr>
				</table>

				<h3><?php _e("Auto generation", Plugin::DOMAIN ); ?></h3>

				<p class="description"><?php _e("You can either assign a single date to a content manually or you can use the automatic content generation for a Datables collection.", Plugin::DOMAIN); ?></p>
				<p class="description"><?php _e("Datables are internal copy templates that hold a series of dates.", Plugin::DOMAIN); ?></p>


				<table class="form-table">
					<tr>
						<th scope="row">
							<label
									for="<?php echo Plugin::OPTION_POST_TYPE_FOR_AUTO_GENERATION ?>"
							>
								<?php _e("Post type", Plugin::DOMAIN); ?>
							</label>
						</th>
						<td>
							<select
									id="<?php echo Plugin::OPTION_POST_TYPE_FOR_AUTO_GENERATION; ?>"
									name="<?php echo Plugin::OPTION_POST_TYPE_FOR_AUTO_GENERATION; ?>"
							>
								<option value=""></option>
								<?php
								$auto_post_type = get_option(Plugin::OPTION_POST_TYPE_FOR_AUTO_GENERATION);
								foreach ($post_types as $post_type){
									$name = $post_type->name;
									$label = $post_type->label;
									if(!$this->isDatablePostType($name)) continue;
									$selected = ($auto_post_type === $name)? "selected='selected'":"";
									echo "<option value='$name' $selected>$label</option>";
								}
								?>
							</select>
							<p class="description"><?php _e("Default post type for auto generated contents."); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo Plugin::OPTION_POST_AUTO_GENERATE_NUMBER; ?>">
								<?php _e("Number"); ?>
							</label>
						</th>
						<td>
							<input
									name="<?php echo Plugin::OPTION_POST_AUTO_GENERATE_NUMBER; ?>"
									type="number"
									min="1"
									max="100"
									value="<?php echo get_option(Plugin::OPTION_POST_AUTO_GENERATE_NUMBER) ?>"
							/>
							<p class="description"><?php _e("Number of contents that are generated in the future.", Plugin::DOMAIN); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

}