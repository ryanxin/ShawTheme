# Shaw Immigration Projects Plugin

A WordPress plugin for managing immigration projects with AJAX filtering, custom post types, and Elementor integration.

## Features

- **Custom Post Type**: `immigration_project`
- **Taxonomies**: 
  - `project_country` (Countries)
  - `project_category` (Project Categories)
- **ACF Fields**: Complete field set for project details
- **AJAX Filtering**: Dual-layer tab filtering without page reload
- **REST API**: Custom endpoints for project filtering
- **Elementor Compatible**: Works with Elementor Loop Grid
- **Responsive Design**: Mobile-friendly layouts

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Install and activate Advanced Custom Fields (ACF) plugin
4. Go to Settings > Permalinks and click "Save Changes" to flush rewrite rules

## Usage

### Creating Projects

1. Go to **Immigration Projects** > **Add New** in WordPress admin
2. Fill in the project details:
   - Title
   - Content (main description)
   - Featured Image
   - ACF Fields (processing period, identity type, etc.)
3. Assign **Countries** and **Categories**
4. Publish

### Setting Up Taxonomies

1. **Countries**: Go to **Immigration Projects** > **Countries**
   - Add countries like: Canada, USA, Turkey, etc.
   - Use slugs like: `canada`, `usa`, `turkey`

2. **Categories**: Go to **Immigration Projects** > **Categories**
   - Add categories like: Entrepreneur Immigration, Skilled Worker, etc.
   - Use slugs like: `entrepreneur-immigration`, `skilled-worker-immigration`

### Using with Elementor

#### Method 1: Archive Page with AJAX Tabs

1. Create a new page or use Theme Builder for Immigration Projects archive
2. Add an **HTML** widget and paste the content from `templates/archive-immigration-project.html`
3. The AJAX filtering will work automatically

#### Method 2: Loop Grid (Static)

1. Add an **Elementor Loop Grid** widget
2. Configure Query:
   - Source: Posts
   - Post Type: Immigration Project
3. Configure Layout:
   - Columns: 3
   - Gap: 40px
4. Configure Loop Item Template:
   - Add Image widget: Dynamic Tags > ACF Field > `card_image`
   - Add Heading: Dynamic Tags > Post Title
   - Add Text: Dynamic Tags > ACF Field > `short_description`
   - Add Button: Link to Dynamic Tags > Post URL

#### Method 3: Custom Template with Tabs

1. Create a new Elementor template
2. Add the dual-layer tabs structure (see `archive-immigration-project.html`)
3. Below the tabs, add a Loop Grid with class `shaw-projects-grid`
4. The JavaScript will handle tab filtering

### ACF Fields Reference

The plugin automatically registers these ACF fields:

**Overview Fields:**
- `processing_period` - Text (e.g., "20 months")
- `identity_type` - Text (e.g., "Permanent Resident")
- `investment_amount` - Text (e.g., "No mandatory requirements")
- `residential_requirements` - Text
- `language` - Text (e.g., "CLB5")

**Content Fields:**
- `card_image` - Image (for listing page)
- `short_description` - Textarea (brief description)
- `project_overview` - WYSIWYG
- `project_gallery` - Gallery
- `advantages` - Repeater (advantage_text)
- `application_requirements` - WYSIWYG
- `application_process` - WYSIWYG
- `about_life` - WYSIWYG
- `life_media` - Repeater (title, media_type, image, video_url)
- `is_featured` - True/False

### REST API Endpoints

#### Get Filtered Projects
```
GET /wp-json/shaw-immigration/v1/projects
```

Parameters:
- `country` (string) - Country slug or "all"
- `category` (string) - Category slug or "all"
- `page` (int) - Page number (default: 1)
- `per_page` (int) - Posts per page (default: 9)

Response:
```json
{
  "projects": [...],
  "total": 27,
  "pages": 3,
  "current_page": 1
}
```

#### Get Filter Options
```
GET /wp-json/shaw-immigration/v1/filters
```

Response:
```json
{
  "countries": [
    {"slug": "canada", "name": "Canada", "count": 15},
    ...
  ],
  "categories": [
    {"slug": "entrepreneur-immigration", "name": "Entrepreneur Immigration", "count": 8},
    ...
  ]
}
```

### JavaScript Integration

The plugin automatically loads JavaScript on:
- Immigration Projects archive pages
- Single Immigration Project pages
- Taxonomy pages (Country, Category)

**Global JavaScript Object:**
```javascript
window.shawImmigration = {
    ajaxUrl: 'https://yoursite.com/wp-json/shaw-immigration/v1/',
    nonce: 'wp_rest_nonce_value'
}
```

### Styling

The plugin includes comprehensive CSS based on the Figma design. You can override styles by adding custom CSS in your theme:

```css
/* Example: Change accent color */
:root {
    --shaw-accent: #your-color;
}

/* Example: Customize project cards */
.shaw-project-card {
    /* your styles */
}
```

### Single Project Template

The plugin includes a custom single project template. To override it:

1. Copy `templates/single-immigration-project.php` to your theme
2. Rename to `single-immigration-project.php`
3. Modify as needed

Or use Elementor Theme Builder:
1. Create a new Single template
2. Set condition: Post Type > Immigration Project
3. Design your template using Dynamic Tags to display ACF fields

### Contact Form Integration

The single project template includes a placeholder contact form. To use a real form:

1. **Contact Form 7**: Replace the placeholder with:
   ```php
   <?php echo do_shortcode('[contact-form-7 id="123"]'); ?>
   ```

2. **Elementor Form**: Create a form and use:
   ```php
   <?php echo do_shortcode('[elementor-template id="456"]'); ?>
   ```

3. **WPForms**: Use:
   ```php
   <?php echo do_shortcode('[wpforms id="789"]'); ?>
   ```

## Customization

### Adding New ACF Fields

To add custom fields, edit `shaw-immigration-projects.php` and modify the `register_acf_fields()` method.

### Modifying REST API

To customize the REST API response, edit the `get_filtered_projects()` and `get_filter_options()` methods.

### Changing Permalinks

Projects use the slug `immigration-projects` by default. To change:

1. Edit the `rewrite` parameter in `register_post_type()`
2. Flush permalinks: Settings > Permalinks > Save

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Advanced Custom Fields (ACF) plugin
- (Optional) Elementor for visual page building

## Support

For issues or questions, please contact Shaw Global support.

## Version History

### 1.0.0
- Initial release
- Custom Post Type and Taxonomies
- ACF field groups
- AJAX filtering with dual-layer tabs
- REST API endpoints
- Elementor integration
- Responsive design

