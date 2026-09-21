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

// Two-Factor 0.16 lets an admin deselect providers site-wide, which removes them from
// Two_Factor_Core::get_providers(). Email has to stay registered or the fallback below
// resolves to a provider that no longer exists and the user logs in with no second factor.
add_filter('two_factor_providers', function ($providers) {
    if (! isset($providers['Two_Factor_Email'])) {
        $providers['Two_Factor_Email'] = TWO_FACTOR_DIR.'providers/class-two-factor-email.php';
    }

    return $providers;
}, PHP_INT_MAX);

// Two-Factor 0.16 filters this same hook at priority 10 to enforce the site-wide provider
// selection, and intersects away anything not in it. Run after that.
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
}, PHP_INT_MAX, 2);
