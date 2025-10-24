# Shaw Global Immigration Theme

A modern Full Site Editing (FSE) block theme for Shaw Global immigration website.

## Features

- Full Site Editing support
- Custom React blocks for immigration projects
- Responsive design
- Integration with Immigration Projects Manager plugin
- Custom taxonomies for countries and project categories

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Node.js 18+
- Immigration Projects Manager plugin
- Advanced Custom Fields (ACF) Pro plugin

## Installation

1. Upload the theme to `/wp-content/themes/`
2. Activate the theme in WordPress admin
3. Install and activate required plugins:
   - Immigration Projects Manager
   - Advanced Custom Fields Pro

## Development

### Setup

```bash
cd wp-content/themes/shawglobal-theme
npm install
```

### Development Mode

```bash
npm run start
```

This will start the development server with hot reloading. Edit files in `src/` directory.

### Production Build

```bash
npm run build
```

This will create optimized files in the `build/` directory.

## Custom Blocks

### Immigration Project Card

Display a single immigration project card.

**Settings:**
- Project selection
- Show/hide excerpt
- Show/hide meta information

### Immigration Projects Filter

Display a filterable list of immigration projects with AJAX loading.

**Settings:**
- Number of columns
- Posts per page

## File Structure

```
shawglobal-theme/
├── src/                    # Source files (edit these)
│   ├── blocks/            # React blocks
│   ├── scripts/           # JavaScript
│   └── styles/            # SCSS styles
├── build/                 # Compiled files (auto-generated)
├── templates/             # FSE templates
├── parts/                 # Template parts (header, footer)
├── patterns/              # Block patterns
├── functions.php          # Theme functions
├── theme.json             # Theme configuration
└── style.css              # Theme metadata

```

## Customization

### Colors

Edit colors in `theme.json` under `settings.color.palette`.

### Typography

Edit fonts and font sizes in `theme.json` under `settings.typography`.

### Spacing

Edit spacing values in `theme.json` under `settings.spacing`.

## Support

For issues and questions, please contact Shaw Global development team.
