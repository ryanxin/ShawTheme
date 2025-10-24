/**
 * Immigration Projects Filter Block
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import './style.scss';
import './editor.scss';

import metadata from './block.json';

registerBlockType(metadata.name, {
	edit: ({ attributes, setAttributes }) => {
		const { columns, postsPerPage } = attributes;
		const [selectedCountry, setSelectedCountry] = useState('');
		const [selectedCategory, setSelectedCategory] = useState('');
		const [projects, setProjects] = useState([]);
		const [isLoading, setIsLoading] = useState(false);

		const blockProps = useBlockProps({
			className: 'immigration-filter',
		});

		// Fetch countries
		const countries = useSelect((select) => {
			return select('core').getEntityRecords('taxonomy', 'project_country', {
				per_page: -1,
			});
		}, []);

		// Fetch categories
		const categories = useSelect((select) => {
			return select('core').getEntityRecords('taxonomy', 'project_category', {
				per_page: -1,
			});
		}, []);

		// Fetch projects based on filters
		useEffect(() => {
			const fetchProjects = async () => {
				setIsLoading(true);
				try {
					const params = {
						per_page: postsPerPage,
					};

					if (selectedCountry) {
						params.project_country = selectedCountry;
					}

					if (selectedCategory) {
						params.project_category = selectedCategory;
					}

					const response = await apiFetch({
						path: `/wp/v2/immigration_project?${new URLSearchParams(params)}`,
					});

					setProjects(response);
				} catch (error) {
					console.error('Error fetching projects:', error);
				} finally {
					setIsLoading(false);
				}
			};

			fetchProjects();
		}, [selectedCountry, selectedCategory, postsPerPage]);

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Display Settings', 'shawglobal-theme')}>
						<RangeControl
							label={__('Columns', 'shawglobal-theme')}
							value={columns}
							onChange={(value) => setAttributes({ columns: value })}
							min={1}
							max={4}
						/>
						<RangeControl
							label={__('Posts Per Page', 'shawglobal-theme')}
							value={postsPerPage}
							onChange={(value) => setAttributes({ postsPerPage: value })}
							min={3}
							max={24}
							step={3}
						/>
					</PanelBody>
				</InspectorControls>

				<div {...blockProps}>
					<div className="filter-controls">
						<select
							value={selectedCountry}
							onChange={(e) => setSelectedCountry(e.target.value)}
							aria-label={__('Filter by country', 'shawglobal-theme')}
						>
							<option value="">
								{__('All Countries', 'shawglobal-theme')}
							</option>
							{countries &&
								countries.map((country) => (
									<option key={country.id} value={country.id}>
										{country.name}
									</option>
								))}
						</select>

						<select
							value={selectedCategory}
							onChange={(e) => setSelectedCategory(e.target.value)}
							aria-label={__('Filter by category', 'shawglobal-theme')}
						>
							<option value="">
								{__('All Categories', 'shawglobal-theme')}
							</option>
							{categories &&
								categories.map((category) => (
									<option key={category.id} value={category.id}>
										{category.name}
									</option>
								))}
						</select>
					</div>

					<div
						className="projects-grid"
						style={{ gridTemplateColumns: `repeat(${columns}, 1fr)` }}
					>
						{isLoading ? (
							<div className="loading-message">
								{__('Loading projects...', 'shawglobal-theme')}
							</div>
						) : projects.length > 0 ? (
							projects.map((project) => (
								<div key={project.id} className="project-card">
									<h3 dangerouslySetInnerHTML={{ __html: project.title.rendered }} />
									{project.excerpt && (
										<div
											className="project-excerpt"
											dangerouslySetInnerHTML={{
												__html: project.excerpt.rendered,
											}}
										/>
									)}
								</div>
							))
						) : (
							<div className="no-results">
								{__('No projects found.', 'shawglobal-theme')}
							</div>
						)}
					</div>
				</div>
			</>
		);
	},

	save: () => {
		// Dynamic block - rendered via PHP
		return null;
	},
});
