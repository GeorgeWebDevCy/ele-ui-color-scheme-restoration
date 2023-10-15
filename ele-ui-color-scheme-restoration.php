<?php
/**
 * Ele UI Color Scheme Restoration
 *
 * @package       ELEUICOLOR
 * @author        George Nicolaou & Atif Riaz
 * @license       gplv2
 * @version       2.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Ele UI Color Scheme Restoration
 * Plugin URI:    https://www.georgenicolaou.me/plugins/ele-ui-color-scheme-restoration
 * Description:   A plugin that allows you to restore the Elementor UI back to the old colors
 * Version:       2.0.0
 * Author:        George Nicolaou & Atif Riaz
 * Author URI:    https://www.georgenicolaou.me/
 * Text Domain:   ele-ui-color-scheme-restoration
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Ele UI Color Scheme Restoration. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if (!defined('ABSPATH'))
    exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differentiate the plugin and register it properly.
 * It also contains further PHPDocs parameter for better documentation.
 * 
 * The function ELEUICOLOR() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define('ELEUICOLOR_NAME', 'Ele UI Color Scheme Restoration');

// Plugin version
define('ELEUICOLOR_VERSION', '2.0.0');

// Plugin Root File
define('ELEUICOLOR_PLUGIN_FILE', __FILE__);

// Plugin base
define('ELEUICOLOR_PLUGIN_BASE', plugin_basename(ELEUICOLOR_PLUGIN_FILE));

// Plugin Folder Path
define('ELEUICOLOR_PLUGIN_DIR', plugin_dir_path(ELEUICOLOR_PLUGIN_FILE));

// Plugin Folder URL
define('ELEUICOLOR_PLUGIN_URL', plugin_dir_url(ELEUICOLOR_PLUGIN_FILE));

/**
 * Load the main class for the core functionality
 */
require_once ELEUICOLOR_PLUGIN_DIR . 'core/class-ele-ui-color-scheme-restoration.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @return  object|Ele_Ui_Color_Scheme_Restoration
 */
function ELEUICOLOR()
{
    $plugin = Ele_Ui_Color_Scheme_Restoration::instance();

    return $plugin;

}

if (!function_exists('ele_ui_color_scheme_restoration_check_elementor')) {
    function ele_ui_color_scheme_restoration_check_elementor()
    {
        return defined('ELEMENTOR_VERSION');
    }
}

function ele_ui_color_scheme_restoration_init()
{
    if (ele_ui_color_scheme_restoration_check_elementor()) {
        require_once ELEUICOLOR_PLUGIN_DIR . 'core/class-ele-ui-color-scheme-restoration.php';
        //ELEUICOLOR()->ele_ui_color_scheme_logger("Plugin Initialized...");


        // Check if the options exist and create them if they don't
        if (!get_option('dark_mode_colors')) {
            add_option('dark_mode_colors', array());
            //ELEUICOLOR()->ele_ui_color_scheme_logger("dark_mode_color created...");
        }

        if (!get_option('light_mode_colors')) {
            add_option('light_mode_colors', array());
            //ELEUICOLOR()->ele_ui_color_scheme_logger("light_mode_color created...");
        }
        if (!get_option('brand_colors')) {
            add_option('brand_colors', array());
            //ELEUICOLOR()->ele_ui_color_scheme_logger("light_mode_color created...");
        }
        if (!get_option('ele-ui-color-scheme-restoration-settings')) {
            add_option('ele-ui-color-scheme-restoration-settings', array());
            ELEUICOLOR()->ele_ui_color_scheme_reset_colors();
            //ELEUICOLOR()->ele_ui_color_scheme_logger("ele-ui-color-scheme-restoration-settings created...");
        }
        //ELEUICOLOR()->ele_ui_color_scheme_logger("Current Options State");
        //$current_light_colors = get_option('light_mode_colors');
        //$current_dark_colors = get_option('dark_mode_colors');
        //$current_ele_ui_color_scheme_restoration_settings = get_option('ele-ui-color-scheme-restoration-settings');
        //ELEUICOLOR()->ele_ui_color_scheme_logger($current_dark_colors);
        //ELEUICOLOR()->ele_ui_color_scheme_logger($current_light_colors);
        //ELEUICOLOR()->ele_ui_color_scheme_logger($current_ele_ui_color_scheme_restoration_settings);

        return Ele_Ui_Color_Scheme_Restoration::instance();
    } else {
        // Elementor is not installed or active, so we display an admin notice.
        add_action('admin_notices', 'ele_ui_color_scheme_restoration_missing_elementor_notice');
        return;
    }
}
add_action('plugins_loaded', 'ele_ui_color_scheme_restoration_init');

function ele_ui_color_scheme_restoration_missing_elementor_notice()
{
    $message = __('Ele UI Color Scheme Restoration requires the Elementor plugin to be installed and activated.', 'ele-ui-color-scheme-restoration');
    printf('<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html($message));

    deactivate_plugins(plugin_basename(__FILE__));
}


function ele_ui_color_scheme_restoration_enqueue_scripts()
{
    // Enqueue Elementor color picker script
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');

    // Inline script to initialize the color picker
    wp_add_inline_script('wp-color-picker', '
        jQuery(document).ready(function($) {
            $(".color-picker").wpColorPicker();
        });
    ');

    wp_add_inline_script('jquery', '
        window.addEventListener("DOMContentLoaded", function(e) {
            document.querySelectorAll("input[type=color]").forEach(function(current) {
                let newEl = document.createElement("input");
                newEl.size = 8;
                newEl.value = current.value;
                newEl.pattern = "#[0-9A-Fa-f]{6}";
                newEl.style.marginLeft = "0.5em";

                current.insertAdjacentElement("afterend", newEl);

                newEl.addEventListener("input", function(e) {
                    if(e.target.validity.valid) {
                        current.value = e.target.value;
                    }
                });

                current.addEventListener("change", function(e) {
                    newEl.value = e.target.value;
                });
            });
        });
    ');
}
add_action('admin_enqueue_scripts', 'ele_ui_color_scheme_restoration_enqueue_scripts');



/**
 * Register the options page
 */
function ele_ui_color_scheme_register_options_page()
{
    add_menu_page(
        'Ele UI Color Scheme Restoration',
        'Ele UI Color Scheme',
        'manage_options',
        'ele-ui-color-scheme',
        'ele_ui_color_scheme_render_options_page',
        'dashicons-admin-generic',
        99
    );
}
add_action('admin_menu', 'ele_ui_color_scheme_register_options_page');



// Check if the reset colors button is clicked
if (isset($_POST['reset_colors'])) {
    // Check if the options exist and create them if they don't
    if (!get_option('dark_mode_colors')) {
        add_option('dark_mode_colors', array());
    }

    if (!get_option('light_mode_colors')) {
        add_option('light_mode_colors', array());
    }

    if (!get_option('brand_colors')) {
        add_option('brand_colors', array());
    }
    // Call the reset colors function
    ELEUICOLOR()->ele_ui_color_scheme_reset_colors();
}
/**
 * Render the options page
 */
function ele_ui_color_scheme_render_options_page()
{
    // Check if the form has been submitted and update the options if needed
    if (isset($_POST['reset_colors'])) {
        ELEUICOLOR()->ele_ui_color_scheme_reset_colors();
    } elseif (isset($_POST['save_colors'])) {
        ELEUICOLOR()->ele_ui_color_scheme_save_colors();

    }

    // Retrieve the saved color options
    $color_options = get_option('ele-ui-color-scheme-restoration-settings', array());
    //var_dump($color_options);
    //wp_die();
    //ELEUICOLOR()->ele_ui_color_scheme_logger($color_options);
    ?>
    <div class="wrap">
        <h1>
            <?php _e('Ele UI Color Scheme Restoration Settings', 'ele-ui-color-scheme-restoration'); ?>
        </h1>
        <form method="post" action="">

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Primary Brand Color', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                        <th>
                            <?php _e('Brand Hover Color', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                        <th>
                            <?php _e('Brand Active Border Color', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a target="_blank"
                                href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '18.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                            <input type="color" name="brand_colors[--brand-color]"
                                value="<?php echo esc_attr(strval($color_options['brand_colors']['--brand-color'])); ?>">
                        </td>
                        <td>
                            <a target="_blank"
                                href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '19.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                            <input type="color" name="brand_colors[--brand-color-hover]"
                                value="<?php echo esc_attr(strval($color_options['brand_colors']['--brand-color-hover'])); ?>">
                        </td>
                        <td>
                            <a target="_blank"
                                href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '20.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                            <input type="color" name="brand_colors[--brand-color-active]"
                                value="<?php echo esc_attr(strval($color_options['brand_colors']['--brand-color-active'])); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Where', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                        <th>
                            <?php _e('Dark Mode', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                        <th>
                            <?php _e('Light Mode', 'ele-ui-color-scheme-restoration'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php _e('Editor Background', 'ele-ui-color-scheme-restoration'); ?>
                            <a target="_blank"
                                href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '1.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-bg-default]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-bg-default'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-bg-default]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-bg-default'])); ?>">
                        </td>
                    </tr>
                    <tr>
                    <tr>
                        <td>
                            <?php _e('Header and Footer Areas', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '2.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-dark-bg]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-dark-bg'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-dark-bg]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-dark-bg'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Header and Footer Text Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '3.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-dark-color-txt]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-dark-color-txt'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-dark-color-txt]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-dark-color-txt'])); ?>">
                        </td>
                    </tr>



                    <tr>
                        <td>
                            <?php _e('Header and Footer Text Color (Hover)', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '4.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-dark-color-txt-hover]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-dark-color-txt-hover'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-dark-color-txt-hover]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-dark-color-txt-hover'])); ?>">
                        </td>
                    </tr>




                    <tr>
                        <td>
                            <?php _e('Update Button and Toggles', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '5.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-btn-bg-primary]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-btn-bg-primary'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-btn-bg-primary]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-btn-bg-primary'])); ?>">
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <?php _e('Update Button and Toggles (Hover)', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '6.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-btn-bg-primary-hover]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-btn-bg-primary-hover'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-btn-bg-primary-hover]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-btn-bg-primary-hover'])); ?>">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php _e('Update Button and Toggles (Active)', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '7.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-btn-bg-primary-active]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-btn-bg-primary-active'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-btn-bg-primary-active]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-btn-bg-primary-active'])); ?>">
                        </td>
                    </tr>



                    <tr>
                        <td>
                            <?php _e('Widget Colors On Hover', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '8.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-bg-hover]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-bg-hover'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-bg-hover]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-bg-hover'])); ?>">
                        </td>
                    </tr>





                    <tr>
                        <td>
                            <?php _e('Widget Colors On Hover (Active)', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '9.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-bg-active]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-bg-active'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-bg-active]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-bg-active'])); ?>">
                        </td>
                    </tr>




                    <tr>
                    <tr>
                        <td>
                            <?php _e('Widget Border Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '10.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-border-color]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-border-color'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-border-color]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-border-color'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Search In Focus', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '11.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-border-color-focus]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-border-color-focus'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-border-color-focus]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-border-color-focus'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Text Selector Background Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '12.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-bg-primary]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-bg-primary'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-bg-primary]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-bg-primary'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Text Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '13.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-color-txt]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-color-txt'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-color-txt]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-color-txt'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Link Hover and Global Style Active Icon Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '14.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-color-primary-bold]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-color-primary-bold'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-color-primary-bold]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-color-primary-bold'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Info Link Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '15.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-color-info]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-color-info'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-color-info]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-color-info'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Border Color in Widget Elements', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '16.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-border-color-bold]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-border-color-bold'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-border-color-bold]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-border-color-bold'])); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php _e('Selected Controls in Widget Editor - Background Color', 'ele-ui-color-scheme-restoration'); ?>
                            <a href="<?php echo esc_attr('/wp-content/plugins/ele-ui-color-scheme-restoration/core/includes/assets/images/') . '17.png'; ?>"
                                class="eleui-image">
                                Show me
                            </a>
                        </td>
                        <td>
                            <input type="color" name="dark_mode[--e-a-bg-active-bold]"
                                value="<?php echo esc_attr(strval($color_options['dark_mode_colors']['--e-a-bg-active-bold'])); ?>">
                        </td>
                        <td>
                            <input type="color" name="light_mode[--e-a-bg-active-bold]"
                                value="<?php echo esc_attr(strval($color_options['light_mode_colors']['--e-a-bg-active-bold'])); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="save_colors" class="button-primary"
                    value="<?php _e('Save Colors', 'ele-ui-color-scheme-restoration'); ?>">
                <input type="submit" name="reset_colors" class="button-secondary"
                    value="<?php _e('Reset Colors', 'ele-ui-color-scheme-restoration'); ?>">
            </p>
        </form>
    </div>
    <?php
}


/**
 * Register the settings
 */
function ele_ui_color_scheme_register_settings()
{
    register_setting(
        'ele-ui-color-scheme-settings',
        'ele-ui-color-scheme-restoration-settings',
        'ele_ui_color_scheme_sanitize_settings'
    );

    add_settings_section(
        'ele-ui-color-scheme-section',
        'Color Scheme Settings',
        'ele_ui_color_scheme_render_section',
        'ele-ui-color-scheme-settings'
    );

    add_settings_field(
        'color-scheme-option',
        'Color Scheme Option',
        'ele_ui_color_scheme_render_color_scheme_option',
        'ele-ui-color-scheme-settings',
        'ele-ui-color-scheme-section'
    );
}
add_action('admin_init', 'ele_ui_color_scheme_register_settings');

/**
 * Sanitize settings
 */
function ele_ui_color_scheme_sanitize_settings($settings)
{
    // Perform any necessary sanitization/validation
    return $settings;
}

/**
 * Render the settings section
 */
function ele_ui_color_scheme_render_section()
{
    // Output section description if needed
}

/**
 * Render the color scheme option field
 */
function ele_ui_color_scheme_render_color_scheme_option()
{
    // Output the HTML for your color scheme option input field
}



/* Editor in Dark Mode*/
$all_colors = get_option('ele-ui-color-scheme-restoration-settings');
//$all_colors['dark_mode_colors'] = get_option('dark_mode_colors');
//echo '<pre>';

//var_dump($all_colors);
//echo '</pre>';
//wp_die();
add_action('elementor/editor/wp_head', function () {
    $all_colors = get_option('ele-ui-color-scheme-restoration-settings');
    //ELEUICOLOR()->ele_ui_color_scheme_logger($all_colors['dark_mode_colors']);
    $style = '<style>
    .elementor-panel
      #elementor-panel-saver-button-publish:not(.elementor-disabled) {
	border-color: currentColor;
	}
	@media (prefers-color-scheme: dark) {
	:root {
	color-scheme: dark;

/* start editing here*/

/* Editor Background */
--e-a-bg-default: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-bg-default']) . ';
				
				
/* Header and Footer Areas*/
--e-a-dark-bg: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-dark-bg']) . ';
--e-a-dark-color-txt: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-dark-color-txt']) . ';
--e-a-dark-color-txt-hover: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-dark-color-txt-hover']) . ';
				
				
/* Update Button and toggles */
--e-a-btn-bg-primary: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-btn-bg-primary']) . ';
--e-a-btn-bg-primary-hover: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-btn-bg-primary-hover']) . ';
--e-a-btn-bg-primary-active: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-btn-bg-primary-active']) . ';
				
				
/* Widget Colors On Hover - Note widget colors same as background colors by default */
--e-a-bg-hover: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-bg-hover']) . ';
--e-a-bg-active: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-bg-active']) . ';
				
				
/* Widget Border Color */
--e-a-border-color: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-border-color']) . ';
				
				
/* Search In Focus */
--e-a-border-color-focus: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-border-color-focus']) . ';
				
				
/* Text Selector Background Color */
--e-a-bg-primary: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-bg-primary']) . ';
				
				
/* text color */
--e-a-color-txt: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-color-txt']) . ';
				 
				 
/* Link Hover and Global Style Active Icon Color */
--e-a-color-primary-bold: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-color-primary-bold']) . ';
				
				
/* Info Link Color*/
--e-a-color-info: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-color-info']) . ';
				 
				 
/* Border Color in Widget Elements*/
--e-a-border-color-bold: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-border-color-bold']) . ';
				
				
/* Selected Controls in Widget Editor - background color */
--e-a-bg-active-bold: ' . sanitize_hex_color($all_colors['dark_mode_colors']['--e-a-bg-active-bold']) . '; 			
				
      }
    }
  </style>';

    echo $style;
}, 100);

/* Editor in Light Mode*/

add_action('elementor/editor/wp_head', function () {
    $all_colors = get_option('ele-ui-color-scheme-restoration-settings');
    //echo '<pre>' . var_dump($all_colors) . '<pre>';
    //wp_die();
    echo '<style>
      .elementor-panel
        #elementor-panel-saver-button-publish:not(.elementor-disabled) {
      border-color: currentColor;
      }
      @media (prefers-color-scheme: light) {
      :root {
      color-scheme: light;
  
      /* start editing here*/

      /* Editor Background */
      --e-a-bg-default: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-bg-default']) . ';
                      
                      
      /* Header and Footer Areas*/
      --e-a-dark-bg: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-dark-bg']) . ';
      --e-a-dark-color-txt: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-dark-color-txt']) . ';
      --e-a-dark-color-txt-hover: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-dark-color-txt-hover']) . ';
                      
                      
      /* Update Button and toggles */
      --e-a-btn-bg-primary: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-btn-bg-primary']) . ';
      --e-a-btn-bg-primary-hover: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-btn-bg-primary-hover']) . ';
      --e-a-btn-bg-primary-active: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-btn-bg-primary-active']) . ';
                      
                      
      /* Widget Colors On Hover - Note widget colors same as background colors by default */
      --e-a-bg-hover: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-bg-hover']) . ';
      --e-a-bg-active: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-bg-active']) . ';
                      
                      
      /* Widget Border Color */
      --e-a-border-color: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-border-color']) . ';
                      
                      
      /* Search In Focus */
      --e-a-border-color-focus: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-border-color-focus']) . ';
                      
                      
      /* Text Selector Background Color */
      --e-a-bg-primary: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-bg-primary']) . ';
                      
                      
      /* text color */
      --e-a-color-txt: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-color-txt']) . ';
                       
                       
      /* Link Hover and Global Style Active Icon Color */
      --e-a-color-primary-bold: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-color-primary-bold']) . ';
                      
                      
      /* Info Link Color*/
      --e-a-color-info: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-color-info']) . ';
                       
                       
      /* Border Color in Widget Elements*/
      --e-a-border-color-bold: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-border-color-bold']) . ';
                      
                      
      /* Selected Controls in Widget Editor - background color */
      --e-a-bg-active-bold: ' . sanitize_hex_color($all_colors['light_mode_colors']['--e-a-bg-active-bold']) . '; 			
                      
            }
          }

         }
        </style>';
}, 100);

add_action('elementor/preview/enqueue_styles', function () {
    $all_colors = get_option('ele-ui-color-scheme-restoration-settings');
    echo '<style>
:root {
    /* Edit Here */ 
    
    /* Primary Color */
    --brand-color: ' . sanitize_hex_color($all_colors['brand_colors']['--brand-color']) . '; 			
    
    /* Hover Color */
    --brand-color-hover: ' . sanitize_hex_color($all_colors['brand_colors']['--brand-color-hover']) . ';
    
    /* Active Border Color */
    --brand-color-active:  ' . sanitize_hex_color($all_colors['brand_colors']['--brand-color-active']) . ';
                
			
/* do not edit below */

--e-p-draggable-color: var(--brand-color) !important;
--e-p-border-column: var(--brand-color) !important;
--e-p-border-column-hover: var(--brand-color-hover) !important;
--e-p-border-column-invert: #0c0d0e !important;
--e-p-border-section: var(--brand-color) !important;
--e-p-border-section-hover: var(--brand-color-hover) !important;
--e-p-border-section-invert: #0c0d0e !important;
--e-p-border-con: var(--brand-color) !important;
--e-p-border-con-hover: var(--brand-color-hover) !important;
--e-p-border-con-active: var(--brand-color-active) !important;
--e-p-border-con-invert: #0c0d0e !important;
--e-p-border-widget: var(--brand-color) !important;
--e-p-border-widget-hover: var(--brand-color-hover) !important;
--e-p-border-widget-active: var(--brand-color-active) !important;
--e-p-border-widget-invert: #0c0d0e !important;
--e-a-btn-bg-primary: var(--brand-color) !important;
--e-a-btn-bg-primary-hover: var(--brand-color-hover) !important;
--e-a-btn-bg-primary-active: var(--brand-color-active) !important;
}
    </style>';
}, 999999999999999999999);
ELEUICOLOR();