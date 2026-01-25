# IssueBadge for Moodle

A Moodle plugin that integrates with the IssueBadge API to enable seamless digital certificate and badge issuance directly from your Moodle LMS.

## Description

IssueBadge for Moodle allows educators and administrators to issue professional digital certificates and badges to learners directly from their Moodle courses. The plugin integrates with the IssueBadge platform to provide:

- **Manual Badge Issuance**: Issue badges to individual students from any course
- **Automatic Course Completion Badges**: Automatically award badges when students complete courses
- **Centralized Management**: Manage all badge issuance from a central dashboard
- **Student Badge Viewing**: Students can view all badges they've earned
- **API Integration**: Seamless connection to IssueBadge API for credential generation

## Features

### Core Features
- ✅ **API Integration**: Secure Bearer token authentication with IssueBadge API
- ✅ **Manual Badge Issuance**: Issue badges to enrolled students from course administration
- ✅ **Automatic Issuance**: Trigger badge issuance on course completion events
- ✅ **Badge Management**: Configure which badges to issue for each course
- ✅ **View Issued Badges**: Track all badges issued to students
- ✅ **Multi-Context Support**: Issue badges at system or course level
- ✅ **GDPR Compliant**: Full Privacy API implementation for data protection

### Technical Features
- 🔒 **Security**: Follows Moodle security best practices
- 🌍 **Internationalization**: Full i18n support with language strings
- 📱 **Responsive**: Works on all devices
- ⚡ **AJAX**: Real-time badge loading and issuance without page reloads
- 🎨 **Moodle UI**: Integrated seamlessly with Moodle's admin interface
- 🎭 **Modern Templates**: Uses Mustache templates and Output API (Moodle 4.x standard)

## Requirements

- **Moodle**: 4.1 or higher (tested up to 4.5)
- **PHP**: 7.4 or higher
- **IssueBadge Account**: Active account at [app.issuebadge.com](https://app.issuebadge.com)
- **API Token**: Bearer token from your IssueBadge dashboard

## Installation

### Method 1: Via Moodle Plugin Installer (Recommended)

1. Download the plugin ZIP file
2. Log in to your Moodle site as an admin
3. Navigate to **Site administration → Plugins → Install plugins**
4. Upload the ZIP file
5. Click "Install plugin from the ZIP file"
6. Follow the on-screen instructions

### Method 2: Manual Installation

1. Extract the plugin files
2. Copy the `issuebadge` folder to `/local/` directory in your Moodle installation
3. Visit **Site administration → Notifications** to complete the installation
4. Configure the plugin settings

### Method 3: Git Clone (For Developers)

```bash
cd /path/to/moodle/local
git clone https://github.com/issuebadge/moodle-local_issuebadge.git issuebadge
```

**Note**: The repository follows Moodle's naming convention `moodle-{plugintype}_{pluginname}` (i.e., `moodle-local_issuebadge`), but must be cloned into a folder named `issuebadge` to match Moodle's expected plugin directory structure.

## Configuration

### Initial Setup

1. **Get Your API Token**
   - Log in to [IssueBadge Dashboard](https://app.issuebadge.com)
   - Navigate to API settings
   - Copy your Bearer token

2. **Configure Plugin**
   - Go to **Site administration → Plugins → Local plugins → IssueBadge**
   - Enter your API Bearer Token
   - Enter API Base URL (default: `https://app.issuebadge.com/api/v1`)
   - Enable/disable automatic badge issuance on course completion
   - Save changes

### Configure Course-Specific Badges

To automatically issue badges when students complete a course:

1. Navigate to a course
2. Go to **Course administration → IssueBadge**
3. Select the badge to issue upon completion
4. Enable auto-issuance for the course
5. Save settings

## Usage

### Manual Badge Issuance

#### From Course Context
1. Navigate to your course
2. Go to **Course administration → IssueBadge**
3. Select a badge from the dropdown
4. Select the student recipient
5. Click "Issue Badge"
6. The badge will be issued instantly and a public URL will be generated

#### From System Context
1. Go to **Site administration → Plugins → Local plugins → IssueBadge Management**
2. Click "Issue Badge Manually"
3. Follow the same steps as above

### Automatic Badge Issuance

Once configured, badges will be automatically issued when:
- A student completes a course (based on course completion settings)
- The course has a badge configured for auto-issuance
- Automatic issuance is enabled in plugin settings

**Note**: Each student receives only one badge per course, even if they complete it multiple times.

### View Issued Badges

**As Admin/Teacher:**
1. Go to **IssueBadge Management → View Issued Badges**
2. Filter by user or course
3. View all issued badges with public URLs

**As Student:**
1. Badges appear in your student dashboard
2. View your earned badges and their public URLs

## Capabilities

The plugin defines three capabilities:

| Capability | Description | Default Roles |
|------------|-------------|---------------|
| `local/issuebadge:manage` | Manage plugin settings and configuration | Manager |
| `local/issuebadge:issue` | Issue badges to students | Manager, Editing Teacher |
| `local/issuebadge:view` | View issued badges | All authenticated users |

## Database Schema

### Tables

**`local_issuebadge_issues`**
- Stores issued badge records
- Links to Moodle users and courses
- Tracks Issue ID and public URL from IssueBadge

**`local_issuebadge_course`**
- Course-specific badge configuration
- Maps courses to badge IDs for auto-issuance

## API Endpoints Used

The plugin communicates with IssueBadge API:

- **GET `/badge/getall`**: Retrieve available badge templates
- **POST `/issue/create`**: Issue a badge to a recipient

All requests use Bearer token authentication.

## Privacy (GDPR Compliance)

This plugin is fully compliant with Moodle's Privacy API:

- **Data Stored**: User badge issuance records
- **Data Exported**: Badge IDs, issue IDs, public URLs, timestamps
- **Data Deleted**: User data can be deleted via Moodle's standard privacy tools
- **External Service**: Data (name, email) is sent to IssueBadge API for badge generation

See Privacy Policy: [issuebadge.com/privacy](https://issuebadge.com/privacy)

## Events

### Custom Events

**`\local_issuebadge\event\badge_issued`**
- Triggered when a badge is issued (manually or automatically)
- Can be used by other plugins to react to badge issuance

## Development

### Modern Architecture

This plugin follows Moodle 4.x best practices:

- **Templates**: Mustache templates for all UI components
- **Output API**: Renderable/templatable classes for clean separation
- **Pagination**: Efficient data loading with proper pagination

### Customizing Templates

Themes can override any template by creating:

```
theme/yourtheme/templates/local_issuebadge/{template_name}.mustache
```

Available templates:
- `management_dashboard.mustache` - Management dashboard
- `issue_form.mustache` - Badge issuance form
- `issued_badges_table.mustache` - Issued badges table

### Repository Naming Convention

This plugin follows the Moodle plugin repository naming convention:

- **Repository name**: `moodle-local_issuebadge`
- **Pattern**: `moodle-{plugintype}_{pluginname}`
- **Installation directory**: `/local/issuebadge/`

This naming convention:
- Makes it clear that it's a Moodle plugin
- Identifies the plugin type (`local`) and name (`issuebadge`)
- Provides a consistent experience for Moodle developers
- Follows best practices documented at [Moodle Developer Docs](https://moodledev.io/general/development/policies/codingstyle/frankenstyle#code-repository-name)

### File Structure

```
local/issuebadge/
├── version.php                      # Plugin metadata
├── lib.php                          # Plugin hooks
├── settings.php                     # Admin settings
├── index.php                        # Management dashboard
├── issue.php                        # Badge issuance page
├── view.php                         # View issued badges
├── db/
│   ├── access.php                   # Capabilities
│   ├── events.php                   # Event observers
│   ├── services.php                 # Web services
│   ├── install.xml                  # Database schema
│   └── upgrade.php                  # Upgrade scripts
├── classes/
│   ├── api/issuebadge_api.php      # API client
│   ├── event/badge_issued.php       # Custom event
│   ├── observer.php                 # Event observers
│   ├── privacy/provider.php         # Privacy API
│   └── external/                    # Web service functions
│       ├── get_badges.php
│       └── issue_badge.php
├── lang/en/                         # Language strings
└── README.md
```

### Extending the Plugin

You can extend this plugin by:

1. **Adding More Event Triggers**: Listen to other Moodle events (quiz completion, grade achieved, etc.)
2. **Bulk Operations**: Implement bulk badge issuance for entire cohorts
3. **Additional Badge Metadata**: Store and display more badge information
4. **Email Notifications**: Send emails when badges are issued

## Comparison: WordPress vs Moodle Plugin

This Moodle plugin is inspired by the IssueBadge WordPress plugin but adapted for the Moodle LMS:

| Feature | WordPress Plugin | Moodle Plugin |
|---------|-----------------|---------------|
| **Platform** | WordPress | Moodle LMS |
| **Architecture** | Singleton class | Local plugin |
| **Database** | Custom table + wp_options | XMLDB tables |
| **AJAX** | wp_ajax_* hooks | External web services |
| **Security** | wp_nonce | sesskey + capabilities |
| **Events** | WordPress hooks | Event observers |
| **Auto-issuance** | Not implemented | Course completion |
| **Context** | Site-wide | Multi-context (system/course) |
| **Users** | WordPress users | Enrolled students |

## Troubleshooting

### Badges Not Loading
- Check that your API token is correct in settings
- Verify the API URL is accessible from your server
- Check Moodle error logs for API errors

### Automatic Issuance Not Working
- Ensure automatic issuance is enabled in plugin settings
- Verify course completion criteria are configured
- Check that a badge is configured for the specific course
- Review event observer logs

### Permission Errors
- Verify user has appropriate capability (issuebadge:issue)
- Check course enrollment and role assignments

### Debug Mode
Enable Moodle debugging:
```php
// In config.php
$CFG->debug = E_ALL;
$CFG->debugdisplay = 1;
```

## Support

- **IssueBadge Support**: support@issuebadge.com

## License

This plugin is licensed under the GNU General Public License v3.0 or later.

See [GNU GPL v3](http://www.gnu.org/licenses/gpl-3.0.html)

## Changelog

### Version 2.0.1 (2026-01-25)
- **New**: Badge caching with "Refresh from API" feature
- **New**: Course configuration page for auto-issue triggers
- **New**: Course completion trigger with grade threshold
- **New**: Quiz grade trigger with passing percentage
- **New**: External recipient support (issue to name + email)
- **New**: Improved dashboard with stats and help section
- **New**: Configured courses list in dashboard
- **Improved**: Better UI/UX throughout the plugin

## Roadmap

Future enhancements planned:

- [ ] Bulk badge issuance for student cohorts
- [ ] Email notifications on badge issuance
- [ ] Badge expiry and renewal
- [ ] Integration with Moodle's native badge system
- [ ] Custom badge templates
- [ ] Advanced reporting and analytics
- [ ] Mobile app support

## Contributing

Contributions are welcome! Please follow Moodle coding standards and submit pull requests.

---

**Made with ❤️ by IssueBadge**

For more information, visit [issuebadge.com](https://issuebadge.com)
