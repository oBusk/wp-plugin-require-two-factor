# Require Two-Factor

A WordPress mu-plugin that enforces two-factor authentication for all users by falling back to email-based 2FA.

The [Two-Factor](https://wordpress.org/plugins/two-factor/) plugin is opt-in per user and has no enforcement setting. This package uses a filter to ensure at least one 2FA provider is available (enabled and configured), and if not, it enables the email provider.

Originally based on [this WordPress.org support thread](https://wordpress.org/support/topic/can-i-by-default-turn-on-this-feature-for-all-my-existing-and-for-new-user/), but has been expanded to cover a bypass path for users with a provider enabled but not usable: a provider enabled without being configured (possible in user meta written by older Two-Factor versions), or backup codes that have all been used.

Two-Factor 0.16 added a site-wide provider selection under Settings → Two-Factor. This package keeps the email provider registered regardless of that setting, so **installing it means email 2FA is always enabled site-wide** and every user can pick it in their profile. That is the deliberate trade-off: TOTP cannot be forced on a user who never sets it up, so email is the floor everyone falls back to.

## Requirements

- [Two-Factor](https://wordpress.org/plugins/two-factor/) plugin 0.16 or newer, installed and active
- PHP 8.4 or newer
- [Bedrock](https://roots.io/bedrock/) or another Composer-managed WordPress setup with [`composer/installers`](https://github.com/composer/installers) (handles the `wordpress-muplugin` install type)
- Bedrock's [mu-plugin autoloader](https://github.com/roots/bedrock-autoloader) or equivalent (WordPress only auto-loads `.php` files directly in `mu-plugins/`, not subdirectories)

## Install

Add the VCS repository to your `composer.json` and require both the Two-Factor plugin and this package:

```json
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://repo.wp-packages.org"
    },
    {
      "type": "vcs",
      "url": "https://github.com/oBusk/wp-plugin-require-two-factor"
    }
  ],
  "require": {
    "wp-plugin/two-factor": "^0.16",
    "obusk/wp-plugin-require-two-factor": "^1.1"
  }
}
```

No configuration needed. Once installed, every user without an available 2FA provider will be prompted for an email code on login.

## Caveats

- **Email deliverability becomes an auth dependency.** Every user without an available provider will receive a one-time code by email on login. If your mail path is broken, those users are locked out. Recovery: remove the Composer requirement and deploy. Renaming the file in `mu-plugins/` works as an emergency stop, but will not survive a rebuild if the site runs from a built image.
- **Email is a weaker second factor than TOTP.** This is a baseline enforcement measure, not a replacement for encouraging users to set up TOTP.
- **Application passwords bypass the 2FA flow.** Since Two-Factor 0.14 application password logins over the REST API and XML-RPC are allowed by default for users with 2FA enabled. Return `false` from `two_factor_user_api_login_enable`, or disable application passwords, if this is a concern. XML-RPC with a regular password is still blocked.
