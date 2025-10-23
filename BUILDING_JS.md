# Building JavaScript Files for Moodle

This document explains how to properly build and minify JavaScript files for the IssueBadge Moodle plugin.

## Overview

Moodle uses **AMD (Asynchronous Module Definition)** for JavaScript modules and requires all JavaScript files to be:
- Placed in `amd/src/` (source files)
- Built and minified into `amd/build/` (production files)

## Prerequisites

Before building JavaScript files, ensure you have:

1. **Node.js and npm** installed (v16+ recommended)
   ```bash
   node --version
   npm --version
   ```

2. **A full Moodle installation** (not just the plugin)
   - You must build JS from the Moodle root directory, not from the plugin directory

## Building JavaScript Files

### Method 1: Using Grunt (Recommended)

Moodle uses Grunt to build JavaScript files. Follow these steps:

#### Step 1: Install Moodle's Development Dependencies

Navigate to your **Moodle root directory** (not the plugin directory):

```bash
cd /path/to/moodle
```

Install npm dependencies:

```bash
npm install
```

This installs Grunt and all required tools defined in Moodle's `package.json`.

#### Step 2: Build JavaScript for the Plugin

From the Moodle root directory, run:

```bash
# Build JS for the entire Moodle installation
npx grunt amd

# Or build JS for a specific plugin
npx grunt amd --root=local/issuebadge
```

This will:
- Read all files from `local/issuebadge/amd/src/*.js`
- Minify them
- Output to `local/issuebadge/amd/build/*.min.js`

#### Step 3: Watch for Changes (Development)

During development, you can watch for changes and auto-rebuild:

```bash
npx grunt watch
```

This will automatically rebuild JavaScript files whenever you save changes to files in `amd/src/`.

### Method 2: Manual Build (For This Plugin)

If you don't have a full Moodle installation, the minified file has been pre-built for you:

- **Source**: `amd/src/issue.js`
- **Built**: `amd/build/issue.min.js`

## File Structure

```
local/issuebadge/
├── amd/
│   ├── src/
│   │   └── issue.js          # Source file (edit this)
│   └── build/
│       └── issue.min.js      # Minified file (auto-generated)
```

## Important Notes

### DO NOT Edit Build Files Directly

**Never manually edit files in `amd/build/`**. They are automatically generated from `amd/src/` files.

If you need to make changes:
1. Edit the file in `amd/src/`
2. Run `grunt amd` to rebuild
3. Commit both `src` and `build` files to version control

### Moodle's JavaScript Linting

Moodle also checks JavaScript code quality. To run linting:

```bash
# From Moodle root
npx grunt eslint:amd
```

To automatically fix issues:

```bash
npx grunt eslint:amd --fix
```

## Common Commands Reference

All commands should be run from the **Moodle root directory**:

| Command | Description |
|---------|-------------|
| `npm install` | Install Grunt and dependencies |
| `npx grunt amd` | Build all AMD JavaScript files |
| `npx grunt amd --root=local/issuebadge` | Build JS for this plugin only |
| `npx grunt watch` | Auto-rebuild on file changes |
| `npx grunt eslint:amd` | Check JavaScript code quality |
| `npx grunt ignorefiles` | Update .eslintignore |

## Troubleshooting

### Error: "Cannot find module 'grunt'"

**Solution**: Run `npm install` from the Moodle root directory.

### Error: "No Gruntfile found"

**Solution**: You're not in the Moodle root directory. Navigate to the Moodle installation root (where `config.php` and `Gruntfile.js` are located).

### JavaScript not loading in Moodle

**Possible causes**:
1. **Cache issue**: Purge all caches in Moodle
   - Site administration → Development → Purge all caches
2. **Missing build file**: Ensure `amd/build/issue.min.js` exists
3. **File permissions**: Ensure web server can read the build file

### Changes not appearing

1. Rebuild the JavaScript:
   ```bash
   npx grunt amd --root=local/issuebadge
   ```

2. Clear Moodle caches:
   - Site administration → Development → Purge all caches
   - Or use CLI: `php admin/cli/purge_caches.php`

3. Hard refresh your browser (Ctrl+F5 or Cmd+Shift+R)

## Pre-built Files

For your convenience, this plugin ships with pre-built JavaScript files in `amd/build/`.

However, if you make any changes to `amd/src/issue.js`, you **must** rebuild using Grunt.

## Moodle Plugin Submission

When submitting to the Moodle Plugins Directory:

- ✅ **Include both** `amd/src/` and `amd/build/` directories
- ✅ Ensure build files are up-to-date with source files
- ✅ Run ESLint to check code quality
- ✅ Test the plugin with JavaScript debugging enabled

## Additional Resources

- [Moodle JavaScript Modules](https://moodledev.io/docs/guides/javascript/modules)
- [Moodle Grunt Documentation](https://moodledev.io/general/development/tools/nodejs)
- [AMD Module Format](https://github.com/amdjs/amdjs-api/blob/master/AMD.md)

## For Plugin Developers

If you're developing this plugin:

1. **Edit**: Make changes to `amd/src/issue.js`
2. **Build**: Run `npx grunt amd` from Moodle root
3. **Test**: Clear Moodle caches and test in browser
4. **Commit**: Commit both src and build files

---

**Note**: The JavaScript file has already been built and minified for this release. You only need to rebuild if you modify the source files.
