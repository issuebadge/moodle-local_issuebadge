# Templates and Output API Guide

This document explains the template-based architecture implemented in the IssueBadge plugin, following Moodle's modern best practices.

## Overview

The plugin now uses **Mustache templates** and the **Output API** instead of legacy `html_writer` methods. This provides:

- ✅ Better separation of concerns (logic vs. presentation)
- ✅ Easier maintenance and customization
- ✅ Theme-friendly rendering
- ✅ Compliance with Moodle 4.x standards
- ✅ Reusable components

## Architecture

### Directory Structure

```
local/issuebadge/
├── classes/
│   └── output/
│       ├── renderer.php                    # Main renderer class
│       ├── management_dashboard.php        # Dashboard renderable
│       ├── issue_form.php                  # Issue form renderable
│       └── issued_badges_table.php         # Badges table renderable
└── templates/
    ├── management_dashboard.mustache       # Dashboard template
    ├── issue_form.mustache                 # Issue form template
    └── issued_badges_table.mustache        # Badges table template
```

### Components

#### 1. **Renderer** (`classes/output/renderer.php`)

The main renderer class extends `plugin_renderer_base` and provides methods to render each component:

```php
class renderer extends plugin_renderer_base {
    public function render_management_dashboard(management_dashboard $page);
    public function render_issue_form(issue_form $page);
    public function render_issued_badges_table(issued_badges_table $page);
}
```

#### 2. **Renderables** (`classes/output/*.php`)

Each renderable class implements `renderable`, `templatable`, and provides data to templates:

```php
class management_dashboard implements renderable, templatable {
    public function export_for_template(renderer_base $output);
}
```

#### 3. **Templates** (`templates/*.mustache`)

Mustache templates define the HTML structure with placeholders for dynamic data.

## Usage Examples

### Rendering the Management Dashboard

**In index.php:**

```php
// Old way (removed):
// echo html_writer::start_div('local_issuebadge_dashboard');
// echo html_writer::tag('h3', get_string('managebadges', 'local_issuebadge'));
// ...

// New way:
$dashboard = new \local_issuebadge\output\management_dashboard();
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($dashboard);
```

**Data flow:**

1. Create renderable instance: `new management_dashboard()`
2. Get plugin renderer: `$PAGE->get_renderer('local_issuebadge')`
3. Render: `$renderer->render($dashboard)`
4. Renderer calls `export_for_template()` → passes data to Mustache template
5. Template generates HTML

### Rendering the Issue Form

**In issue.php:**

```php
// Old way (removed):
// echo html_writer::start_tag('form', ['id' => 'issuebadge_form']);
// echo html_writer::tag('label', get_string('badge', 'local_issuebadge'));
// ...

// New way:
$issueform = new \local_issuebadge\output\issue_form($courseid, $context);
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($issueform);
```

### Rendering the Badges Table

**In view.php:**

```php
// Old way (removed):
// $table = new html_table();
// $table->head = [...];
// echo html_writer::table($table);

// New way:
$badgestable = new \local_issuebadge\output\issued_badges_table($issues, $paginationhtml);
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($badgestable);
```

## Template Syntax

### Basic Variables

```mustache
{{variablename}}          <!-- Escaped HTML -->
{{{variablename}}}        <!-- Unescaped HTML -->
```

### Conditionals

```mustache
{{#hasissues}}
    <!-- Show when hasissues is true -->
{{/hasissues}}

{{^hasissues}}
    <!-- Show when hasissues is false -->
{{/hasissues}}
```

### Loops

```mustache
{{#issues}}
    <tr>
        <td>{{recipientname}}</td>
        <td>{{recipientemail}}</td>
    </tr>
{{/issues}}
```

### Language Strings

```mustache
{{#str}}stringkey, component{{/str}}
{{#str}}badge, local_issuebadge{{/str}}
```

### Example: issued_badges_table.mustache

```mustache
{{#hasissues}}
<table class="table">
    <thead>
        <tr>
            <th>{{#str}}recipientname, local_issuebadge{{/str}}</th>
            <th>{{#str}}recipientemail, local_issuebadge{{/str}}</th>
        </tr>
    </thead>
    <tbody>
        {{#issues}}
        <tr>
            <td>{{recipientname}}</td>
            <td>{{recipientemail}}</td>
        </tr>
        {{/issues}}
    </tbody>
</table>
{{/hasissues}}

{{^hasissues}}
<div class="alert alert-info">
    {{#str}}nobadges, local_issuebadge{{/str}}
</div>
{{/hasissues}}
```

## Customization

### Theme Overrides

Themes can override templates by creating:

```
theme/yourtheme/templates/local_issuebadge/management_dashboard.mustache
```

This allows complete customization without modifying plugin code.

### Adding New Templates

1. **Create Template** (`templates/mycomponent.mustache`):

```mustache
<div class="my-component">
    <h3>{{title}}</h3>
    <p>{{description}}</p>
</div>
```

2. **Create Renderable** (`classes/output/mycomponent.php`):

```php
namespace local_issuebadge\output;

class mycomponent implements renderable, templatable {
    protected $title;
    protected $description;

    public function __construct($title, $description) {
        $this->title = $title;
        $this->description = $description;
    }

    public function export_for_template(renderer_base $output) {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
```

3. **Add Renderer Method** (`classes/output/renderer.php`):

```php
public function render_mycomponent(mycomponent $page) {
    $data = $page->export_for_template($this);
    return $this->render_from_template('local_issuebadge/mycomponent', $data);
}
```

4. **Use in PHP**:

```php
$component = new \local_issuebadge\output\mycomponent('Title', 'Description');
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($component);
```

## Testing Templates

### Purge Caches

After modifying templates, always purge caches:

```bash
php admin/cli/purge_caches.php
```

Or via UI: **Site administration → Development → Purge all caches**

### Template Debugging

Enable template debugging in Moodle:

1. Go to **Site administration → Development → Debugging**
2. Set **Debug messages** to **DEVELOPER**
3. Check **Display debug messages**

This shows which template is being used for each component.

### Mustache CLI

Moodle provides CLI tools for validating templates:

```bash
php admin/cli/check_mustache.php --file=local/issuebadge/templates/management_dashboard.mustache
```

## Benefits

### Before (Legacy HTML)

```php
echo html_writer::start_div('local_issuebadge_dashboard');
echo html_writer::tag('h3', get_string('managebadges', 'local_issuebadge'));
echo html_writer::start_tag('ul');
foreach ($links as $link) {
    echo html_writer::tag('li', html_writer::link($link['url'], $link['text']));
}
echo html_writer::end_tag('ul');
echo html_writer::end_div();
```

**Issues:**
- ❌ HTML mixed with PHP logic
- ❌ Hard to read and maintain
- ❌ Difficult to customize without editing PHP
- ❌ No theme support

### After (Templates)

**PHP (index.php):**
```php
$dashboard = new \local_issuebadge\output\management_dashboard();
echo $PAGE->get_renderer('local_issuebadge')->render($dashboard);
```

**Template (management_dashboard.mustache):**
```mustache
<div class="local_issuebadge_dashboard">
    <h3>{{heading}}</h3>
    <ul>
        {{#links}}
        <li><a href="{{url}}">{{text}}</a></li>
        {{/links}}
    </ul>
</div>
```

**Benefits:**
- ✅ Clean separation of logic and presentation
- ✅ Easy to read and maintain
- ✅ Theme-overrideable
- ✅ Follows Moodle standards

## Resources

- [Moodle Templates Documentation](https://moodledev.io/docs/guides/templates)
- [Moodle Output API](https://moodledev.io/docs/apis/subsystems/output)
- [Mustache Documentation](https://mustache.github.io/mustache.5.html)
- [Moodle Output Renderers](https://moodledev.io/docs/apis/subsystems/output/renderers)

## Migration Summary

| File | Old Approach | New Approach |
|------|--------------|--------------|
| `index.php` | `html_writer` methods | `management_dashboard` template |
| `issue.php` | `html_writer` methods | `issue_form` template |
| `view.php` | `html_table` class | `issued_badges_table` template |

All three pages now use the modern template-based architecture! 🎉

---

**Updated**: 2025-01-22
**Moodle Version**: 4.1+
**Standards**: Compliant with Moodle 4.x Output API
