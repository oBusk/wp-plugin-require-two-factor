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
    // Tracks the users we are already resolving, since the lookup below re-enters this filter.
    static $resolving = [];

    // The Two-Factor plugin loads after mu-plugins, so it may not be there yet.
    if (! class_exists('Two_Factor_Core') || ! class_exists('Two_Factor_Email')) {
        return $providers;
    }

    // Nothing to add when the fallback is already in place.
    if (in_array('Two_Factor_Email', $providers, true)) {
        return $providers;
    }

    // The re-entrant call resolves availability from the stored providers, so leave them alone.
    if (isset($resolving[$user_id])) {
        return $providers;
    }

    // Available means enabled AND configured: a reset authenticator app stays enabled but unusable.
    $resolving[$user_id] = true;
    $available = Two_Factor_Core::get_available_providers_for_user($user_id);
    unset($resolving[$user_id]);

    // No usable second factor, which is what the login flow checks before challenging.
    if (empty($available)) {
        $providers[] = 'Two_Factor_Email';
    }

    return $providers;
}, 10, 2);
