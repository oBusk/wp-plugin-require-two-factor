<?php

/**
 * Plugin Name: Require Two-Factor
 * Plugin URI: https://github.com/oBusk/wp-plugin-require-two-factor
 * Description: Falls back to email-based two-factor for any user who has not configured a provider
 * Version: 1.1.0
 * Author: Oscar Busk
 * Author URI: https://github.com/oBusk
 * License: MIT License
 *
 * @see https://wordpress.org/support/topic/can-i-by-default-turn-on-this-feature-for-all-my-existing-and-for-new-user/
 */

// Ensure that email as a two-factor provider is always selectable
add_filter('option_two_factor_enabled_providers', function ($providers) {
    $providers = is_array($providers) ? $providers : [];

    if (! in_array('Two_Factor_Email', $providers, true)) {
        $providers[] = 'Two_Factor_Email';
    }

    return $providers;
});

add_filter('two_factor_enabled_providers_for_user', function ($providers, $user_id) {
    $user = get_userdata($user_id);
    $registered = Two_Factor_Core::get_providers();

    foreach ($providers as $provider) {
        // Check if any two factor provider is "available"
        // Available means it is both enabled and configured. E.g. totp can be enabled but not configured.
        if ($registered[$provider]->is_available_for_user($user)) {
            // As long as any provider is available, return as is.
            return $providers;
        }
    }

    // No provider was available, so we add email
    $providers[] = 'Two_Factor_Email';

    return $providers;
}, 10, 2);
