# Require Two-Factor

A WordPress mu-plugin that enforces two-factor authentication for all users by falling back to email-based 2FA.

The [Two-Factor](https://wordpress.org/plugins/two-factor/) plugin (0.9.1) is opt-in per user and has no enforcement setting. This package hooks into its `two_factor_enabled_providers_for_user` filter and adds `Two_Factor_Email` as a provider for any user who hasn't configured one, making email-based 2FA the default for every account with no enrollment step.

Users who have already configured their own provider (TOTP, WebAuthn, etc.) are unaffected — their provider list is non-empty, so the filter is a no-op.

Based on [this WordPress.org support thread](https://wordpress.org/support/topic/can-i-by-default-turn-on-this-feature-for-all-my-existing-and-for-new-user/).

## Install

Add the GitHub repository as a Composer VCS source, then require the package:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/oBusk/require-two-factor"
    }
  ]
}
```

```sh
composer require obusk/require-two-factor:^1.0
```

## Caveats

- **Email deliverability becomes an auth dependency.** Every user without a configured provider will receive a one-time code by email on login. If your mail path is broken, those users are locked out. Recovery: deactivate the package (remove or rename the file in `mu-plugins/`).
- **Email is a weaker second factor than TOTP.** This is a baseline enforcement measure, not a replacement for encouraging users to set up TOTP or WebAuthn.
- **Application passwords and XML-RPC bypass the 2FA flow entirely.** The Two-Factor plugin does not intercept these authentication paths. If this is a concern, disable them separately.
