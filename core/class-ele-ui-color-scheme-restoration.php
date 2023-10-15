<?php

// Exit if accessed directly.
if (!defined('ABSPATH'))
	exit;

/**
 * HELPER COMMENT START
 * 
 * This is the main class that is responsible for registering
 * the core functions, including the files and setting up all features. 
 * 
 * To add a new class, here's what you need to do: 
 * 1. Add your new class within the following folder: core/includes/classes
 * 2. Create a new variable you want to assign the class to (as e.g. public $helpers)
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Ele_Ui_Color_Scheme_Restoration_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 * 
 * HELPER COMMENT END
 */

if (!class_exists('Ele_Ui_Color_Scheme_Restoration')):

	/**
	 * Main Ele_Ui_Color_Scheme_Restoration Class.
	 *
	 * @package		ELEUICOLOR
	 * @subpackage	Classes/Ele_Ui_Color_Scheme_Restoration
	 * @since		1.0.0
	 * @author		George Nicolaou
	 */
	final class Ele_Ui_Color_Scheme_Restoration
	{

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Ele_Ui_Color_Scheme_Restoration
		 */
		private static $instance;

		/**
		 * ELEUICOLOR helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Ele_Ui_Color_Scheme_Restoration_Helpers
		 */
		public $helpers;

		/**
		 * ELEUICOLOR settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Ele_Ui_Color_Scheme_Restoration_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone()
		{
			_doing_it_wrong(__FUNCTION__, __('You are not allowed to clone this class.', 'ele-ui-color-scheme-restoration'), '1.0.0');
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup()
		{
			_doing_it_wrong(__FUNCTION__, __('You are not allowed to unserialize this class.', 'ele-ui-color-scheme-restoration'), '1.0.0');
		}

		/**
		 * Main Ele_Ui_Color_Scheme_Restoration Instance.
		 *
		 * Insures that only one instance of Ele_Ui_Color_Scheme_Restoration exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Ele_Ui_Color_Scheme_Restoration	The one true Ele_Ui_Color_Scheme_Restoration
		 */
		public static function instance()
		{
			if (!isset(self::$instance) && !(self::$instance instanceof Ele_Ui_Color_Scheme_Restoration)) {
				self::$instance = new Ele_Ui_Color_Scheme_Restoration;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers = new Ele_Ui_Color_Scheme_Restoration_Helpers();
				self::$instance->settings = new Ele_Ui_Color_Scheme_Restoration_Settings();

				//Fire the plugin logic
				new Ele_Ui_Color_Scheme_Restoration_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action('ELEUICOLOR/plugin_loaded');
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes()
		{
			require_once ELEUICOLOR_PLUGIN_DIR . 'core/includes/classes/class-ele-ui-color-scheme-restoration-helpers.php';
			require_once ELEUICOLOR_PLUGIN_DIR . 'core/includes/classes/class-ele-ui-color-scheme-restoration-settings.php';
			require_once ELEUICOLOR_PLUGIN_DIR . 'core/includes/classes/class-ele-ui-color-scheme-restoration-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks()
		{
			add_action('plugins_loaded', array(self::$instance, 'load_textdomain'));
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain()
		{
			load_plugin_textdomain('ele-ui-color-scheme-restoration', "", dirname(plugin_basename(ELEUICOLOR_PLUGIN_FILE)) . '/languages/');

		}

		/**
		 * Destroy options when plugin is deleted or color need a reset back to defaults.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function ele_ui_color_scheme_destroy_options()
		{

			delete_option('ele-ui-color-scheme-restoration-settings');

		}
		/**
		 * Create options when plugin is activated.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */

		public function ele_ui_color_scheme_create_options_with_defaults()
		{
			//setup default color srrays 

			$default_dark_colors = array(
				'--e-a-bg-default' => '#1f2124',
				'--e-a-dark-bg' => '#26292C',
				'--e-a-dark-color-txt' => '#A4AFB7',
				'--e-a-dark-color-txt-hover' => '#d5d8dc',
				'--e-a-btn-bg-primary' => '#39B54A',
				'--e-a-btn-bg-primary-hover' => '#36A046',
				'--e-a-btn-bg-primary-active' => '#39B54A',
				'--e-a-bg-hover' => '#71D7F7',
				'--e-a-bg-active' => '#71D7F7',
				'--e-a-border-color' => '#000000',
				'--e-a-border-color-focus' => '#000000',
				'--e-a-bg-primary' => '#71d7f7',
				'--e-a-color-txt' => '#ffffff',
				'--e-a-color-primary-bold' => '#ffffff',
				'--e-a-color-info' => '#2563eb',
				'--e-a-border-color-bold' => '#ffffff',
				'--e-a-bg-active-bold' => '##71D7F7',
			);

			$default_light_colors = array(
				'--e-a-bg-default' => '#E6E9EC',
				'--e-a-dark-bg' => '#93003C',
				'--e-a-dark-color-txt' => '#A4AFB7',
				'--e-a-dark-color-txt-hover' => '#FFFFFF',
				'--e-a-btn-bg-primary' => '#39B54A',
				'--e-a-btn-bg-primary-hover' => '#36A046',
				'--e-a-btn-bg-primary-active' => '#39B54A',
				'--e-a-bg-hover' => '#71D7F7',
				'--e-a-bg-active' => '#71D7F7',
				'--e-a-border-color' => '#000000',
				'--e-a-border-color-focus' => '#000000',
				'--e-a-bg-primary' => '#71d7f7',
				'--e-a-color-txt' => '#000000',
				'--e-a-color-primary-bold' => '#ffffff',
				'--e-a-color-info' => '#2563EB',
				'--e-a-border-color-bold' => '#000000',
				'--e-a-bg-active-bold' => '##71D7F7',

			);

			$default_brand_colors = array(
				'--brand-color' => '#71D7F7',
				'--brand-color-hover' => '#65C1DE',
				'--brand-color-active' => '#65C1DE',

			);

			// Retrieve the posted color values
			$default_color_options = array(
				'dark_mode_colors' => $default_dark_colors,
				'light_mode_colors' => $default_light_colors,
				'brand_colors' => $default_brand_colors
			);

			// Update the options with the new color values
			add_option('ele-ui-color-scheme-restoration-settings', $default_color_options);
			////ELEUICOLOR()->ele_ui_color_scheme_logger("Running Reset");
			$current_light_colors = get_option('light_mode_colors');
			$current_dark_colors = get_option('dark_mode_colors');
			$current_ele_ui_color_scheme_restoration_settings = get_option('ele-ui-color-scheme-restoration-settings');
			////ELEUICOLOR()->ele_ui_color_scheme_logger($current_dark_colors);
			////ELEUICOLOR()->ele_ui_color_scheme_logger($current_light_colors);
			////ELEUICOLOR()->ele_ui_color_scheme_logger($current_ele_ui_color_scheme_restoration_settings);
		}

		function ele_ui_color_scheme_save_colors()
		{
			// Retrieve the posted color values
			$dark_mode_colors = $_POST['dark_mode'];
			$light_mode_colors = $_POST['light_mode'];
			$brand_colors = $_POST['brand_colors'];
			//echo '<script>console.log(' . json_encode($dark_mode_colors) . ');</script>';
			// Construct the $color_options array
			$color_options = array(
				'dark_mode_colors' => $dark_mode_colors,
				'light_mode_colors' => $light_mode_colors,
				'brand_colors' => $brand_colors
			);

			// Update the options with the new color values
			update_option('ele-ui-color-scheme-restoration-settings', $color_options);
			//echo '<pre>';
			//var_dump($color_options);
			//echo '</pre>';
			//wp_die();

		}


		/**
		 * Reset options when reset is clicked.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function ele_ui_color_scheme_reset_colors()
		{

			ELEUICOLOR()->ele_ui_color_scheme_destroy_options();
			ELEUICOLOR()->ele_ui_color_scheme_create_options_with_defaults();
		}
		// Function to convert RGB colors to hex format
		public function ele_ui_color_scheme_rgbToHex($color)
		{
			$color = trim($color, 'rgba()'); // Remove 'rgba()' from the color string
			$color = explode(',', $color); // Split the color into RGB components
			$hex = '#';
			foreach ($color as $component) {
				$component = intval($component); // Convert the component to integer
				$hex .= str_pad(dechex($component), 2, '0', STR_PAD_LEFT); // Convert the component to hex and append to the final hex string
			}
			return $hex;
		}
		/**
		 * Reset options when reset is clicked.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function ele_ui_color_scheme_logger($variable)
		{
			echo '<script>console.log(' . json_encode($variable) . ');</script>';
		}


	}

endif; // End if class_exists check.