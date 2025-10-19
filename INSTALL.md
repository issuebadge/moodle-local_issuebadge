# Installation Guide - IssueBadge for Moodle

## Quick Start Guide

### Prerequisites

Before installing the IssueBadge plugin, ensure you have:

1. **Moodle Installation**
   - Moodle version 4.1 or higher
   - Admin access to your Moodle site
   - PHP 7.4 or higher

2. **IssueBadge Account**
   - Active account at [app.issuebadge.com](https://app.issuebadge.com)
   - API Bearer Token (obtained from IssueBadge dashboard)

## Installation Steps

### Option 1: ZIP Upload (Easiest)

1. **Download the Plugin**
   - Download the `local_issuebadge.zip` file

2. **Upload to Moodle**
   - Log in to Moodle as admin
   - Navigate to: `Site administration → Plugins → Install plugins`
   - Drag and drop the ZIP file or click "Choose a file"
   - Click "Install plugin from the ZIP file"

3. **Complete Installation**
   - Review the plugin validation report
   - Click "Continue"
   - Scroll down and click "Upgrade Moodle database now"
   - Click "Continue" when complete

### Option 2: Manual Installation via FTP/SSH

1. **Extract Plugin Files**
   ```bash
   unzip local_issuebadge.zip
   ```

2. **Upload to Server**
   - Using FTP or SSH, upload the `issuebadge` folder to:
     ```
     /path/to/moodle/local/issuebadge
     ```

3. **Set Permissions** (Linux/Unix)
   ```bash
   cd /path/to/moodle/local/issuebadge
   chmod -R 755 .
   chown -R www-data:www-data .
   ```
   *(Replace `www-data` with your web server user)*

4. **Trigger Installation**
   - Log in to Moodle as admin
   - Navigate to: `Site administration → Notifications`
   - Moodle will detect the new plugin
   - Click "Upgrade Moodle database now"

### Option 3: Git Clone (For Developers)

1. **Navigate to Moodle local directory**
   ```bash
   cd /path/to/moodle/local
   ```

2. **Clone Repository**
   ```bash
   git clone https://github.com/issuebadge/moodle-local_issuebadge.git issuebadge
   ```

3. **Complete Installation**
   - Visit: `Site administration → Notifications`
   - Click "Upgrade Moodle database now"

## Post-Installation Configuration

### Step 1: Configure API Settings

1. Navigate to: `Site administration → Plugins → Local plugins → IssueBadge`

2. **Enter API Configuration:**
   - **API Base URL**: `https://app.issuebadge.com/api/v1`
     *(Leave as default unless you have a custom endpoint)*

   - **API Bearer Token**:
     - Log in to [app.issuebadge.com](https://app.issuebadge.com)
     - Go to Settings → API
     - Copy your Bearer Token
     - Paste it into the Moodle setting

   - **Enable Automatic Issuance**: Check this box if you want badges to be automatically issued when students complete courses

3. Click **Save changes**

### Step 2: Test the Connection

1. Navigate to: `Site administration → Plugins → Local plugins → IssueBadge Management`

2. Click **"Issue Badge Manually"**

3. The badge dropdown should populate with your available badges
   - If you see badges listed, the connection is successful ✅
   - If you see an error, verify your API token and URL

### Step 3: Configure Capabilities (Optional)

By default:
- **Managers** can manage settings and issue badges
- **Editing Teachers** can issue badges in their courses
- **All Users** can view their own badges

To customize:
1. Navigate to: `Site administration → Users → Permissions → Define roles`
2. Edit a role (e.g., "Teacher")
3. Find IssueBadge capabilities:
   - `local/issuebadge:manage`
   - `local/issuebadge:issue`
   - `local/issuebadge:view`
4. Set permissions as needed

### Step 4: Configure Course Badges (Optional)

To enable automatic badge issuance for a specific course:

1. Navigate to a course
2. Go to: `Course administration → IssueBadge` *(or similar, depending on your setup)*
3. Select which badge to issue upon course completion
4. Enable auto-issuance
5. Save settings

## Verification Checklist

After installation, verify the following:

- [ ] Plugin appears in `Site administration → Plugins → Local plugins`
- [ ] Settings page is accessible
- [ ] API token is configured
- [ ] Badge dropdown loads available badges
- [ ] You can manually issue a test badge
- [ ] Badge appears in the "View Issued Badges" page
- [ ] Public URL from IssueBadge is accessible

## Database Tables

The plugin creates two database tables:

1. **`mdl_local_issuebadge_issues`**
   - Stores issued badge records
   - Links badges to users and courses

2. **`mdl_local_issuebadge_course`**
   - Stores course-specific badge configuration
   - Maps courses to badge IDs for auto-issuance

*(Table prefix may vary based on your Moodle configuration)*

## Common Installation Issues

### Issue: "Plugin validation failed"

**Solution:**
- Ensure folder is named exactly `issuebadge` (not `local_issuebadge` or `moodle-local_issuebadge`)
- Verify all files are in the correct directory: `/local/issuebadge/`

### Issue: "Could not create database tables"

**Solution:**
- Check database user has CREATE TABLE permissions
- Verify Moodle's database connection is working
- Review server error logs

### Issue: "Badges not loading"

**Solution:**
- Verify API token is correct
- Check your server can connect to `https://app.issuebadge.com`
- Ensure no firewall is blocking outbound HTTPS connections
- Enable debugging in Moodle to see detailed error messages

### Issue: "Permission denied errors"

**Solution:**
- Check file permissions (should be 755 for directories, 644 for files)
- Ensure web server user owns the files
- On Linux: `chown -R www-data:www-data /path/to/moodle/local/issuebadge`

## Uninstallation

To uninstall the plugin:

1. Navigate to: `Site administration → Plugins → Plugins overview`
2. Find "IssueBadge" under Local plugins
3. Click "Uninstall"
4. Confirm deletion

**Warning**: This will permanently delete all issued badge records from your Moodle database. The actual badges on IssueBadge.com will remain intact.

## Upgrading

To upgrade to a newer version:

1. **Backup your database** (important!)
2. Replace the plugin files with the new version
3. Visit: `Site administration → Notifications`
4. Click "Upgrade Moodle database now"

Your existing configuration and issued badges will be preserved.

## Getting Help

If you encounter issues during installation:

1. **Enable Debugging**
   - `Site administration → Development → Debugging`
   - Set to "DEVELOPER" level temporarily

2. **Check Logs**
   - `Site administration → Reports → Logs`
   - Look for errors related to "local_issuebadge"

3. **Contact Support**
   - Email: support@issuebadge.com
   - Include: Moodle version, PHP version, error messages

## System Requirements Summary

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| Moodle | 4.1 | 4.5+ |
| PHP | 7.4 | 8.1+ |
| Database | MySQL 5.7 / PostgreSQL 10 | MySQL 8.0 / PostgreSQL 13 |
| Disk Space | 5 MB | 10 MB |
| HTTPS | Required for API | Required |

## Next Steps

After successful installation:

1. ✅ Configure your first course badge
2. ✅ Issue a test badge manually
3. ✅ Set up course completion criteria
4. ✅ Test automatic badge issuance
5. ✅ Train teachers on how to use the plugin

---

**Congratulations!** Your IssueBadge plugin is now installed and ready to use.

For detailed usage instructions, see [README.md](README.md)
