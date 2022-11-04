<?php
/**
 * Edit the social profiles available.
 *
 * @param  array $profiles The current array of registered social profiles.
 * @return array           The modified array of registered social profiles.
 */

add_filter('esp_social_profiles', 'test_edit_social_profile', 20, 1);

function test_edit_social_profile($profiles)
{
    // if we have a linkedin profile.
    if (!empty($profiles['linkedin']))
    {
        // remove it.
        unset($profiles['linkedin'] );
    }

    // add the Pinterest profile.
	$profiles['pinterest'] = array(
		'id'                => 'esp_pinterest_url',
		'label'             => __('Pinterest URL', 'extensible-social-profile'),
		'class'             => 'pinterest',
		'description'       => __('Enter your Pinterest profile URL', 'extensible-social-profile'),
		'priority'          => 50,
		'type'              => 'text',
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	);

    // return profiles;
	return $profiles;
}

/**
 * Add a custom title to the social profiles widget.
 *
 * @param  array $args     The widget args.
 * @param  array $instance The widget instance.
 */
add_action( 'esp_social_icons_widget_output', 'test_add_custom_title', 10, 2);

function test_add_custom_title($args, $instance)
{
    echo '<h2>My custom title ' . esc_html( $instance['title'] ) . '</h2>';
}