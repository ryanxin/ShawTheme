const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		// Main scripts and styles
		main: path.resolve(process.cwd(), 'src/scripts', 'main.js'),
		// Blocks
		'blocks/project-card/index': path.resolve(process.cwd(), 'src/blocks/project-card', 'index.js'),
		'blocks/projects-filter/index': path.resolve(process.cwd(), 'src/blocks/projects-filter', 'index.js'),
	},
};
