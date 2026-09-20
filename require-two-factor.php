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

add_filter('two_factor_enabled_providers_for_user', function ($providers, $user_id) {
    $user = get_userdata($user_id);
    $registered = Two_Factor_Core::get_providers();

    foreach ($providers as $provider) {
        // Enabled is not enough: a reset authenticator app stays enabled but is no longer usable.
        if ($registered[$provider]->is_available_for_user($user)) {
            return $providers;
        }
    }

    // Nothing usable, which is the same condition the login flow checks before challenging.
    $providers[] = 'Two_Factor_Email';

    return $providers;
}, 10, 2);
