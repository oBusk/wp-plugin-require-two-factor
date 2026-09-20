# Require Two-Factor

A WordPress mu-plugin that enforces two-factor authentication for all users by falling back to email-based 2FA.

The [Two-Factor](https://wordpress.org/plugins/two-factor/) plugin (0.9.1) is opt-in per user and has no enforcement setting. This package uses a filter to ensure at least one 2FA provider is available (enabled and configured), and if not, it enables the email provider.

Originally based on [this WordPress.org support thread](https://wordpress.org/support/topic/can-i-by-default-turn-on-this-feature-for-all-my-existing-and-for-new-user/), but has been expanded to cover a bypass path for users with a provider enabled but not usable: TOTP or FIDO U2F enabled without being configured, or backup codes that have all been used.

## Requirements

- [Two-Factor](https://wordpress.org/plugins/two-factor/) plugin installed and active
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
    "wp-plugin/two-factor": "^0.9",
    "obusk/wp-plugin-require-two-factor": "^1.0"
  }
}
```

No configuration needed. Once installed, every user without an available 2FA provider will be prompted for an email code on login.

## Caveats

- **Email deliverability becomes an auth dependency.** Every user without an available provider will receive a one-time code by email on login. If your mail path is broken, those users are locked out. Recovery: remove the Composer requirement and deploy. Renaming the file in `mu-plugins/` works as an emergency stop, but will not survive a rebuild if the site runs from a built image.
- **Email is a weaker second factor than TOTP.** This is a baseline enforcement measure, not a replacement for encouraging users to set up TOTP or FIDO U2F.
- **Application passwords and XML-RPC bypass the 2FA flow entirely.** The Two-Factor plugin does not intercept these authentication paths. If this is a concern, disable them separately.
