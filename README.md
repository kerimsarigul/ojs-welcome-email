# Welcome Email Plugin for OJS 3.3

A plugin for Open Journal Systems (OJS) that sends a professionally designed HTML welcome email to users immediately after self-registration, without requiring email activation.

## Features

- 🎨 Professional HTML email template with responsive design
- 🌍 Multi-language support (English and Turkish included)
- ⚡ Automatic sending upon user registration
- 🔧 No configuration required - works out of the box
- 📱 Mobile-friendly email design

## Compatibility

| OJS Version | PHP Version | Status |
|-------------|-------------|--------|
| 3.3.x       | 7.3 - 8.0   | ✅ Supported |
| 3.4.x       | 8.0 - 8.2   | ❌ Not compatible |
| 3.5.x       | 8.1 - 8.3   | ❌ Not compatible |

> **Note:** This plugin is developed and tested for OJS 3.3.x only. It is not compatible with OJS 3.4 or later versions due to architectural changes in the registration system and locale file structure.

### OJS 3.3 & PHP Compatibility

| OJS Version | Minimum PHP | Maximum PHP |
|-------------|-------------|-------------|
| 3.3.0-0 to 3.3.0-7 | 7.3 | 8.0 |
| 3.3.0-8 to 3.3.0-18 | 7.3 | 8.0 |

## Installation

### Method 1: Upload via Admin Panel (Recommended)

1. Download the latest release (`welcomeEmail.tar.gz`)
2. Login to your OJS as Administrator
3. Navigate to **Settings** → **Website** → **Plugins**
4. Click **Upload A New Plugin**
5. Select the downloaded `welcomeEmail.tar.gz` file
6. Click **Save**
7. Enable the plugin from the plugin list

### Method 2: Manual Installation

1. Download and extract the plugin
2. Copy the `welcomeEmail` folder to `plugins/generic/`
3. Login to OJS as Administrator
4. Navigate to **Settings** → **Website** → **Plugins**
5. Find "Welcome Email" in the Generic Plugins list
6. Click the checkbox to enable it

## Configuration

No configuration is required. The plugin automatically:

- Uses the journal's contact email as the sender
- Uses the journal's name in the email content
- Detects the user's language preference
- Sends the email immediately after successful registration

## Email Template

The plugin sends a professionally designed HTML email with:

- Journal header with branding color
- Personalized greeting with user's name
- Welcome message
- Call-to-action button linking to the journal
- Footer with journal information

### Email Preview

```
┌─────────────────────────────────────────────────┐
│           [Journal Name]                        │  ← Header
├─────────────────────────────────────────────────┤
│                                                 │
│  Dear [User Name],                              │
│                                                 │
│  Thank you for registering at [Journal Name].   │
│                                                 │
│  Your account has been successfully created.    │
│  You can now log in and access all features.    │
│                                                 │
│         [ Visit Our Website ]                   │  ← Button
│                                                 │
│  Best regards,                                  │
│  [Journal Name]                                 │
│                                                 │
├─────────────────────────────────────────────────┤
│  This is an automated message.                  │  ← Footer
│  [Journal URL]                                  │
└─────────────────────────────────────────────────┘
```

## Adding New Languages

OJS 3.3 uses locale codes with region (e.g., `en_US`, `tr_TR`, `de_DE`).

1. Create a new folder in `locale/` with the locale code (e.g., `locale/de_DE/`)
2. Copy `locale/en_US/locale.po` to the new folder
3. Translate the `msgstr` values in the new `locale.po` file
4. The plugin will automatically detect and use the new language

### Supported Locale Codes (OJS 3.3)

| Language | Locale Code | Folder Name |
|----------|-------------|-------------|
| English | en_US | `locale/en_US/` |
| Turkish | tr_TR | `locale/tr_TR/` |
| German | de_DE | `locale/de_DE/` |
| French | fr_FR | `locale/fr_FR/` |
| Spanish | es_ES | `locale/es_ES/` |
| Portuguese | pt_BR | `locale/pt_BR/` |
| Arabic | ar_IQ | `locale/ar_IQ/` |

## File Structure

```
welcomeEmail/
├── WelcomeEmailPlugin.inc.php   # Main plugin class
├── index.php                     # Plugin loader
├── version.xml                   # Version information
├── LICENSE                       # GPL v3 License
├── README.md                     # Documentation
└── locale/
    ├── en_US/
    │   └── locale.po            # English translations
    └── tr_TR/
        └── locale.po            # Turkish translations
```

## Troubleshooting

### Email not sending

1. Check that your OJS email settings are configured correctly in `config.inc.php`
2. Verify SMTP settings if using SMTP
3. Check the PHP error log for any error messages starting with `WelcomeEmailPlugin:`

### Plugin not appearing

1. Ensure the folder name is exactly `welcomeEmail`
2. Check file permissions (should be readable by web server)
3. Clear OJS template cache: delete contents of `cache/` folder

### Translations not working

1. Ensure locale folder names match OJS locale codes (e.g., `en_US`, not `en`)
2. Check that `locale.po` file exists in the correct folder
3. Verify the locale is enabled in your OJS installation

### Error logs

The plugin logs all activities with the prefix `WelcomeEmailPlugin:`. Check your PHP error log or OJS log files for debugging information.

```
WelcomeEmailPlugin: Email sent successfully to user@example.com
WelcomeEmailPlugin: From email not configured
WelcomeEmailPlugin: Failed to send email to user@example.com
```

## Changelog

### Version 1.0.0 (2026-01-17)

- Initial release
- Professional HTML email template with responsive design
- Multi-language support (English, Turkish)
- Automatic email sending upon user registration
- OJS 3.3.x compatibility

## Support

- **Email:** info@ojs-services.com
- **GitHub:** https://github.com/kerimsarigul
- **Issues:** Please report issues on GitHub

## License

This plugin is licensed under the GNU General Public License v3.0. See [LICENSE](LICENSE) for details.

---

© 2026 OJS Services. All rights reserved.
