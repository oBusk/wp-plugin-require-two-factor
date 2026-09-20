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

/**
 * A provider can be enabled without being configured: resetting the authenticator app
 * deletes the TOTP secret but leaves Two_Factor_Totp in the enabled providers list. The
 * enabled list is therefore not enough to tell whether the user has a usable second
 * factor, so ask for the available (enabled AND configured) providers instead.
 *
 * Resolving those calls back into this filter, hence the re-entrancy guard: during the
 * inner call the stored providers are returned untouched, so availability is judged on
 * what the user actually configured.
 */
add_filter('two_factor_enabled_providers_for_user', function ($providers, $user_id) {
    static $resolving = [];

    if (! class_exists('Two_Factor_Core') || ! class_exists('Two_Factor_Email')) {
        return $providers;
    }

    if (in_array('Two_Factor_Email', $providers, true) || isset($resolving[$user_id])) {
        return $providers;
    }

    $resolving[$user_id] = true;
    $available = Two_Factor_Core::get_available_providers_for_user($user_id);
    unset($resolving[$user_id]);

    if (empty($available)) {
        $providers[] = 'Two_Factor_Email';
    }

    return $providers;
}, 10, 2);
