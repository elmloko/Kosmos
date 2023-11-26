<?php
	class PAFE_Video_Playlist extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-video-playlist'; 
		}
		public function get_title() {
			return __( 'PAFE Video Playlist', 'pafe' );
		}
		public function get_icon() {
			return 'eicon-video-playlist';
		}
		public function get_youtube_title($ref) {
	        $json = file_get_contents('https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $ref . '&format=json');
	        $details = json_decode($json, true); //parse the JSON into an array
	        return $details['title']; //return the video title
		}

        public function includes() {
            require_once(__DIR__ . '/compatibility/wpml/pafe-video-list-wpml.php');
        }
		public function curl_get($url) {
		    $curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		    $return = curl_exec($curl);
		    curl_close($curl);
		    return $return;
		}
		public function vimeo_object($video_url) {
			$oembed_endpoint = 'http://vimeo.com/api/oembed';
			// Create the URLs
			$xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($video_url) . '&width=640';
			// Load in the oEmbed XML
			$oembed = simplexml_load_string($this->curl_get($xml_url));
			return $oembed;
		}
		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}
		public function get_keywords() {
			return [ 'video', 'playlist' ];
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
		protected function _register_controls() {
			$this->start_controls_section(
				'pafe_video_playlist_title_section',
				[
					'label' => __( 'Video Playlist Title', 'pafe' ),
				]
			);	
			$this->add_control( 
				'pafe_video_playlist_title',
				[
					'label' => __( 'Video List Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => 'Video List',
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$this->add_control(
				'pafe_video_playlist_autoplay',
				[
					'label' => __( 'Autoplay', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes', 
				] 
			);
			$this->end_controls_section();	
			$this->start_controls_section(
				'pafe_video_playlist_section',
				[
					'label' => __( 'Video Playlist', 'pafe' ),
				]
			);
			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'pafe_video_playlist_item_title',
				[
					'label' => __( 'Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$repeater->add_control(
				'pafe_video_playlist_item_link',
				[
					'label' => __( 'Link', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
				]
			); 
			$this->add_control(
			'pafe_video_playlist',
				[
					'type' => \Elementor\Controls_Manager::REPEATER,
					'show_label' => true,
					'fields' => $repeater->get_controls(),
					'title_field' => __( '{{{pafe_video_playlist_item_title}}}' ),
					'description' => __( "Video URL eg: Youtube, Vimeo,...", "pafe" ), 
				]
			); 
			$this->end_controls_section();
			$this->start_controls_section(
			'pafe_video_playlist_title_style_section',
				[
					'label' => __( 'Video Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'label_typography',
					'selector' => '{{WRAPPER}} .pafe-video-playlist__item-title',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                    ]
				]
			);
			$this->add_control(
			'pafe_video_playlist_title_color',
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__item-title' => 'color: {{VALUE}}',
						'{{WRAPPER}} .active::before' => 'color: {{VALUE}}',
					],
				]
			);
			$this->add_responsive_control(
	   			'video_title_align',
	    			[
	  				'label' => __( 'Alignment', 'elementor' ),
	 				'type' => \Elementor\Controls_Manager::CHOOSE,
	   				'options' => [
	  					'left' => [
	 						'title' => __( 'Left', 'elementor' ),
	  					'icon' => 'eicon-text-align-left',
	   					],
	     					'center' => [
	  						'title' => __( 'Center', 'elementor' ),
	   						'icon' => 'eicon-text-align-center',
	    					],
	    					'right' => [
	   						'title' => __( 'Right', 'elementor' ),
	   						'icon' => 'eicon-text-align-right',
	   					],
	   				],
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__list-title' => 'text-align: {{VALUE}};',
	 				],
	   			]
   			);
   			$this->add_responsive_control(
				'pafe_video_playlist_title_padding',
				[
					'label' => __( 'Padding', 'pafe' ),
					'type' => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__list-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				] 
			);
			$this->end_controls_section();
			$this->start_controls_section(
			'pafe_list_title_style_section',
				[
					'label' => __( 'Video List Title', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				[
					'name' => 'title_typography',
					'selector' => '{{WRAPPER}} .pafe-video-playlist__list-title',
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
                    ]
				]
			);
			$this->add_control(
			'pafe_list_title_color', 
				[
					'label' => __( 'Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__list-title' => 'color: {{VALUE}}',
					],
				]
			);
			$this->end_controls_section();
			$this->start_controls_section(
			'pafe_video_playlist_style_section',
				[
					'label' => __( 'Video Playlist Style', 'pafe' ),
					'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
			'pafe_video_playlist_background_color',
				[
					'label' => __( 'Background color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#252D43', 
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__list' => 'background-color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
			'pafe_video_playlist-active_background_color',
				[
					'label' => __( 'Active Color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#42527B', 
					'selectors' => [
						'{{WRAPPER}} .active' => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .pafe-video-playlist__item:hover' => 'background-color: {{VALUE}}',
					],
				]
			);
			$this->add_control(
			'pafe_video_playlist_border_bottom_color',
				[
					'label' => __( 'Divider color', 'pafe' ),
					'type' => \Elementor\Controls_Manager::COLOR,
					'global' => [
                        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                    ],
					'default' => '#fff', 
					'selectors' => [
						'{{WRAPPER}} .pafe-video-playlist__item' => 'border-bottom-color: {{VALUE}}',
					],
				]
			);			
			$this->end_controls_section();	
		}	
		protected function render() { 
			$settings = $this->get_settings_for_display();          	
			?>	
				<div class="pafe-video-playlist" data-pafe-video-playlist>
					<div class="pafe-video-playlist__video" data-pafe-video-playlist-video>
						<?php 
							for ($i = 0; $i < count($settings['pafe_video_playlist']); $i++) : 	
								if ($i == 0) :			
									$video_link = $settings['pafe_video_playlist'][$i]['pafe_video_playlist_item_link'];
									if (!empty($video_link)) :
										if (strpos($video_link, 'vimeo.com') !== false) {
											$video_object = $this->vimeo_object($video_link);
											$video_id = $video_object->video_id;		
											$title = $video_object->title;   
											$auto = '';
											if ($settings['pafe_video_playlist_autoplay'] == 'yes') {
												$auto = 'autoplay;';
											}
											$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
											$thumbnail= $hash[0]['thumbnail_medium'];  
											$iframe = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?title=0&byline=0&portrait=0" frameborder="0" allow="'.$auto.' fullscreen" allowfullscreen></iframe>';
										} else {
											parse_str( parse_url( $video_link, PHP_URL_QUERY ), $my_array_of_vars );
											$video_id = $my_array_of_vars['v'];		
											$auto = '';
											if ($settings['pafe_video_playlist_autoplay'] == 'yes') {
												$auto = 'autoplay;';
											}
											$thumbnail = "http://img.youtube.com/vi/".$video_id."/maxresdefault.jpg";	
											$title = $this->get_youtube_title($video_id);
										 	$iframe = '<iframe src="https://www.youtube.com/embed/'.$video_id.'?&autoplay=1" frameborder="0" allow="accelerometer;'.$auto.' encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
										}
										echo $iframe;			

							endif; endif; endfor; ?>	 
					</div>
					<div class="pafe-video-playlist__list" data-pafe-video-playlist-list>
						<div class="pafe-video-playlist__list-title" data-pafe-video-playlist-list-title> 
						<?php 
							if (!empty($settings['pafe_video_playlist_title']) ) :
						?>		
							<?php echo $settings['pafe_video_playlist_title'];  ?>
						<?php endif; ?>							 
						</div>
						<div class="pafe-video-playlist__list-video" data-pafe-video-playlist-list-video> 
							<?php
								$index_1 = 0;
								foreach ( $settings['pafe_video_playlist'] as $item) :
									$index_1 ++;
									$video_link = $item['pafe_video_playlist_item_link'];
									if (!empty($video_link)) :
										if (strpos($video_link, 'vimeo.com') !== false) {
											$video_object = $this->vimeo_object($video_link);
											$video_id = $video_object->video_id;		
											$title = $video_object->title;   
											$auto = '';
											if ($settings['pafe_video_playlist_autoplay'] == 'yes') {
												$auto = 'autoplay;';
											}
											$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
											$thumbnail= $hash[0]['thumbnail_medium'];  
											$iframe = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?autoplay=1?title=0&byline=0&portrait=0" frameborder="0" allow="'.$auto.' fullscreen" allowfullscreen></iframe>';
										} else {
											parse_str( parse_url( $video_link, PHP_URL_QUERY ), $my_array_of_vars );
											$video_id = $my_array_of_vars['v'];		
											$auto = '';
											if ($settings['pafe_video_playlist_autoplay'] == 'yes') {
												$auto = 'autoplay;';
											}
											$thumbnail = "http://img.youtube.com/vi/".$video_id."/maxresdefault.jpg";	
											$title = $this->get_youtube_title($video_id);
										 	$iframe = '<iframe src="https://www.youtube.com/embed/'.$video_id.'?&autoplay=1" frameborder="0" allow="accelerometer;'.$auto.' encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
										}										
							?> 
										<div class="pafe-video-playlist__item<?php if($index_1 == 1){ echo " active"; } ?>" data-pafe-video-playlist-item='<?php echo $iframe; ?>'>
											<div class="pafe-video-playlist__item-title">
												<?php if (!empty($item['pafe_video_playlist_item_title'])) {
													echo $item['pafe_video_playlist_item_title'];
												} else { echo $title; } ?>	
											</div>
											<img src="<?php echo $thumbnail; ?>" class="pafe-video-playlist__item-thumbnail" alt="">
										</div> 
							<?php
								endif; endforeach; 
							?>
						</div>
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
                'fields'            => array(
                    array(
                        'field'       => 'pafe_video_playlist_title',
                        'type'        => __( 'Video List: Title', 'pafe' ),
                        'editor_type' => 'LINE',
                    ),
                ),
                'integration-class' => 'PAFE\widgets\compatibility\wpml\Video_List',
            ];

            return $widgets;
        }
}		
