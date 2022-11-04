<?php
/*
Plugin Name: Extensible Social Profiles Widget
Plugin URI: 
Description: Adds the ability to add social profiles to a site and output them as a widget.
Version: 1.0
License: GPL-2.0+
Author: Claudio Alcantara
Author URI: 
Text domain: extensible-social-profile
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Protection por directly access
if (!defined('ABSPATH'))
{
    exit;
}

// Define variables for path to this plugin
define('ESP_LOCATION', dirname(__FILE__));
define('ESP_LOCATION_URL', plugins_url( '', __FILE__ ));

/**
 * Get the registered social profiles.
 *
 * @return array An array of registered social profiles.
 */

function esp_get_social_profiles()
{
    return apply_filters('esp_social_profiles', array());
}

/**
 * Registers the default social profiles.
 *
 * @param  array $profiles An array of the current registered social profiles.
 * @return array The modified array of social profiles.
 */

add_filter('esp_social_profiles', 'esp_register_default_social_profiles', 10, 1);

function esp_register_default_social_profiles($profiles)
{
    // Facebook profile
    $profiles['facebook'] = array(
        'id'                => 'esp_facebook_url',
        'label'             => __('Facebook URL', 'extensible-social-profile'),
        'class'             => 'facebook',
        'description'       => __('Enter your Facebook profile URL', 'extensible-social-profile'),
        'priority'          => 10,
        'type'              => 'text',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    );

    // Linkedin profile
    $profiles['linkedin'] = array(
        'id'                => 'esp_linkedin_url',
        'label'             => __('Linkedin URL', 'extensible-social-profile'),
        'class'             => 'linkedin',
        'description'       => __('Enter your Linkedin profile URL', 'extensible-social-profile'),
        'priority'          => 20,
        'type'              => 'text',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    );

    // Twitter profile
    $profiles['twitter'] = array(
        'id'                => 'esp_twitter_url',
        'label'             => __('Twitter URL', 'extensible-social-profile'),
        'class'             => 'twitter',
        'description'       => __('Enter your Twitter profile URL', 'extensible-social-profile'),
        'priority'          => 40,
        'type'              => 'text',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    );

    return $profiles;
}

/**
 * Registers the social profiles with the customizer in WordPress.
 *
 * @param  WP_Customizer $wp_customize The customizer object.
 */

add_action('customize_register', 'esp_register_social_customizer_settings');

function esp_register_social_customizer_settings($wp_customize)
{
    // Get the social profiles
    $social_profiles = esp_get_social_profiles();

    // If we have any social profile
    if (!empty($social_profiles))
    {
        // Register the customize section for social profiles
        $wp_customize->add_section(
            'esp_social',
            array(
                'title'             => __('Social Profiles'),
                'description'       => __('Add social media profiles here'),
                'priority'          => 160,
                'capability'        => 'edit_theme_options',
            )
        );
    
    // Loop through each profile
	foreach ($social_profiles as $social_profile)
    {
        // Add the customize settings for this profile
        $wp_customize->add_setting(
            $social_profile['id'],
            array(
                'default'           => '',
                'sanitize_callback' => $social_profile['sanitize_callback'],
            )
        );

        //Add the customize control for this profile
        $wp_customize->add_control(
            $social_profile['id'],
            array(
                'type'              => $social_profile['type'],
                'priority'          => $social_profile['priority'],
                'section'           => 'esp_social',
                'label'             => $social_profile['label'],
                'description'       => $social_profile['description'],
                )
            );
        }
    }
}

/**
 * Register the social icons widget with WordPress.
 */

add_action( 'widgets_init', 'esp_register_social_icons_widget');

function esp_register_social_icons_widget()
{
    register_widget('ESP_Social_Icons_Widget');
}

/**
 * Extend the widgets class for our new social icons widget.
 */

Class ESP_Social_Icons_Widget extends WP_Widget
{
    /**
	 * Setup the widget.
	 */
    public function __construct()
    {
        // Widget settings
        $widget_ops = array(
            'classname'             => 'esp-social-icons',
            'description'           => __('Output your sites social icons, based on the social profiles added to the cutomizer.', 'extensible-social-profile'),
        );

        //Widget control settings
        $control_ops = array(
            'id_base'               => 'esp_social_icons'
        );

        // Create the widget
        parent::__construct('esp_social_icons', 'Social Icons', $widget_ops, $control_ops);
    }

    /**
	 * Output the widget front-end.
	 */

    public function widget($args, $instance)
    {
        // Output the before widget content
        echo wp_kses_post($args['before_widget']);
        
        /**
		 * Call an action which outputs the widget.
		 *
		 * @param $args is an array of the widget arguments e.g. before_widget.
		 * @param $instance is an array of the widget instances.
		 *
		 * @hooked esp_social_icons_output_widget_title.- 10
		 * @hooked esp_output_social_icons_widget_content - 20
		 */

         do_action('esp_social_icons_widget_output', $args, $instance);

        // Output the after widget content
        echo wp_kses_post($args['after_widget']);
    }

    /**
	 * Output the backend widget form.
	 */

    public function form($instance)
    {
        //Get the save title
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'extensible-social-profile');?>
            </label>
            <input 
                class="widefat"
                id="<?php echo esc_attr($this->get_field_id('title'));?>"
                name="<?php esc_attr( $this->get_field_name('title'));?>"
                type="text"
                value="<?php echo esc_attr($title);?>"
            >
        </p>
        <p>
            <?php 
                printf(
                    __('To add social profiles, please use the social profile section in the %1$scustomizer%2$s.', 'extensible-social-profile'),
                    '<a href="' . admin_url('customize.php') . '">', '</a>'
                );
            ?>
        </p>
        <?php
    }

    /**
	 * Controls the save function when the widget updates.
	 *
	 * @param  array $new_instance The newly saved widget instance.
	 * @param  array $old_instance The old widget instance.
	 * @return array The new instance to update.
	 */

    public function update($new_instance, $old_instance)
    {
        // Create an empty array to store the new value in.
        $instance = array();

        // Add the title to the array, stripping empty tags along the way
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        // Return the instance array to be saved.
		return $instance;
    }
}

/**
 * Outputs the widget title for the social icons widget.
 *
 * @param  array $args An array of widget args.
 * @param  array $instance The current instance of widget data.
 */

add_action('esp_social_icons_widget_output', 'esp_social_icons_output_widget_title', 10, 2);

function esp_social_icons_output_widget_title($args, $instance)
{
    // If we before widget content
    if(!empty($instance['title']))
    {
        // If we have before title content
        if (!empty($args['before_title']))
        {
            // Output the before title content
            echo wp_kses_post($args['before_title']);
        }

        // Output the before widget content
        echo esc_html($instance['title']);

        // If we have after title content
        if (!empty($args['after_title']))
        {
            // Output the after title content
            echo wp_kses_post($args['after_title']);
        }
    }
}

/**
 * Outputs the widget content for the social icons widget - the actual icons and links
 *
 * @param  array $args An array of widget args.
 * @param  array $instance The current instance of widget data.
 */

add_action('esp_social_icons_widget_output', 'esp_output_social_icons_widget_content', 20, 2);

function esp_output_social_icons_widget_content($args, $instance)
{
    // Get the array of social profiles
    $social_profiles = esp_get_social_profiles();

    // If we have any social profiles
    if (!empty($social_profiles))
    {
        // Start the output markup
        ?>
        <ul class="esp-social-icons">
        <?php
        
        // Loop through each profile
        foreach ($social_profiles as $social_profile)
        {
            // Get the value fot this social profile - the profile url
            $profile_url = get_theme_mod($social_profile['id']);

            // If we have a no value - url
            if (empty($profile_url))
            {
                // Continue to the next social profile
                continue;
            }

            // if we don't have a specified class
            if (empty($social_profile['class']))
            {
                // Use the label for form a class
                $social_profile['class'] = strtolower(sanitize_title_with_dashes($social_profile['label']));
            }

            // Build the markup for this social profile
            ?>

            <li class="esp-social-icons__item esp-social-icons__item--">
                <a target="_blank" 
                    class="esp-social-icons__item-link"
                    href="<?php echo esc_url($profile_url);?>">
                    <?php echo esc_attr($social_profile['class']);?>
                </a>
                <i class="icon-<?php echo esc_attr($social_profile['class']);?>"></i>
            </li>
            <?php
        }
        ?>
        </ul>
        <?php 
    }
}