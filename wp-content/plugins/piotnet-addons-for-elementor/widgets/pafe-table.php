<?php

class PAFE_Table extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-table';
	}

	public function get_title() {
		return __( 'PAFE Table', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'pafe-free-widgets' ];
	}

	public function get_keywords() {
		return [ 'table' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-widget-free'
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-style-free'
		];
	}
    public function includes() {
        require_once(__DIR__ . '/compatibility/wpml/pafe-table-wpml.php');
    }
	

/** Insert Content Section**/
	protected function _register_controls() {
		
		$this->start_controls_section(
			'pafe_table_layout_section',
			[
				'label' => __( 'Layout', 'pafe' ),
			]
		);

			$this->add_control(
				'pafe_table_layout_type', 
				[
					'label' => __( 'Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'layout1' => __('Layout 1', 'pafe'),
						'layout2' => __('Layout 2', 'pafe'),
					],
					'default' => 'layout1',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'pafe_table_head_section',
				[
					'label' => __( 'Head', 'pafe' ),
				]
			);

			$repeater = new \Elementor\Repeater();
            $repeater->add_responsive_control(
                'pafe_table_head_width',
                [
                    'label' => __( 'Width', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 50,
                            'max' => 200,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 100,
                    ],
                ]
            );

            $repeater->add_control(
				'pafe_table_head_item_title', 
				[
					'label' => __( 'Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => 'Head Title',
				]
			);

			$repeater->add_responsive_control(
				'pafe_table_head_title_alignment',
				[
					'label' => __( 'Alignment', 'pafe' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'pafe' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'pafe' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'pafe' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => 'center',
					'toggle' => true,
				]
			);

			$repeater->add_control(
				'pafe_table_head_colspan',
				[
					'label' => __( 'Column Span', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '1',
				]
			);

			$this->add_control(
				'pafe_table_head',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'title_field' => __('{{{pafe_table_head_item_title}}}'),
					// 'condition' => [
					// 	'pafe_table_layout_type' => 'layout1',
					// 	'pafe_table_layout_type' => 'layout2',
					// ],				
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_table_body_section',
			[
				'label' => __( 'Body', 'pafe' ),
			]
		);

			$repeater_body = new \Elementor\Repeater();
			$repeater_body->add_control(
				'pafe_table_body_row_type', 
				[
					'label' => __( 'Type', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'row' => __('Row', 'pafe'),
						'col' => __('Column', 'pafe'),
					],
					'default' => 'row',
				]
			);

			$repeater_body->add_control(
				'pafe_table_body_item_title', 
				[
					'label' => __( 'Text', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => 'Content',
					'condition' => [
						'pafe_table_body_row_type' => 'col',
					],
				]
			);

			$repeater_body->add_control(
				'pafe_table_body_item_title_url',
				[
					'label' => __( 'Text URL', 'pafe' ),
					'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => esc_html__( 'https://your-link.com', 'pafe' ),
                    'options' => [ 'url', 'is_external', 'nofollow' ],
                    'default' => [
                        'url' => '',
                        'is_external' => true,
                        'nofollow' => true,
                        // 'custom_attributes' => '',
                    ],
                    'label_block' => true,
					'condition' => [
						'pafe_table_body_row_type' => 'col',
					],
				]
			);

			$repeater_body->add_control(
				'show_body_item_icon', 
				[
					'label' => __( 'Enable Icon', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'pafe' ),
					'label_off' => __( 'No', 'pafe' ),
					'return_value' => 'yes',
					'default' => 'no',
					'condition' => [
						'pafe_table_body_row_type' => 'col',
					],
				]
			);

			$repeater_body->add_control(
				'pafe_table_body_item_icon',
				[
					'label' => __( 'Icon', 'pafe' ),
					'type' => \Elementor\Controls_Manager::ICON,
					'condition' => [
						'show_body_item_icon' => 'yes',
					],
				]
			);

			$repeater_body->add_control(
				'pafe_table_body_item_icon_position',
				[
					'label' => __( 'Icon Position', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'before' => __( 'Before', 'pafe' ),
						'after' => __( 'After', 'pafe' ),
					],
					'default' => 'before',
					'toggle' => true,
					'condition' => [
							'show_body_item_icon' => 'yes',
						],
				]
			);

            $repeater_body->add_control(
                'pafe_table_body_item_icon_before_spacing',
                [
                    'label' => __( 'Icon Spacing', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 2,
                            'max' => 10,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 5,
                    ],
                    'condition' => [
                        'show_body_item_icon' => 'yes',
                        'pafe_table_body_item_icon_position' => 'before',
                    ],
                ]
            );

            $repeater_body->add_control(
                'pafe_table_body_item_icon_after_spacing',
                [
                    'label' => __( 'Icon Spacing', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 2,
                            'max' => 10,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 5,
                    ],
                    'condition' => [
                        'show_body_item_icon' => 'yes',
                        'pafe_table_body_item_icon_position' => 'after',
                    ],
                ]
            );

            $repeater_body->add_control(
                'pafe_table_body_item_rowspan',
                [
                    'label' => __( 'Row Span', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => '1',
                    'condition' => [
                        'pafe_table_body_row_type' => 'col',
                    ],
                ]
            );
			
			$repeater_body->add_control(
				'pafe_table_body_item_colspan',
				[
					'label' => __( 'Column Span', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '1',
					'condition' => [
						'pafe_table_body_row_type' => 'col',
						// 'terms' => [
						// 	[
						// 		'name' => 'pafe_table_layout_type', 
						// 		'operator' => '==',
						// 		'value' => 'layout2',
						// 	],
						// 	[
						// 		'name' => 'pafe_table_body_row_type', 
						// 		'operator' => '==',
						// 		'value' => 'col',
						// 	],
						// ],
					],
				]
			);

			$repeater_body->add_responsive_control(
				'pafe_table_body_title_alignment',
				[
					'label' => __( 'Alignment', 'pafe' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'pafe' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'pafe' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'pafe' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => 'center',
					'toggle' => true,
					'condition' => [
							'pafe_table_body_row_type' => 'col',
						],
				]
			);

			$this->add_control(
				'pafe_table_body',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater_body->get_controls(),
					'title_field' => __('{{{pafe_table_body_row_type}}}: {{{pafe_table_body_item_title}}}'),
				]
			);

		$this->end_controls_section();

/* Edit Content Style Section*/
		
		$this->start_controls_section(
			'pafe_table_style_section',
			[
				'label' => __( 'Table Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'pafe_table_width',
				[
					'label' => __( 'Width', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 500,
							'max' => 1000,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors' => [
						'{{WRAPPER}} .pafe-table' => 'width: {{SIZE}}{{UNIT}}',
					],
				
				]
			);

			$this->add_responsive_control(
				'pafe_table_alignment',
				[
					'label' => __( 'Alignment', 'pafe' ),
					'type' => \Elementor\Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'pafe' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'pafe' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'pafe' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => 'center',
					'toggle' => true,
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_table_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_table_head_style_section',
			[
				'label' => __( 'Head Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'pafe_table_head_typography',
					'selector' => '{{WRAPPER}} .pafe-table-head-text',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                    ]
				]
			);

			$this->add_control(
				'pafe_table_head_color',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#27140d',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-head-text' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pafe_table_head_background_color',
				[
					'label' => __( 'Backround Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#9391e0',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-head-cell' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'pafe_table_head_text_shadow',
					'label' => __( 'Text Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-head-text',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_table_head_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-head-cell',
				]
			);

			$this->add_responsive_control(
				'pafe_table_head_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-table-head-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_table_body_firts_cell_style_section',
			[
				'label' => __( 'First Column Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_table_layout_type' => 'layout1',
				],
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'pafe_table_body_first_cell_typography',
					'selector' => '{{WRAPPER}} .pafe-table-body-first-text',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                    ]
				]
			);

			$this->add_control(
				'pafe_table_body_first_cell_color',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#27140d',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-first-text' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pafe_table_body_first_cell_icon_color',
				[
					'label' => __( 'Icon Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#FFFFFF',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-item-icon' => 'color: {{VALUE}}',
					],
				]
			);

            $this->add_control(
                'pafe_table_body_first_cell_icon_size',
                [
                    'label' => __( 'Icon Size', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 40,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .pafe-table-body-first-cell .pafe-table-body-item-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

			$this->add_control(
				'pafe_table_body_first_cell_background_color',
				[
					'label' => __( 'Backround Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#9391e0',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-first-cell' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'pafe_table_body_first_cell_text_shadow',
					'label' => __( 'Text Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-body-first-text',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_table_body_first_cell_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-body-first-cell',
				]
			);

			$this->add_responsive_control(
				'pafe_table_body_first_cell_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-first-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_table_body_cell_style_section',
			[
				'label' => __( 'Column Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_table_layout_type' => 'layout1',
				],
			]
		);

			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'pafe_table_body_cell_typography',
					'selector' => '{{WRAPPER}} .pafe-table-body-text',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                    ]
				]
			);

			$this->add_control(
				'pafe_table_body_cell_color',
				[
					'label' => __( 'Text Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#27140d',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-text' => 'color: {{VALUE}}',
					],
				]
			);

            $this->add_control(
                'pafe_table_body_cell_icon_color',
                [
                    'label' => __( 'Icon Color', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#FFFFFF',
                    'selectors' => [
                        '{{WRAPPER}} .pafe-table-body-item-icon' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'pafe_table_body_cell_icon_size',
                [
                    'label' => __( 'Icon Size', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px' ],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 40,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .pafe-table-body-cell .pafe-table-body-item-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

			$this->add_control(
				'pafe_table_body_cell_background_color',
				[
					'label' => __( 'Backround Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'default' => '#dadbe5',
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-cell' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'pafe_table_body_cell_text_shadow',
					'label' => __( 'Text Shadow', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-body-text',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'pafe_table_body_cell_border',
					'label' => __( 'Border', 'pafe' ),
					'selector' => '{{WRAPPER}} .pafe-table-body-cell',
				]
			);

			$this->add_responsive_control(
				'pafe_table_body_cell_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-table-body-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_table_body_row_style_section',
			[
				'label' => __( 'Row Style', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'pafe_table_layout_type' => 'layout2',
				],
			]
		);

			$this->start_controls_tabs(
				'pafe_table_body_tabs'
			);
				$this->start_controls_tab( 
					'pafe_table_body_row_odd_tab',
					[
						'label' => __( 'ODD', 'pafe' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'pafe_table_body_row_odd_typography',
							'selector' => '{{WRAPPER}} .pafe-table-body tr:nth-of-type(odd)',
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                            ]
						]
					);

					$this->add_control(
						'pafe_table_body_row_odd_color',
						[
							'label' => __( 'Text Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#27140d',
							'selectors' => [
								'{{WRAPPER}} .pafe-table-body tr:nth-of-type(odd) .pafe-table-body-text' => 'color: {{VALUE}}',
							],
						]
					);

                    $this->add_control(
                        'pafe_table_body_row_odd_icon_color',
                        [
                            'label' => __( 'Icon Color', 'pafe' ),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '#FFFFFF',
                            'selectors' => [
                                '{{WRAPPER}} .pafe-table-body-row:nth-of-type(odd) .pafe-table-body-item-icon' => 'color: {{VALUE}}',
                            ],
                        ]
                    );

                    $this->add_control(
                        'pafe_table_body_row_odd_icon_size',
                        [
                            'label' => __( 'Icon Size', 'pafe' ),
                            'type' => \Elementor\Controls_Manager::SLIDER,
                            'size_units' => [ 'px' ],
                            'range' => [
                                'px' => [
                                    'min' => 10,
                                    'max' => 40,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 20,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .pafe-table-body-row:nth-of-type(odd) .pafe-table-body-item-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                            ],
                        ]
                    );

					$this->add_control(
						'pafe_table_body_row_odd_background_color',
						[
							'label' => __( 'Backround Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#dadbe5',
							'selectors' => [
								'{{WRAPPER}} .pafe-table-body tr:nth-of-type(odd)' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Text_Shadow::get_type(),
						[
							'name' => 'pafe_table_body_row_odd_text_shadow',
							'label' => __( 'Text Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-table-body tr:nth-of-type(odd)',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'pafe_table_body_row_odd_border',
							'label' => __( 'Border', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-table-body-cell',
						]
					);

					$this->add_responsive_control(
							'pafe_table_body_row_odd_padding',
							[
								'label' => __( 'Padding', 'pafe' ),
								'type' => \Elementor\Controls_Manager::DIMENSIONS,
								'size_units' => [ 'px', 'em', '%' ],
								'selectors' => [
									'{{WRAPPER}} .pafe-table-body-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
							]
						);
				$this->end_controls_tab();

				$this->start_controls_tab( 
					'pafe_table_body_row_even_tab',
					[
						'label' => __( 'EVEN', 'pafe' ),
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' => 'pafe_table_body_row_even_typography',
							'selector' => '{{WRAPPER}} .pafe-table-body tr:nth-of-type(even)',
							'global' => [
                                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                            ]
						]
					);

					$this->add_control(
						'pafe_table_body_row_even_color',
						[
							'label' => __( 'Text Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#27140d',
							'selectors' => [
								'{{WRAPPER}} .pafe-table-body tr:nth-of-type(even) .pafe-table-body-text' => 'color: {{VALUE}}',
							],
						]
					);

                $this->add_control(
                    'pafe_table_body_row_even_icon_color',
                    [
                        'label' => __( 'Icon Color', 'pafe' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '#FFFFFF',
                        'selectors' => [
                            '{{WRAPPER}} .pafe-table-body-row:nth-of-type(even) .pafe-table-body-item-icon' => 'color: {{VALUE}}',
                        ],
                    ]
                );

                $this->add_control(
                    'pafe_table_body_row_even_icon_size',
                    [
                        'label' => __( 'Icon Size', 'pafe' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 10,
                                'max' => 40,
                            ],
                        ],
                        'default' => [
                            'unit' => 'px',
                            'size' => 20,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .pafe-table-body-row:nth-of-type(even) .pafe-table-body-item-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                        ],
                    ]
                );

                $this->add_control(
						'pafe_table_body_row_even_background_color',
						[
							'label' => __( 'Backround Color', 'pafe' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '#dadbe5',
							'selectors' => [
								'{{WRAPPER}} .pafe-table-body tr:nth-of-type(even)' => 'background-color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Text_Shadow::get_type(),
						[
							'name' => 'pafe_table_body_row_even_text_shadow',
							'label' => __( 'Text Shadow', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-table-body tr:nth-of-type(even)',
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Border::get_type(),
						[
							'name' => 'pafe_table_body_row_even_border',
							'label' => __( 'Border', 'pafe' ),
							'selector' => '{{WRAPPER}} .pafe-table-body-cell',
						]
					);

					$this->add_responsive_control(
							'pafe_table_body_row_even_padding',
							[
								'label' => __( 'Padding', 'pafe' ),
								'type' => \Elementor\Controls_Manager::DIMENSIONS,
								'size_units' => [ 'px', 'em', '%' ],
								'selectors' => [
									'{{WRAPPER}} .pafe-table-body-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
							]
						);
				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="pafe-table-container" >
			<div class="pafe-table-inner<?php if ($settings['pafe_table_alignment']=="left") {echo ' table_left';} if ($settings['pafe_table_alignment']=="center") {echo ' table_center';} if ($settings['pafe_table_alignment']=="right") {echo ' table_right';} ?>">
				<table class="pafe-table">
					<?php if ($settings['pafe_table_layout_type'] == 'layout1'): ?>
						<thead class="pafe-table-head">
							<tr class="pafe-table-head-row">
								<?php
									$index = 0;
									foreach ($settings['pafe_table_head'] as $item) :
										$index ++;
								?>	
									<th class="pafe-table-head-cell<?php if ($item['pafe_table_head_title_alignment']=="left") {echo ' left';} if ($item['pafe_table_head_title_alignment']=="center") {echo ' center';} if ($item['pafe_table_head_title_alignment']=="right") {echo ' right';} ?>" colspan="<?php echo $item['pafe_table_head_colspan']; ?>" style="width: <?php echo $item['pafe_table_head_width']['size'].$item['pafe_table_head_width']['unit'].';'; ?>">
										<span class="pafe-table-head-content">
											<span class="pafe-table-head-text"><?php echo $item['pafe_table_head_item_title'];?></span>
										<!-- 	<i class="pafe-table-head-item-icon pafe-table-head-item-icon-sort fa fa-sort"aria-hidden="true" ></i>
											<i class="pafe-table-head-item-icon pafe-table-head-item-icon-down fa fa-sort-desc"aria-hidden="true" ></i>
											<i class="pafe-table-head-item-icon pafe-table-head-item-icon-up fa fa-sort-asc"aria-hidden="true" ></i> -->
										</span>
									</th>
								<?php endforeach; ?>
							</tr>	
						</thead>
						<tbody class="pafe-table-body">
							<?php
								$index = 0;
								$row = 0;
								$count_row = 0;
								foreach ($settings['pafe_table_body'] as $item):
                                    $target = $item['pafe_table_body_item_title_url']['is_external'] ? ' target="_blank"' : '';
                                    $nofollow = $item['pafe_table_body_item_title_url']['nofollow'] ? ' rel="nofollow"' : '';
									$index++;
									if ($item['pafe_table_body_row_type']=='row'):?>
										<?php if ($index==1): ?> <tr class="pafe-table-body-row"><?php $i=0; $count_row++;?>
											<?php else: ?> </tr> <tr class="pafe-table-body-row"><?php $i=0; $count_row++;?>
										<?php endif; ?>
									<?php elseif ($item['pafe_table_body_row_type']=='col'): ?>
										<?php if ($count_row > $row): ?>
											<?php $i++; ?>
											<?php $count_row=1; ?>
											<?php if ($i==1): ?>
												<?php $row = $item['pafe_table_body_item_rowspan'];?>
												<th class="pafe-table-body-first-cell<?php if ($item['pafe_table_body_title_alignment']=="left") {echo ' left';} if ($item['pafe_table_body_title_alignment']=="center") {echo ' center';} if ($item['pafe_table_body_title_alignment']=="right") {echo ' right';} ?>" rowspan="<?php echo $item['pafe_table_body_item_rowspan']; ?>">
													<span class="pafe-table-body-first-content">
														<?php if ($item['show_body_item_icon']=='yes'):?>
															<?php if ($item['pafe_table_body_item_icon_position']=='before'):?>
																<?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-right: '.$item['pafe_table_body_item_icon_before_spacing']['size'].$item['pafe_table_body_item_icon_before_spacing']['unit'].';"></i>'; ?>
															<?php endif; ?>
														<?php endif; ?>
														<span class="pafe-table-body-first-text"><?php echo $item['pafe_table_body_item_title'];?></span>
														<?php if ($item['show_body_item_icon']=='yes'):?>
															<?php if ($item['pafe_table_body_item_icon_position']=='after'):?>
																<?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-left: '.$item['pafe_table_body_item_icon_after_spacing']['size'].$item['pafe_table_body_item_icon_after_spacing']['unit'].';"></i>'; ?>
															<?php endif; ?>
														<?php endif; ?>
													</span>
												</th>
											<?php endif; ?>
										<?php else: ?>
											<td class="pafe-table-body-cell<?php if ($item['pafe_table_body_title_alignment']=="left") {echo ' left';} if ($item['pafe_table_body_title_alignment']=="center") {echo ' center';} if ($item['pafe_table_body_title_alignment']=="right") {echo ' right';} ?>">
												<span class="pafe-table-body-content">
                                                    <?php if ($item['show_body_item_icon']=='yes'):?>
                                                        <?php if ($item['pafe_table_body_item_icon_position']=='before'):?>
                                                            <?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-right: '.$item['pafe_table_body_item_icon_before_spacing']['size'].$item['pafe_table_body_item_icon_before_spacing']['unit'].';"></i>'; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
													<a class="pafe-table-body-text" <?php if (!empty($item['pafe_table_body_item_title_url']['url'])) { echo 'href="' . $item['pafe_table_body_item_title_url']['url'].'"' . $target . $nofollow ;} ?>><?php echo $item['pafe_table_body_item_title'];?></a>
                                                    <?php if ($item['show_body_item_icon']=='yes'):?>
                                                        <?php if ($item['pafe_table_body_item_icon_position']=='after'):?>
                                                            <?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-left: '.$item['pafe_table_body_item_icon_after_spacing']['size'].$item['pafe_table_body_item_icon_after_spacing']['unit'].';"></i>'; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
												</span>
											</td>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?>
								</tr>
						</tbody>
					<?php elseif ($settings['pafe_table_layout_type'] == 'layout2'): ?>
						<thead class="pafe-table-head">
							<tr class="pafe-table-head-row">
								<?php
									$index = 0;
									foreach ($settings['pafe_table_head'] as $item) :
										$index ++;	
									
								?>	
									<th class="pafe-table-head-cell<?php if ($item['pafe_table_head_title_alignment']=="left") {echo ' left';} if ($item['pafe_table_head_title_alignment']=="center") {echo ' center';} if ($item['pafe_table_head_title_alignment']=="right") {echo ' right';} ?>" colspan="<?php echo $item['pafe_table_head_colspan']; ?>" style="width: <?php echo $item['pafe_table_head_width']['size'].$item['pafe_table_head_width']['unit'].';'; ?>">
										<span class="pafe-table-head-content">
											<span class="pafe-table-head-text"><?php echo $item['pafe_table_head_item_title'];?></span>
										<!-- 	<i class="pafe-table-head-item-icon pafe-table-head-item-icon-sort fa fa-sort"aria-hidden="true" ></i>
											<i class="pafe-table-head-item-icon pafe-table-head-item-icon-down fa fa-sort-desc"aria-hidden="true" ></i>
											<i class="pafe-table-head-item-icon pafe-table-head-item-icon-up fa fa-sort-asc"aria-hidden="true" ></i> -->
										</span>
									</th>
								<?php endforeach; ?>
							</tr>	
						</thead>
						<tbody class="pafe-table-body">
							<?php
								$index = 0;
								foreach ($settings['pafe_table_body'] as $item):
                                    $target = $item['pafe_table_body_item_title_url']['is_external'] ? ' target="_blank"' : '';
                                    $nofollow = $item['pafe_table_body_item_title_url']['nofollow'] ? ' rel="nofollow"' : '';
									$index++;
									if ($item['pafe_table_body_row_type']=='row'):?>
										<?php if ($index==1): ?> <tr class="pafe-table-body-row"><?php $i=0;?>
											<?php else: ?> </tr> <tr class="pafe-table-body-row"><?php $i=0;?>
										<?php endif; ?>
										<?php elseif ($item['pafe_table_body_row_type']=='col'): ?>
											<td class="pafe-table-body-cell<?php if ($item['pafe_table_body_title_alignment']=="left") {echo ' left';} if ($item['pafe_table_body_title_alignment']=="center") {echo ' center';} if ($item['pafe_table_body_title_alignment']=="right") {echo ' right';} ?>" rowspan="<?php echo $item['pafe_table_body_item_rowspan']; ?>" colspan="<?php echo $item['pafe_table_body_item_colspan']; ?>">
                                                <?php if ($item['show_body_item_icon']=='yes'):?>
                                                    <?php if ($item['pafe_table_body_item_icon_position']=='before'):?>
                                                        <?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-right: '.$item['pafe_table_body_item_icon_before_spacing']['size'].$item['pafe_table_body_item_icon_before_spacing']['unit'].';"></i>'; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <span class="pafe-table-body-content">
													<a class="pafe-table-body-text" <?php if (!empty($item['pafe_table_body_item_title_url']['url'])) { echo 'href="' . $item['pafe_table_body_item_title_url']['url'].'"' . $target . $nofollow ;} ?>><?php echo $item['pafe_table_body_item_title'];?>
                                                    </a>
												</span>
                                                <?php if ($item['show_body_item_icon']=='yes'):?>
                                                    <?php if ($item['pafe_table_body_item_icon_position']=='after'):?>
                                                        <?php echo '<i class="pafe-table-body-item-icon '. $item['pafe_table_body_item_icon'].'"aria-hidden="true" style="padding-left: '.$item['pafe_table_body_item_icon_after_spacing']['size'].$item['pafe_table_body_item_icon_after_spacing']['unit'].';"></i>'; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
											</td>
									<?php endif; ?>
								<?php endforeach; ?>
								</tr>
						</tbody>
					<?php endif; ?>
				</table>
			</div>
		</div>
		<?php

	}

    public static function check_plugin_active( $slug = '' ) {

        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        $wpml = in_array( 'sitepress-multilingual-cms/sitepress.php', (array) get_option( 'active_plugins', array() ), true );
        $wpml_trans = in_array( 'wpml-string-translation/plugin.php', (array) get_option( 'active_plugins', array() ), true );

        return $wpml && $wpml_trans;
    }

    public function add_wpml_support() {
        if ( ! self::check_plugin_active() ) {
            return;
        }
        $this->includes();
        add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
    }
    public function wpml_widgets_to_translate_filter( $widgets ) {

        $widgets[ $this->get_name() ] = [
            'conditions' => [ 'widgetType' => $this->get_name() ],
            'fields'            => array(),
            'integration-class' => 'PAFE\widgets\compatibility\wpml\Table',
        ];

        return $widgets;
    }
}
