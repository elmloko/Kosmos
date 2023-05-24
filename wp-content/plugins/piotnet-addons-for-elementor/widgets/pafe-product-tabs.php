<?php
	class PAFE_Product_Tabs extends \Elementor\Widget_Base {
		public function get_name() {
			return 'pafe-product-tabs';
		}

		public function get_title() {
			return __( 'PAFE Product Tabs', 'pafe' );
		}
 
		public function get_icon() {  
			return 'eicon-testimonial';
		} 

		public function get_categories() {
			return [ 'pafe-free-widgets' ];
		}

		public function get_keywords() {
			return [ 'product','tabs' ];
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
			$this->start_controls_section (
				'pafe_product_tabs_section', [
					'label' => __( 'Setting', 'pafe' ),
				] 
			);
			$this->add_control(
				'pafe_product_tabs_title', [
					'label' => __( 'Title', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => 'Product',
				]
			); 	
			$this->add_control(
				'pafe_product_tabs_post_per_pages',[
					'label' => __( 'Post per page', 'pafe' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 0,
					'max' => 100,
					'step' => 1, 
					'default' => 10,
				]
			);	 
			$this->add_control(
				'pafe_product_tabs_term_not_in', [
					'label' => __( 'Term Not In', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => 'E.g: 1,4,5',
					'default' => '',
				] 
			); 	
			$this->end_controls_tabs();	
			
		}
		protected function render() {
		$settings = $this->get_settings_for_display(); 
		
		$terms_product = get_terms(array(
				'post_types' => 'product',
				'taxonomy' => 'product_cat'
			)
		); 

		$options = [
			'post_per_pages' => $settings['pafe_product_tabs_post_per_pages'], 
			
		];
		?>
		<div class="pafe-product-tabs" data-pafe-product-tabs data-pafe-product-tabs-option='<?php echo json_encode( $options ); ?>'>
			<div class="pafe-product-tabs__filter" >
				<div class="pafe-product-tabs__filter-title">
					<?php 
						if ( !empty($settings['pafe_product_tabs_title']) ) {			
					    	echo $settings['pafe_product_tabs_title'];
						}
					?> 
				</div>	
				<div class="pafe-product-tabs__filter-list">	
				<?php foreach ($terms_product as $terms_products) :?>	
					<span class="pafe-product-tabs__filter-item" data-pafe-product-tabs-id=<?php echo $terms_products->term_id; ?> data-pafe-product-tabs-filter-item><?php echo $terms_products->name; ?></span>
				<?php endforeach;?>
				</div>
			</div>
			<div class="pafe-product-tabs__result" data-pafe-product-tabs-result>

			</div>	
		</div>	
		<?php 				
		}
	}	