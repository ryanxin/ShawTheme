# Immigration Projects Manager

Immigration Projects Manager is a WordPress plugin that provides custom post types, taxonomies, ACF fields, and REST API endpoints for managing immigration projects.

## Features

- **Custom Post Type**: `immigration_project` for managing immigration projects
- **Taxonomies**: 
  - `project_country`: Countries/Regions (Canada, Turkey, etc.)
  - `project_category`: Project Categories (Entrepreneurship, Technology, Investment, Property)
- **ACF Field Groups**:
  - Basic Information (processing period, identity type, investment, etc.)
  - Project Details (overview, advantages, requirements, process, about life)
  - Banner (image and subtitle)
- **REST API Endpoints**:
  - `/immigration/v1/projects` - Get filtered projects with AJAX support
  - `/immigration/v1/projects/:id` - Get single project details
  - `/immigration/v1/filters` - Get available filters (countries and categories)

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Advanced Custom Fields (ACF) plugin

## Installation

1. Upload the plugin folder to `/wp-content/plugins/` directory
2. Activate the plugin from the WordPress admin dashboard
3. Default countries and categories will be created automatically

## Usage

### Creating a Project

1. Navigate to **Immigration Projects** in the WordPress admin menu
2. Click **Add New Project**
3. Fill in the project details:
   - Title
   - Content
   - Featured Image
   - Select Country (from project_country taxonomy)
   - Select Category (from project_category taxonomy)
4. Fill in the custom ACF fields:
   - Basic Information (processing period, identity type, etc.)
   - Project Details (overview, advantages, requirements, etc.)
   - Banner (image and subtitle)
5. Publish the project

### REST API Examples

#### Get All Projects
```bash
GET /wp-json/immigration/v1/projects
```

#### Get Projects Filtered by Country
```bash
GET /wp-json/immigration/v1/projects?country=canada&per_page=12&paged=1
```

#### Get Projects Filtered by Category
```bash
GET /wp-json/immigration/v1/projects?category=investment&per_page=12&paged=1
```

#### Get Projects Filtered by Both Country and Category
```bash
GET /wp-json/immigration/v1/projects?country=canada&category=investment&per_page=12&paged=1
```

#### Get Single Project Details
```bash
GET /wp-json/immigration/v1/projects/123
```

#### Get Available Filters
```bash
GET /wp-json/immigration/v1/filters
```

### JavaScript/AJAX Integration

```javascript
// Example: Fetch filtered projects
fetch('/wp-json/immigration/v1/projects?country=canada&category=investment')
  .then(response => response.json())
  .then(data => {
    console.log('Projects:', data.data);
    console.log('Total pages:', data.total_pages);
  });

// Example: Get filters
fetch('/wp-json/immigration/v1/filters')
  .then(response => response.json())
  .then(data => {
    console.log('Countries:', data.countries);
    console.log('Categories:', data.categories);
  });
```

## Default Countries and Categories

### Countries
- Canada
- Turkey
- Antigua and Barbuda
- Greece
- Singapore
- Japan
- United States
- Philippines
- Portugal
- All

### Project Categories
- Entrepreneurship Immigration
- Technology Immigration
- Investment Immigration
- Property Immigration

## ACF Field Structure

### Basic Information Group
- `processing_period` (Text)
- `identity_type` (Text)
- `investment_amount` (Text)
- `residential_requirement` (Text)
- `language_requirement` (Text)

### Project Details Group
- `project_overview` (WYSIWYG Editor)
- `project_advantages` (Repeater)
  - `icon` (Image)
  - `title` (Text)
  - `description` (Textarea)
- `application_requirements` (WYSIWYG Editor)
- `application_process` (Repeater)
  - `step_number` (Number)
  - `content` (Textarea)
- `about_life` (Gallery)

### Banner Group
- `banner_image` (Image)
- `banner_subtitle` (Text)

## Developing Custom Blocks

To create custom React blocks for displaying projects, you can use the REST API endpoints provided by this plugin.

Example: Creating a projects filter block to display in your theme template.

## Troubleshooting

### Projects not showing up?
- Ensure the plugin is activated
- Check that you've published the projects
- Verify the project is assigned to a country and category

### ACF fields not showing?
- Make sure ACF plugin is installed and activated
- Flush WordPress rewrite rules: Go to Settings > Permalinks and click Save

### REST API not working?
- Check WordPress REST API is enabled (default in WordPress 4.7+)
- Verify permalinks are not set to "Plain"

## Support

For issues or feature requests, please contact Shaw Global support.

## License

This plugin is licensed under the GPL-2.0-or-later License.
