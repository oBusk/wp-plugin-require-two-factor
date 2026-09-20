<?php

/**
 * Plugin Name: Require Two-Factor
 * Plugin URI: https://github.com/oBusk/wp-plugin-require-two-factor
 * Description: Falls back to email-based two-factor for any user who has not configured a provider
 * Version: 1.0.0
 * Author: Oscar Busk
 * Author URI: https://github.com/oBusk
 * License: MIT License
 *
 * @see https://wordpress.org/support/topic/can-i-by-default-turn-on-this-feature-for-all-my-existing-and-for-new-user/
 */

add_filter('two_factor_enabled_providers_for_user', function ($providers) {
    if (empty($providers) && class_exists('Two_Factor_Email')) {
        $providers[] = 'Two_Factor_Email';
    }

    return $providers;
});
