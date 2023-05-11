<?php

use Saltus\WP\Plugin\Saltus\InteractiveMaps\Plugin\MapList;

function igmaps_maps() {

	$map_list = new MapList();
	$options  = json_decode( $map_list->maps, true );

	$options = apply_filters( 'igm_map_options', $options );

	return $options;
}

function igmaps_model() {

	// Actions
	$actions_default = apply_filters( 'igm_click_actions', [] );

	$options = get_option( 'interactive-maps' );

	$tooltip_editor        = isset( $options['tooltip_editor'] ) ? $options['tooltip_editor'] : 'textarea';
	$color_field           = isset( $options['color_field_type'] ) ? $options['color_field_type'] : 'color';
	$action_content_editor = isset( $options['actionContent_editor'] ) ? $options['actionContent_editor'] : 'textarea';
	$description_editor    = isset( $options['description_editor'] ) ? $options['description_editor'] : 'text';
	$ttemplate_editor      = isset( $options['tooltip_template_editor'] ) ? $options['tooltip_template_editor'] : 'text';
	$capability            = isset( $options['capability'] ) ? $options['capability'] : 'page';

	$after_map = '';
	if ( isset( $_GET['post'] ) && ! is_array( $_GET['post'] ) ) {
		$id        = esc_attr( $_GET['post'] );
		$after_map = apply_filters( 'igm_map_after', $after_map, $id );
	}

	$plugins_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );

	$model = [
		'active'   => true,
		'type'     => 'cpt',
		'name'     => 'igmap',
		'supports' => [
			'title',
		],
		'features' => [
			'duplicate'     => [
				'label'      => __( 'Clone Map', 'interactive-geo-maps' ),
				'attr_title' => __( 'Create a copy of this map', 'interactive-geo-maps' ),
			],
			'single_export' => [
				'label' => __( 'Download Map Export File', 'interactive-geo-maps' ),
			],
			'admin_cols'    => array(
				'title',
				'description' => array(
					'title'    => __( 'Description', 'interactive-geo-maps' ),
					'function' => function() {
						global $post;
						$meta = get_post_meta( $post->ID, 'map_info', true );
						if ( isset( $meta['description'] ) ) {
							echo esc_html( $meta['description'] );
						}
					},
				),
				'id'          => array(
					'title'      => 'ID',
					'post_field' => 'ID',
					'default'    => 'DESC',
				),
				'image'       => array(
					'title'    => __( 'Image Preview', 'interactive-geo-maps' ),
					'function' => function() {
						global $post;

						$image_meta = get_post_meta( $post->ID, 'map_image', true );
						$image      = isset( $image_meta['mapImage'] ) && $image_meta['mapImage'] !== '' ? $image_meta['mapImage'] : '';

						if ( '' !== $image ) {
							echo sprintf( '<img style="max-width:100%%;" src="%1$s">', $image );
						}

						echo '';
					},
				),
				'shortcode'   => array(
					'title'    => __( 'Shortcode', 'interactive-geo-maps' ),
					'function' => function() {
						global $post;
						echo esc_html( '[display-map id="' . $post->ID . '"]' );
					},
				),

			),
		],
		'labels'   => [
			'has_one'     => __( 'Map', 'interactive-geo-maps' ),
			'has_many'    => __( 'Maps', 'interactive-geo-maps' ),
			'text_domain' => 'interactive-geo-maps',
			'overrides'   => [
				'labels'        => [
					'name'                  => __( 'Maps', 'interactive-geo-maps' ),
					'singular_name'         => __( 'Map', 'interactive-geo-maps' ),
					'menu_name'             => __( 'Maps', 'interactive-geo-maps' ),
					'name_admin_bar'        => __( 'Map', 'interactive-geo-maps' ),
					'add_new'               => __( 'Add New', 'interactive-geo-maps' ),
					'add_new_item'          => __( 'Add New Map', 'interactive-geo-maps' ),
					'edit_item'             => __( 'Edit Map', 'interactive-geo-maps' ),
					'new_item'              => __( 'New Map', 'interactive-geo-maps' ),
					'view_item'             => __( 'View Map', 'interactive-geo-maps' ),
					'view_items'            => __( 'View Maps', 'interactive-geo-maps' ),
					'search_items'          => __( 'Search Maps', 'interactive-geo-maps' ),
					'not_found'             => __( 'No maps found', 'interactive-geo-maps' ),
					'not_found_in_trash'    => __( 'No maps found in trash', 'interactive-geo-maps' ),
					'parent_item-colon'     => __( 'Parent Maps:', 'interactive-geo-maps' ),
					'all_items'             => __( 'All Maps', 'interactive-geo-maps' ),
					'archives'              => __( 'Map Archives', 'interactive-geo-maps' ),
					'attributes'            => __( 'Map Attributes', 'interactive-geo-maps' ),
					'insert_into_item'      => __( 'Insert into Map', 'interactive-geo-maps' ),
					'uploaded_to_this_item' => __( 'Upload to this map', 'interactive-geo-maps' ),
					'featured_image'        => __( 'Featured Image', 'interactive-geo-maps' ),
					'set_featured_image'    => __( 'Set featured image', 'interactive-geo-maps' ),
					'remove_featured_image' => __( 'Remove featured image', 'interactive-geo-maps' ),
					'use_featured_image'    => __( 'Use featured image', 'interactive-geo-maps' ),
					'filter_items_list'     => __( 'Filter maps list', 'interactive-geo-maps' ),
					'items_list_navigation' => __( 'Maps list navigation', 'interactive-geo-maps' ),
					'items_list'            => __( 'Maps list', 'interactive-geo-maps' ),
				],
				// you can use the placeholders {permalink}, {preview_url}, {date}
				'messages'      => [
					'post_updated'         => __( 'Map information updated.', 'interactive-geo-maps' ),
					'post_updated_short'   => __( 'Map information updated.', 'interactive-geo-maps' ),
					'custom_field_updated' => __( 'Custom field updated', 'interactive-geo-maps' ),
					'custom_field_deleted' => __( 'Custom field deleted', 'interactive-geo-maps' ),
					'post_published'       => __( 'Map Published', 'interactive-geo-maps' ),
					'post_saved'           => __( 'Map information saved.', 'interactive-geo-maps' ),
					'post_submitted'       => __( 'Map submitted. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-geo-maps' ),
					'post_schedulled'      => __( 'Map publishing scheduled for {date}. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-geo-maps' ),
					'post_draft_updated'   => __( 'Map draft updated. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-geo-maps' ),
				],
				'bulk_messages' => [
					'updated_singular'   => __( 'Map updated.', 'interactive-geo-maps' ),
					'updated_plural'     => __( '%s Maps updated.', 'interactive-geo-maps' ),
					'locked_singular'    => __( 'Map not updated, somebody is editing it.', 'interactive-geo-maps' ),
					'locked_plural'      => __( '%s Map not updated, somebody is editing them.', 'interactive-geo-maps' ),
					'deleted_singular'   => __( 'Map permanetly deleted.', 'interactive-geo-maps' ),
					'deleted_plural'     => __( '%s Map permanently deleted.', 'interactive-geo-maps' ),
					'trashed_singular'   => __( 'Map moved to the trash.', 'interactive-geo-maps' ),
					'trashed_plural'     => __( '%s Maps moved to the trash.', 'interactive-geo-maps' ),
					'untrashed_singular' => __( 'Map recovered from trash.', 'interactive-geo-maps' ),
					'untrashed_plural'   => __( '%s Maps recovered from trash', 'interactive-geo-maps' ),
				],
				'ui'            => [
					'enter_title_here' => __( 'Enter map title here', 'interactive-geo-maps' ),
				],
			],
		],
		'options'  => [
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => false,
			'has_archive'         => false,
			'hierarchical'        => true,
			'show_in_rest'        => true,
			'menu_position'       => null,
			'can_export'          => true,
			'exclude_from_search' => true,
			'capability_type'     => $capability,
			'menu_icon'           => 'dashicons-admin-site',
		],
		'settings' => [
			'interactive-maps' => [
				'title'         => __( 'Global Settings', 'interactive-geo-maps' ),
				'menu_title'    => __( 'Settings', 'interactive-geo-maps' ),
				'footer_text'   => '', // we filter this later to add the current version number
				'show_bar_menu' => false,
				'ajax_save'     => true,
				'sections'      => [
					'editing'     => [
						'title'  => __( 'Editing', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-pencil-square-o',
						'fields' => [
							'editing_intro'      => [
								'type'    => 'content',
								'content' => __( 'Options to control the Map edit screen.', 'interactive-geo-maps' ),
							],
							'geocodingheader'    => [
								'type'    => 'subheading',
								'content' => __( 'Coordinates Fields Options', 'interactive-geo-maps' ),
							],
							'googleApiKey'       => [
								'type'    => 'text',
								'title'   => __( 'Google API Key', 'interactive-geo-maps' ),
								'desc'    => __( 'The API Key is not mandatory, the maps will still work without it. The API Key is only necessary it you want to automatically get the coordinates for an address/location when adding a marker. You can still enter the coordinates manually.', 'interactive-geo-maps' ),
								'default' => '',
							],
							'googleAutocomplete' => [
								'type'       => 'switcher',
								'title'      => __( 'Autocomplete', 'interactive-geo-maps' ),
								'desc'       => __( 'When writing a location name or address for a marker, there will be autocomplete suggestions.<br>In order for this to work, you will also need to have the "Places API" connected with your project/API Key added above.', 'interactive-geo-maps' ),
								'default'    => false,
								'dependency' => [ 'googleApiKey', '!=', '' ],

							],
							'previewHeader'  => [
								'type'    => 'subheading',
								'content' => __( 'Preview', 'interactive-geo-maps' ),
							],
							'preview' => [
								'type'       => 'switcher',
								'title'      => __( 'Enable Live Preview', 'interactive-geo-maps' ),
								'desc'    => __( 'The live preview might slow down the edit screen if you\'re building a map with a big amount of data.<br>When disabled, only non blocking preview changes will be processed.', 'interactive-geo-maps' ),
								'default'    => true,
							],
							'saveImage' => [
								'type'       => 'switcher',
								'title'      => __( 'Save Image Preview', 'interactive-geo-maps' ),
								'desc'    => __( 'On rare ocasions the image preview saving process might cause errors on your server.<br>In that case you could disable the option to save the image preview of the map.', 'interactive-geo-maps' ),
								'default'    => true,
							],
							'otherFieldsHeader'  => [
								'type'    => 'subheading',
								'content' => __( 'Other Fields', 'interactive-geo-maps' ),
							],
							'tooltip_editor'     => [
								'type'    => 'select',
								/* translators: field type = input type (textarea, rich text editor...) */
								'title'   => __( 'Tooltip field type', 'interactive-geo-maps' ),
								'desc'    => __( 'Type of field to use to edit the tooltip content. <br> When using the Rich Text Editor, if you have a lot of entries, you might come across the "<a href=\'https://interactivegeomaps.com/docs/max-input-vars-issue/\' target=\'_blank\'>Too much data" issue</a>.', 'interactive-geo-maps' ),
								'default' => 'textarea',
								'options' => [
									'textarea'  => __( 'Textarea', 'interactive-geo-maps' ),
									/* translators: equivalent to classic editor in a way */
									'wp_editor' => __( 'WP Rich Text Editor', 'interactive-geo-maps' ),
									'text'      => __( 'Text Input', 'interactive-geo-maps' ),
								],
							],

						],
					],
					'performance' => [
						'title'  => __( 'Performance', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-tachometer',
						'fields' => [
							'performance_intro' => [
								'type'    => 'content',
								'content' => __( 'Options to improve performance of the overall maps loading and rendering.<br>Not all features will work the same for all users, some might be incompatible with other settings or plugins you might have installed, so review the options carefully.', 'interactive-geo-maps' ),
							],
							'frontend'          => [
								'type'    => 'subheading',
								'content' => __( 'Frontend Improvements', 'interactive-geo-maps' ),
							],
							'animations'        => [
								'type'    => 'switcher',
								'title'   => __( 'Animations', 'interactive-geo-maps' ),
								'desc'    => __( 'Animations will increase CPU usage. That is OK for basic maps, but for ones that have a lot of data, it might be a good idea to disable animations.', 'interactive-geo-maps' ),
								'default' => true,
							],
							'lazyLoad'          => [
								'type'    => 'switcher',
								'title'   => __( 'Lazy Loading', 'interactive-geo-maps' ),
								'desc'    => __( 'By default all maps will render on page load, even if they are way outside the current viewport. If you enable this option you can ensure that the map will only start initializing when its container scrolls into view.', 'interactive-geo-maps' ),
								'default' => true,
							],

							'async'             => [
								'type'    => 'switcher',
								'title'   => __( 'Async Loading', 'interactive-geo-maps' ),
								'desc'    => __( 'BETA FEATURE. When enabled the map scripts and source files will load asynchronously. <br>Don\'t use this option if you want the maps to work on older Internet Explorer versions. <br> When using a cache/minify plugin, double check if this works when the scripts are cached/minified.', 'interactive-geo-maps' ),
								'default' => false,
							],

							'hold'             => [
								'type'    => 'switcher',
								'title'   => __( 'Hold Map rendering', 'interactive-geo-maps' ),
								'desc'    => __( 'BETA FEATURE. Do not render maps. Preview will also not render the map. You can programatically load them with the code <pre>iMaps.init(false);</pre>', 'interactive-geo-maps' ),
								'default' => false,
							],

							'backend'           => [
								'type'    => 'subheading',
								'content' => __( 'Backend Improvements', 'interactive-geo-maps' ),
							],

							'dictionary'        => [
								'type'    => 'switcher',
								'title'   => __( 'Region Name Conversion', 'interactive-geo-maps' ),
								'desc'    => __( 'If you\'re using the correct region codes in all your maps, you can disable this.<br>It will prevent the plugin from trying to convert any region name that needs to be converted into a proper region code.<br>This might help prevent memory issues on maps with a big number of regions.', 'interactive-geo-maps' ),
								'default' => true,
							],

						],
					],
					'mapFeatures' => [
						'title'  => __( 'Map Features', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-plus-circle',
						'fields' => [
							'zoomMaster' => [
								'type'    => 'switcher',
								'title'   => __( 'Zoom', 'interactive-geo-maps' ),
								'desc'    => __( 'Enable simple zoom controls for all maps by default.', 'interactive-geo-maps' ),
								'default' => false,
							],
						],
					],
				],
			],
		],
		'meta'     => [
			'go_pro'           => [
				'title'     => __( 'Unlock Pro Features', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'context'   => 'side',
				'priority'  => 'high',
				'fields'    => [
					'goProInfo' => [
						'type'    => 'content',
						'content' => '<a href="https://interactivegeomaps.com/features" target="_blank">' . __( 'Available features in the Pro version.', 'interactive-geo-maps' ) . '</a> <span class="dashicons dashicons-external"></span>',
					],
				],
			],
			'map_regions_info' => [
				'title'     => __( 'Region Codes', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'context'   => 'side',
				'priority'  => 'low',
				'fields'    => [
					'regionDataInfo' => [
						'type'    => 'content',
						'content' => sprintf( '<div id="map_region_data">%s</div>', __( 'Select a map to display and save the changes to see the list of available regions.', 'interactive-geo-maps' ) ),
					],
					'regionData'     => [
						'type'    => 'textarea',
						'default' => '',
						'class'   => 'hidden',
					],
				],
			],
			'shortcode'        => [
				'title'     => __( 'Shortcode', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'context'   => 'side',
				'priority'  => 'high',
				'fields'    => [
					'shortcodeInfo' => [
						'type'    => 'content',
						'content' => __( 'Place this shortcode where you want the map to display.', 'interactive-geo-maps' ),
					],
					'shortcode'     => [
						'type'    => 'content',
						'content' => '',
					],
				],
			],
			'map_preview'      => [
				'title'     => __( 'Map Preview', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'fields'    => [
					'preview' => [
						'type'    => 'content',
						'content' => sprintf(
							'<div class="map_preview">
								<div class="map_wrapper" id="">
									<div class="map_box">
										<div class="map_aspect_ratio" style="width: 100%%; padding-top: 56%%;">
											<div class="map_blocked_preview map_block_hidden"><div>%5$s</div></div>
											<div class="map_container">
												<div class="map_render" id=""></div>
											</div>
										</div>
									</div>

								</div>
								%7$s
								<div class="map_information">
									<div class="map_information_box" id="map_visual_info_box">
										<div class="map_information_title">%1$s</div>
										<div id="map_visual_info"></div>
									</div>
									<div class="map_information_box" style="display:none;">
										<div class="map_information_title">%2$s</div>
										<div id="map_click_events_info"></div>
	
									</div>
									<div class="map_information_box" id="map_clicked_coordinates" style="display:none;">
										<div class="map_information_title">%3$s</div>
										<div id="map_click_events_coordinates"></div>
										<div id="map_clicked_coordinates_box" style="display:none;">
										</div>
									</div>
									
								</div>
							</div>
							<div class="igm_small">%4$s<br>%6$s <span class="dashicons dashicons-external"></span></div>',
							__( 'Map Information', 'interactive-geo-maps' ),
							__( 'Clicked Region/Marker', 'interactive-geo-maps' ),
							__( 'Clicked Coordinates', 'interactive-geo-maps' ), // 3rd
							__( 'Click actions will not be fully executed in the preview.', 'interactive-geo-maps' ),
							__( 'Save your map to restart the live preview.', 'interactive-geo-maps' ),
							'<span class="dashicons dashicons-sos"></span><a href="https://interactivegeomaps.com/topic/troubleshooting/" target="_blank">' . __( 'Map not displaying?', 'interactive-geo-maps' ) . '</a>',
							$after_map
						),
					],
				],
			],
			'map_image'        => [
				'title'     => __( 'Map Image', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'context'   => 'side',
				'priority'  => 'low',
				'fields'    => [
					'mapImageInfo' => [
						'type'    => 'content',
						'content' => sprintf( '<div id="map_image_preview">%s</div>', __( 'No image generated yet.', 'interactive-geo-maps' ) ),
					],
					'mapImage'     => [
						'type'    => 'textarea',
						'default' => '',
						'class'   => 'hidden',
					],
				],
			],
			'map_info'         => [
				'title'     => __( 'Map Settings', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'sections'  => [
					'general'      => [
						'title'  => __( 'General Details', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-globe',
						'fields' => [

							'map'              => [
								'type'        => 'select',
								'title'       => __( 'Map to display', 'interactive-geo-maps' ),
								'subtitle'    => __( 'Select one of the available maps.', 'interactive-geo-maps' ),
								'desc'        => __( '"Low" maps will have less details and therefore load faster.<br> "High" maps have more detail, but they are bigger files.', 'interactive-geo-maps' ),
								'default'     => 'worldLow',
								'placeholder' => __( 'Please select...', 'interactive-geo-maps' ),
								'options'     => igmaps_maps(),
								'chosen'      => true,
							],
							'mapURL'           => [
								'type'       => 'text',
								'title'      => __( 'Custom URL Source', 'interactive-geo-maps' ),
								'desc'       => __( 'Link to geojson file.', 'interactive-geo-maps' ),
								'default'    => '',
								'dependency' => [ 'map', '==', 'custom' ],
							],
							'usaWarning'       => [
								'type'       => 'content',
								'dependency' => [ 'map', 'any', 'usaLow,usaHigh' ],
								'content'    => __( 'We suggest you to select AlbersUSA projection below.', 'interactive-geo-maps' ),
							],
							'projection'       => [
								'type'        => 'select',
								'title'       => __( 'Projection', 'interactive-geo-maps' ),
								'desc'        => __( 'A map projection is a way to flatten a globe\'s surface into a plane in order to make a map. There are different techniques which render the map differently.', 'interactive-geo-maps' ),
								'subtitle'    => __( 'Select map projection.', 'interactive-geo-maps' ),
								'default'     => 'Miller',
								'placeholder' => __( 'Please select...', 'interactive-geo-maps' ),
								'options'     => [
									'Miller'    => 'Miller',
									'AlbersUsa' => 'AlbersUsa (For US Maps)',
									'Mercator'  => 'Mercator',
								],
							],
							'albersUsaWarning' => [
								'type'       => 'content',
								'dependency' => [ 'projection', '==', 'AlbersUsa' ],
								'content'    => __( 'The AlbersUSA projection will only work well with USA High/Low maps', 'interactive-geo-maps' ),
							],
							'description'      => [
								'type'    => $description_editor,
								'title'   => __( 'Description', 'interactive-geo-maps' ),
								'desc'    => __( 'Map description.', 'interactive-geo-maps' ),
								'default' => '',
							],
						],
					],
					'visual'       => [
						'title'  => __( 'Visual Settings', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-paint-brush fa-lg',
						'fields' => [
							'visual'       => [
								'type'   => 'fieldset',
								'fields' => [
									'visual_fieldset_info' => [
										'type'    => 'subheading',
										'content' => __( 'Default Visual Settings', 'interactive-geo-maps' ),
									],
									'backgroundColor'      => [
										'type'    => $color_field,
										'title'   => __( 'Background Color', 'interactive-geo-maps' ),
										'default' => 'transparent',
									],
									'borderColor'          => [
										'type'    => $color_field,
										'title'   => __( 'Borders Color', 'interactive-geo-maps' ),
										'default' => '#f9f9f9',
									],
									'borderWidth'          => [
										'type'       => 'spinner',
										'title'      => __( 'Borders Width', 'interactive-geo-maps' ),
										'desc'       => __( 'Region borders width.', 'interactive-geo-maps' ),
										'default'    => '1',
										'min'        => 0,
										'max'        => 10,
										'step'       => 0.2,
										'attributes' => [
											'step' => 'any',
										],
									],
									'paddingTop'           => [
										'type'    => 'spinner',
										'title'   => __( 'Map Height', 'interactive-geo-maps' ),
										'default' => '56',
										'min'     => 10,
										'max'     => 100,
										'step'    => 1,
										'unit'    => '%',
										'desc'    => __( 'The default 56% corresponds to a 16:9 aspect ratio. Use percentual values to make sure the map is responsive and calculates the height based on it\'s width.', 'interactive-geo-maps' ),
									],
									'paddingTopMobile'           => [
										'type'    => 'spinner',
										'title'   => __( 'Map Height on Mobile', 'interactive-geo-maps' ),
										'default' => '',
										'min'     => 10,
										'max'     => 100,
										'step'    => 1,
										'unit'    => '%',
										'desc'    => __( 'Leave blank to use the same value above. On mobile there might be the need for the map to take more vertical space. You can increase the map height here, to change its aspect ratio.', 'interactive-geo-maps' ),
									],
									'maxWidth'             => [
										'type'    => 'spinner',
										'title'   => __( 'Map Max-Width', 'interactive-geo-maps' ),
										'desc'    => __( 'Leave empty if you always want your map to take 100% of the available space. Otherwise set a maximum pixel width for the map.', 'interactive-geo-maps' ),
										'default' => '2000',
										'min'     => 10,
										'max'     => 100,
										'step'    => 1,
										'unit'    => 'px',
									],
									'fontFamily'           => [
										'type'    => 'text',
										'title'   => __( 'Font Family', 'interactive-geo-maps' ),
										'default' => 'inherit',
										'desc'    => __( 'Leave blank or write "inherit" to inherit the font-family from the page or container.', 'interactive-geo-maps' ),
									],
								],
							],
							'goPro_visual' => [
								'type'    => 'content',
								'content' => __( 'Do you want to change the initial zoom and map center position? <a href="https://interactivegeomaps.com/feature/change-initial-zoom-and-center/" target="_blank">Go Pro</a>', 'interactive-geo-maps' ) . '<span class="dashicons dashicons-external"></span>',
							],

						],
					],
					'regions'      => [
						'title'  => __( 'Regions', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-map fa-lg',
						'class'  => 'regions_tab',
						'fields' => [
							'regions_info'      => [
								'type'    => 'content',
								'content' => __( 'Give interactivity to the regions of the selected map.<br>Click "Add New Region" below or the plus sign on the region list in the sidebar, to start adding data and interactivity to the regions.', 'interactive-geo-maps' ),
							],
							'regions'           => [
								'type'         => 'group',
								'button_title' => __( 'Add New Region', 'interactive-geo-maps' ),
								'fields'       => [
									'name'           => [
										'type'       => 'text',
										'title'      => __( 'Name', 'interactive-geo-maps' ),
										'attributes' => [
											'class' => 'skip-preview',
										],
									],
									'id'             => [
										'type'       => 'text',
										'title'      => __( 'Region Code *', 'interactive-geo-maps' ),
										'desc'       => __( 'Check the table on the right to see the available region codes or start typing a region name.', 'interactive-geo-maps' ),
										'attributes' => [
											'class'        => 'region-code-autocomplete',
											'autocomplete' => 'off',
										],
									],
									'tooltipContent' => [
										'type'  => $tooltip_editor,
										'title' => __( 'Tooltip Content', 'interactive-geo-maps' ),
									],
									'content'        => [
										'type'     => $action_content_editor,
										'title'    => __( 'Action Content', 'interactive-geo-maps' ),
										'subtitle' => __( 'URL or content to trigger when region is clicked.', 'interactive-geo-maps' ) . '<br><span class="igm_select_region_click_reminder">' . __( 'Don\'t forget to select a click action below.', 'interactive-geo-maps' ) . '</span>',
										//'media_buttons' => false,
									],
								],
							],
							'regionDefaults'    => [
								'type'   => 'fieldset',
								'title'  => __( 'Default values', 'interactive-geo-maps' ),
								'desc'   => __( 'Do you want to colour each region individually? <a href="https://interactivegeomaps.com/pricing" target="_blank">Go Pro</a>', 'interactive-geo-maps' ) . '<span class="dashicons dashicons-external"></span>',
								'fields' => [
									'fill'          => [
										'type'    => $color_field,
										/* translators: color to use to fill the map region */
										'title'   => __( 'Fill Color', 'interactive-geo-maps' ),
										'default' => isset ( $options['defaultActiveColor'] ) ? $options['defaultActiveColor'] : '#99d8c9',
									],
									'hover'         => [
										'type'    => $color_field,
										/* translators: color to use to fill the map region when hovering it */
										'title'   => __( 'Hover Color', 'interactive-geo-maps' ),
										'default' => isset ( $options['defaultHoverColor'] ) ? $options['defaultHoverColor'] : '#2ca25f',
									],
									'inactiveColor' => [
										'type'    => $color_field,
										/* translators: color to use in regions that have no data */
										'title'   => __( 'Empty Color', 'interactive-geo-maps' ),
										'default' => '#e0e0e0',
										'desc'    => __( 'For dataless and inactive regions', 'interactive-geo-maps' ),
									],
									'action'        => [
										'type'    => 'select',
										/* translators: action that gets triggered on click */
										'title'   => __( 'Click Action', 'interactive-geo-maps' ),
										'desc'    => '<a href="https://interactivegeomaps.com/docs/click-actions/" target="_blank">' . __( 'More information about click actions', 'interactive-geo-maps' ) . '</a> <span class="dashicons dashicons-external"></span>',
										'options' => $actions_default,
										'default' => 'none',
									],
									'customAction'  => [
										'type'       => 'code_editor',
										'title'      => __( 'Custom Javascript Action', 'interactive-geo-maps' ),
										'desc'       => __( 'When a region is clicked a function will be called where the object with the data from the clicked region will be passed. You can write the contents of the function here.', 'interactive-geo-maps' ),
										'dependency' => [ 'action', '==', 'customRegionAction' ],
									],
								],
							],

							'onlyIncludeActive' => [
								'type'    => 'switcher',
								'title'   => __( 'Only include active regions', 'interactive-geo-maps' ),
								'desc'    => __( 'Only regions with data will display in the map.', 'interactive-geo-maps' ),
								'default' => false,
							],

							'include'           => [
								'type'       => 'text',
								'title'      => __( 'Include Regions', 'interactive-geo-maps' ),
								'desc'       => __( 'Only include regions specified here. Separate each region code with a comma.', 'interactive-geo-maps' ),
								'default'    => '',
								'dependency' => [ 'onlyIncludeActive', '==', false ],
							],
							'exclude'           => [
								'type'       => 'text',
								'title'      => __( 'Exclude regions', 'interactive-geo-maps' ),
								'desc'       => __( 'Exclude regions specified here. Separate each region code with a comma.<br>For example <code>AQ</code> will exclude Antarctica.', 'interactive-geo-maps' ),
								'default'    => 'AQ',
								'dependency' => [ 'onlyIncludeActive', '==', false ],
							],
						],
					],
					'roundMarkers' => [
						'title'  => __( 'Round Markers', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-circle-o fa-lg',
						'class'  => 'markers_tab',
						'fields' => [
							'roundMarkers_info' => [
								'type'    => 'content',
								'content' => __( 'Add round coloured markers to the map.<br>Click "Add New Marker" below to start adding markers.', 'interactive-geo-maps' )
								. '<br> ' .
								/* translators: %s will be a link to an external website */
								sprintf( __( 'You can get the latitude and longitude for a specific location clicking it on the map or from sites like %s.', 'interactive-geo-maps' ), '<a href="https://www.latlong.net/" target="_blank">LatLong.net</a>' )
								. '<br> ' .
								__( 'Correct format: Latitude: 41.1579, Longitude: -8.6291<br> Wrong format: Latitude: <strike>41.1579° N</strike> Longitude: <strike>8.6291° W</strike>', 'interactive-geo-maps' ),
							],
							'roundMarkers'      => [
								'type'         => 'group',
								'button_title' => __( 'Add New Marker', 'interactive-geo-maps' ),
								'fields'       => [
									'id'             => [
										'type'       => 'text',
										'title'      => __( 'Title', 'interactive-geo-maps' ),
										'attributes' => [
											'class' => 'skip-preview',
										],
									],
									'coordinates'    => [
										'type'   => 'fieldset',
										'title'  => __( 'Coordinates', 'interactive-geo-maps' ),
										'fields' => [
											'name'      => [
												'type'  => 'text',
												'title' => __( 'Location', 'interactive-geo-maps' ),
												'class' => 'geocoding geocoding-hide',
												'attributes' => [
													'class' => 'geocoding-input',
												],
											],
											'latitude'  => [
												'type'     => 'text',
												'title'    => __( 'Latitude', 'interactive-geo-maps' ),
												'validate' => 'csf_validate_numeric',

											],
											'longitude' => [
												'type'     => 'text',
												'title'    => __( 'Longitude', 'interactive-geo-maps' ),
												'validate' => 'csf_validate_numeric',

											],
										],
									],

									'tooltipContent' => [
										'type'  => $tooltip_editor,
										'title' => __( 'Tooltip Content', 'interactive-geo-maps' ),
									],
									'content'        => [
										'type'     => $action_content_editor,
										'title'    => __( 'Action Content', 'interactive-geo-maps' ),
										'subtitle' => __( 'URL or content to trigger when marker is clicked.', 'interactive-geo-maps' ) . '<br><span class="igm_select_marker_click_reminder">' . __( 'Don\'t forget to select a click action below.', 'interactive-geo-maps' ) . '</span>',
									],

								],
							],
							'markerDefaults'    => [
								'type'   => 'fieldset',
								'title'  => __( 'Default values', 'interactive-geo-maps' ),
								'desc'   => __( 'Do you want to colour or change the size of each marker individually? <a href="https://interactivegeomaps.com/pricing">Go Pro</a>', 'interactive-geo-maps' ) . '<span class="dashicons dashicons-external"></span>',
								'fields' => [
									'radius' => [
										'type'    => 'spinner',
										'default' => 10,
										'title'   => __( 'Radius', 'interactive-geo-maps' ),
									],
									'fill'   => [
										'type'    => $color_field,
										'title'   => __( 'Fill Color', 'interactive-geo-maps' ),
										'default' => isset ( $options['defaultActiveColor'] ) ? $options['defaultActiveColor'] : '#99d8c9',
									],
									'hover'  => [
										'type'    => $color_field,
										'title'   => __( 'Hover Color', 'interactive-geo-maps' ),
										'default' => isset ( $options['defaultHoverColor'] ) ? $options['defaultHoverColor'] : '#2ca25f',
									],
									'action' => [
										'type'    => 'select',
										'title'   => __( 'Click Action', 'interactive-geo-maps' ),
										'desc'    => '<a href="https://interactivegeomaps.com/docs/click-actions/" target="_blank">' . __( 'More information about click actions', 'interactive-geo-maps' ) . '</a> <span class="dashicons dashicons-external"></span>',
										'options' => $actions_default,
										'default' => 'none',
									],
								],
							],
						],
					],
					'goPro'        => [
						'title'  => __( 'Premium Features', 'interactive-geo-maps' ),
						'icon'   => 'fa fa-unlock-alt fa-lg',
						'fields' => [
							'premium_info' => [
								'type'    => 'content',
								'content' => sprintf(
									// translators: %1s is just an icon
									__( 'Start a <a href="https://interactivegeomaps.com/pricing" target="_blank">Free 7 Days Trial</a> %1s today and get access to the following features:', 'interactive-geo-maps' ),
									'<span class="dashicons dashicons-external"></span>'
								) .
								sprintf(
									'<div class="igm_pro_image_features">

								<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/auto_labels_map_wordpress-600x300.png"></div>
										<div>' . __( 'Labels, vector icon markers and lines', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/custom_marker_images_map_us_world_wordpress-600x300.png"></div>
										<div>' . __( 'Custom marker images', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
									<div><img src="%1$sassets/imgs/germany_map_wordpress-600x300.png"></div>
									<div>' . __( 'Colour each region/country differently', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/live_filter_map_wordpress-600x300.png"></div>
										<div>' . __( 'Live filter menu, to show/hide different markers, from different maps', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/world_map_us_divided_states-600x300.png"></div>
										<div>' . __( 'Overlay different maps', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/map_heatmap_wordpress_usmap-600x300.png"></div>
										<div>' . __( 'Create heatmaps, using external data', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/asia-australia-centered-map-wordpress-600x300.png"></div>
										<div>' . __( 'Change center point and initial zoom of a map', 'interactive-geo-maps' ) . '</div>
									</div>



									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/cluster_markers_wordpress_map.png"></div>
										<div>' . __( 'Cluster Markers', 'interactive-geo-maps' ) . '</div>
									</div>




									<div class="igm_pro_image_feature">
										<div><img src="%1$sassets/imgs/interactive_globe_map_wordpress-600x300.png"></div>
										<div>' . __( 'Globe map projection', 'interactive-geo-maps' ) . '</div>
									</div>

									<div class="igm_pro_image_feature">
									<div><img src="%1$sassets/imgs/external_dropdown-600x300.png"></div>
									<div>' . __( 'External search dropdown', 'interactive-geo-maps' ) . '</div>
									</div>

									</div>
								',
									$plugins_url
								)
								. '<br>

								<div class="igm_go_pro_container"><a class="igm_go_pro" href="https://interactivegeomaps.com/features/" target="_blank">' . __( 'Live Examples', 'interactive-geo-maps' ) . ' <span class="dashicons dashicons-external"></span></a></div>

								<h3>And many other features, including:</h3>
								<ul id="igm_pro_feature_list">
									<li>' . __( 'Color each marker and region individually', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Control the size, colour and action of each marker', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Populate map automatically from tags or categories', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Customize tooltip appearance', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Enable advanced zoom options', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Drilldown Maps', 'interactive-geo-maps' ) . '</li>
									<li>' . __( 'Advanced click actions:', 'interactive-geo-maps' ) . '
										<ul>
											<li>' . __( 'Open content in a lightbox', 'interactive-geo-maps' ) . '</li>
											<li>' . __( 'Display content next to or below the map', 'interactive-geo-maps' ) . '</li>
											<li>' . __( 'Trigger custom javascript code', 'interactive-geo-maps' ) . '</li>
										</ul>
									</li>
								</ul>
								<div class="igm_go_pro_title">' . __( 'From $2.49/month<br> Lifetime License Available<br>Multi-license deals', 'interactive-geo-maps' ) . '</div>

								<div class="igm_go_pro_container"><a class="igm_go_pro" href="https://interactivegeomaps.com/pricing/" target="_blank">' . __( 'More Information', 'interactive-geo-maps' ) . ' <span class="dashicons dashicons-external"></span></a></div>
								',
							],
						],
					],
				],
			],

			'min_req'          => [
				'title'     => __( 'Min Requirements', 'interactive-geo-maps' ),
				'data_type' => 'serialize',
				'priority'  => 'low',
				'fields'    => [
					'shortcodeInfo' => [
						'type'    => 'content',
						'content' => '<div id="map_min_req"></div><a href="https://interactivegeomaps.com/docs/minimum-requirements-panel/" target="_blank">' . __( 'What is this?', 'interactive-geo-maps' ) . '</a>',
					],
				],
			],
		],
	];

	$model = apply_filters( 'igm_model', $model );

	// if we're not in the edit map screen, remove the map preview meta
	global $pagenow;
	if ( $pagenow !== 'post.php' ) {
		unset( $model['meta']['shortcode'] );
	} else {

		if ( isset( $_GET['post'] ) && ! is_array( $_GET['post'] ) ) {
			global $post;
			$model['meta']['shortcode']['fields']['shortcode']['content'] = sprintf( '<textarea style="width:100%%;" readonly>[display-map id=\'%s\']</textarea>', sanitize_key( esc_attr( $_GET['post'] ) ) );
		}
	}

	// if we disable the preview
	if ( isset( $options['saveImage'] ) && $options['saveImage'] === "0" ){
		unset( $model['meta']['map_image']);
		unset( $model['features']['admin_cols']['image']);
	}

	return $model;
}

return igmaps_model();
