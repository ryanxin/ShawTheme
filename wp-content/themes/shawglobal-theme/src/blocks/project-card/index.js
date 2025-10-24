/**
 * Immigration Project Card Block
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import './style.scss';
import './editor.scss';

import metadata from './block.json';

registerBlockType(metadata.name, {
	edit: ({ attributes, setAttributes }) => {
		const { projectId, showExcerpt, showMeta } = attributes;
		const blockProps = useBlockProps({
			className: 'immigration-project-card',
		});

		// Fetch immigration projects
		const projects = useSelect((select) => {
			return select('core').getEntityRecords('postType', 'immigration_project', {
				per_page: -1,
			});
		}, []);

		// Fetch selected project
		const selectedProject = useSelect(
			(select) => {
				if (!projectId) return null;
				return select('core').getEntityRecord('postType', 'immigration_project', projectId);
			},
			[projectId]
		);

		// Create options for select control
		const projectOptions = projects
			? [
					{ label: __('Select a project', 'shawglobal-theme'), value: 0 },
					...projects.map((project) => ({
						label: project.title.rendered,
						value: project.id,
					})),
			  ]
			: [{ label: __('Loading...', 'shawglobal-theme'), value: 0 }];

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Project Settings', 'shawglobal-theme')}>
						<SelectControl
							label={__('Select Project', 'shawglobal-theme')}
							value={projectId}
							options={projectOptions}
							onChange={(value) => setAttributes({ projectId: parseInt(value) })}
						/>
						<ToggleControl
							label={__('Show Excerpt', 'shawglobal-theme')}
							checked={showExcerpt}
							onChange={() => setAttributes({ showExcerpt: !showExcerpt })}
						/>
						<ToggleControl
							label={__('Show Meta Information', 'shawglobal-theme')}
							checked={showMeta}
							onChange={() => setAttributes({ showMeta: !showMeta })}
						/>
					</PanelBody>
				</InspectorControls>

				<div {...blockProps}>
					{!selectedProject ? (
						<div className="placeholder">
							<p>{__('Please select a project from the settings panel.', 'shawglobal-theme')}</p>
						</div>
					) : (
						<div className="project-card-content">
							{selectedProject.featured_media && (
								<div className="project-thumbnail">
									<img
										src={selectedProject.featured_media}
										alt={selectedProject.title.rendered}
									/>
								</div>
							)}
							<div className="project-info">
								<h3
									className="project-title"
									dangerouslySetInnerHTML={{ __html: selectedProject.title.rendered }}
								/>
								{showExcerpt && selectedProject.excerpt && (
									<div
										className="project-excerpt"
										dangerouslySetInnerHTML={{ __html: selectedProject.excerpt.rendered }}
									/>
								)}
								{showMeta && (
									<div className="project-meta">
										<span className="meta-item">
											{__('View Details →', 'shawglobal-theme')}
										</span>
									</div>
								)}
							</div>
						</div>
					)}
				</div>
			</>
		);
	},

	save: () => {
		// Dynamic block - rendered via PHP
		return null;
	},
});
