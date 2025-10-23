# Changelog - Version 1.0.2

## Summary

This release fixes critical bugs and improves JavaScript compatibility with modern Moodle standards.

## Changes

### 🐛 Bug Fixes

#### 1. **Fixed: moodle_url Objects as Array Keys (index.php)**
- **Issue**: PHP doesn't support objects as array keys
- **Location**: `index.php:47-56`
- **Fix**: Changed from using `moodle_url` objects as keys to using associative arrays with 'url' and 'text' properties
- **Impact**: Critical - Plugin management page was broken

#### 2. **Fixed: Incorrect API Request Method (issuebadge_api.php)**
- **Issue**: Always used POST even for GET requests
- **Location**: `classes/api/issuebadge_api.php:62-79`
- **Fix**: Properly handle GET and POST methods separately using `$curl->get()` and `$curl->post()`
- **Impact**: High - API calls were not working correctly

#### 3. **Fixed: WordPress Function Reference (issuebadge_api.php)**
- **Issue**: Used `wp_generate_uuid4()` which is a WordPress function, not Moodle
- **Location**: `classes/api/issuebadge_api.php:163`
- **Fix**: Removed WordPress function check, use only PHP's UUID generation
- **Impact**: Medium - Could cause errors in some environments

#### 4. **Fixed: Missing Default Value in External Function (get_badges.php)**
- **Issue**: The `badges` array didn't have a default value
- **Location**: `classes/external/get_badges.php:91-96`
- **Fix**: Added `VALUE_DEFAULT` and `[]` as default parameters
- **Impact**: Low - Could cause issues when no badges are returned

#### 5. **Fixed: Incorrect Return Type Documentation (lib.php)**
- **Issue**: Function documented as returning `string|null` but actually returns `string|false`
- **Location**: `lib.php:78`
- **Fix**: Updated return type documentation and added proper fallback handling
- **Impact**: Low - Documentation accuracy

### ⚡ Performance Improvements

#### 6. **Added: Pagination for Issued Badges View (view.php)**
- **Issue**: All badges loaded at once, impacting performance with large datasets
- **Location**: `view.php`
- **Features Added**:
  - Pagination with 25 records per page (configurable)
  - Count query for total records
  - Moodle standard paging bar
  - Page and per-page URL parameters
- **Impact**: High - Significant performance improvement for sites with many badges

### 🔧 JavaScript Improvements

#### 7. **Replaced: Legacy M.util with Modern core/str Module**
- **Issue**: Using deprecated `M.util.get_string()` which causes JavaScript errors in modern Moodle
- **Location**: `amd/src/issue.js`
- **Changes**:
  - Added `core/str` to AMD dependencies
  - Replaced all `M.util.get_string()` calls with `Str.get_string().done()`
  - Updated string loading to use Promises
- **Impact**: Critical - JavaScript now compatible with Moodle 4.x standards
- **Reference**: [Moodle String API Documentation](https://moodledev.io/docs/4.4/guides/javascript/modules)

#### 8. **Updated: Minified JavaScript**
- Rebuilt `amd/build/issue.min.js` with updated code
- File size: 6,436 bytes (source) → 3,835 bytes (minified)
- Reduction: 40.4%

### 📚 Documentation

#### 9. **Added: JavaScript Build Documentation**
- Created `BUILDING_JS.md` with comprehensive Grunt build instructions
- Updated `README.md` with JavaScript build section
- Includes troubleshooting and best practices

#### 10. **Updated: Repository Naming Convention**
- Added documentation about Moodle's repository naming standard
- Explained `moodle-local_issuebadge` pattern
- Clarified installation directory structure

## Technical Details

### Files Modified

```
✅ index.php                                    # Fixed array key issue
✅ view.php                                     # Added pagination
✅ lib.php                                      # Fixed return type
✅ classes/api/issuebadge_api.php              # Fixed API methods
✅ classes/external/get_badges.php             # Fixed return structure
✅ classes/external/issue_badge.php            # (No changes)
✅ amd/src/issue.js                            # Replaced M.util with core/str
✅ amd/build/issue.min.js                      # Rebuilt minified version
```

### Files Added

```
📄 BUILDING_JS.md                              # JavaScript build guide
📄 CHANGELOG_v1.0.2.md                         # This file
```

### Files Updated (Documentation)

```
📝 README.md                                    # Added JS build section, naming convention
📝 INSTALL.md                                   # Updated repository naming notes
```

## Upgrade Notes

### From v1.0.1 to v1.0.2

1. **Backup your data** before upgrading
2. Replace all plugin files with the new version
3. Visit **Site administration → Notifications** to complete the upgrade
4. **Clear all caches**: Site administration → Development → Purge all caches
5. Test the following:
   - Badge issuance page loads without errors
   - JavaScript console shows no errors
   - View issued badges page displays with pagination
   - API calls work correctly

### Breaking Changes

**None** - This release is fully backward compatible.

### Database Changes

**None** - No database schema changes in this release.

## Testing Checklist

- [x] PHP syntax validation passed
- [x] JavaScript updated to modern standards
- [x] Pagination implemented and tested
- [x] All API methods work correctly
- [x] Build files generated
- [x] Documentation updated
- [x] Backward compatibility maintained

## Known Issues

None at this time.

## Credits

- Bug reports and suggestions from community testing
- Moodle coding standards compliance
- Modern JavaScript best practices from Moodle documentation

## Support

For issues or questions:
- Email: support@issuebadge.com
- Documentation: [issuebadge.com/docs](https://issuebadge.com/docs)

---

**Release Date**: 2025-01-22
**Version**: 1.0.2
**Moodle Compatibility**: 4.1 - 4.5
**License**: GNU GPL v3 or later
