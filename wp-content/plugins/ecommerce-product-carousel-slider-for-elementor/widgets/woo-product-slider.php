<?php
namespace WB_WPCE\PRODUCT_CAROUSEL;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Elementor Post Slider Slider Widget.
 *
 * Main widget that create the Post Slider widget
 *
 * @since 1.0.0
*/
class WB_WPCE_WIDGET extends \Elementor\Widget_Base
{

	/**
	 * Get widget name
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wpce-slider';
	}

	/**
	 * Get widget title
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html( 'Product Slider', 'wpce' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * Retrieve the widget category.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_categories() {
		return [ 'web-builder-element' ];
	}

	public function get_style_depends()
    {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim',
        ];
    }

    public function get_script_depends()
    {
        return [
            'font-awesome-4-shim'
        ];
    }

	/**
	 * Retrieve the widget category.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'query_configuration',
			[
				'label' => esc_html( 'Query Builder', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);


        $this->add_control(
			'post_status',
			[
				'label' => esc_html__( 'Post Status', 'wpce' ),
				'placeholder' => esc_html__( 'Choose Post Status', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'publish',
				'multiple' => true,
				'options' => wpce_get_post_status(),
			]
		);

		$this->add_control(
			'product_types',
			[
				'label' => esc_html__( 'Product Types', 'wpce' ),
				'placeholder' => esc_html__( 'Choose Products to Include', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => '',
				'options' => wpce_get_product_types(),
			]
		);

		/*$this->add_control(
			'product_cats',
			[
				'label' => esc_html__( 'Categories', 'wpce' ),
				'placeholder' => esc_html__( 'Choose Categories to Include', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'product_tags',
			[
				'label' => esc_html__( 'Tags', 'wpce' ),
				'placeholder' => esc_html__( 'Choose Tags to Include', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$product_lists = wpce_get_product_lists();
		$this->add_control(
			'include_products_posts',
			[
				'label' => esc_html__( 'Include Products', 'wpce' ),
				'placeholder' => esc_html__( 'Search for Products to Include', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'exclude_products_posts',
			[
				'label' => esc_html__( 'Exclude Products', 'wpce' ),
				'placeholder' => esc_html__( 'Search for Products to Exclude', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);*/

        $this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Limit', 'wpce' ),
				'placeholder' => esc_html__( 'Default is 10', 'wpce' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -1,
				'default' => 10,
			]
		);

		$this->add_control(
			'more_feature_one',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'item_configuration',
			[
				'label' => esc_html( 'Item Configurtion', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'template_style',
			[
				'label' => esc_html__( 'Template Style', 'wpce' ),
				'placeholder' => esc_html__( 'Choose Template from Here', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'wpce' ),
				],
			]
		);

		$this->add_control(
			'display_image',
			[
				'label' => esc_html__( 'Show Image', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpce' ),
				'label_off' => esc_html__( 'No', 'wpce' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'default' => 'medium_large',
				'condition' => [
					'display_image'	=>	'yes',
				]
			]
		);


		$this->add_control(
			'display_rating',
			[
				'label' => esc_html__( 'Display Ratings', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpce' ),
				'label_off' => esc_html__( 'No', 'wpce' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'display_price',
			[
				'label' => esc_html__( 'Display Price', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpce' ),
				'label_off' => esc_html__( 'No', 'wpce' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		/*$this->add_control(
			'display_content',
			[
				'label' => esc_html__( 'Display Content', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpce' ),
				'label_off' => esc_html__( 'No', 'wpce' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);*/

		/*$this->add_control(
			'display_title',
			[
				'label' => esc_html__( 'Display Title', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);
		$this->add_control(
			'display_read_more',
			[
				'label' => esc_html__( 'Display Add to Cart Button', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'item_spacing',
			[
				'label' => __( 'Item Spacing', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);*/

		$this->add_control(
			'more_feature_two',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_configuration',
			[
				'label' => esc_html( 'Slider Configurtion', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slide_to_show',
			[
				'label' => __( 'Slides to Show', 'wpce' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'label' => __( 'Slides to Scroll', 'wpce' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);


		/*$this->add_control(
			'pauseOnFocus',
			[
				'label' => esc_html__( 'Pause On Focus', 'wpce' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'wpce' ),
				'label_off' => esc_html__( 'No', 'wpce' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);*/

		/*$this->add_control(
			'display_navigation_arrows',
			[
				'label' => esc_html__( 'Display Navigation Arrows', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'display_dots',
			[
				'label' => esc_html__( 'Display Dots', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'AutoPlay', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'AutoPlay Speed', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'pauseOnHover',
			[
				'label' => esc_html__( 'Pause On Hover', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'pauseOnDotsHover',
			[
				'label' => esc_html__( 'Pause On Dots Hover', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'slide_speed',
			[
				'label' => esc_html__( 'Slide Speed', 'wpce' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);*/

		$this->add_control(
			'more_feature_three',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html( 'Title Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'title_link_tabs'
		);

		$this->start_controls_tab(
			'title_link_normal_tab',
			[
				'label' => __( 'Normal', 'wpce' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'separator'=> 'after',
				'selectors' => [
					'{{WRAPPER}} .wpce_title a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_link_hover_tab',
			[
				'label' => __( 'Hover', 'wpce' ),
			]
		);
		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'Hover Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'separator'=> 'after',
				'selectors' => [
					'{{WRAPPER}} .wpce_title a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		/*$this->add_control(
			'title_typography',
			[
				'label' => esc_html__( 'Typography:', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
				'description'	=>	'<strong>Change Font family, Font Size, Line Height etc.</strong>',
			]
		);

		$this->add_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin:', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
				//'description'	=>	'<strong>Change Font family, Font Size, Line Height etc.</strong>',
			]
		);

		$this->add_control(
			'title_text_align',
			[
				'label' => esc_html__( 'Text Align:', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
				//'description'	=>	'<strong>Change Font family, Font Size, Line Height etc.</strong>',
			]
		);	*/
		$this->add_control(
			'more_feature_four',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rating_style_section',
			[
				'label' => esc_html( 'Star Rating Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'rating_default_color',
			[
				'label' => __( 'Default Star Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'separator'=> 'after',
				'selectors' => [
					'{{WRAPPER}} .wpce-rating .star-rating::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'rating_fill_color',
			[
				'label' => __( 'Positive Star Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'separator'=> 'after',
				'selectors' => [
					'{{WRAPPER}} .wpce-rating .star-rating span::before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'more_feature_eight',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'price_style_section',
			[
				'label' => esc_html( 'Price Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Price Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'separator'=> 'after',
				'selectors' => [
					'{{WRAPPER}} .wpce_price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'more_feature_nine',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'cart_btn_style_section',
			[
				'label' => esc_html( 'Cart Button Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'cart_btn_style_tabs'
		);

		$this->start_controls_tab(
			'cart_btn_normal_tab',
			[
				'label' => __( 'Normal', 'wpce' ),
			]
		);

		$this->add_control(
			'cart_btn_color',
			[
				'label' => __( 'Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpce_add_to_cart_btn' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'cart_btn_border',
				'label' => __( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce_add_to_cart_btn',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'cart_btn_background',
				'label' => __( 'Background', 'plugin-domain' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpce_add_to_cart_btn',
			]
		);

		$this->add_control(
			'ajax_added_to_cart_btn_color',
			[
				'label' => __( 'Added to Cart Text Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .added_to_cart' => 'color: {{VALUE}}',
				],
			]
		);

		/*$this->add_control(
			'read_more_text_align',
			[
				'label' => esc_html__( 'Button Align', 'post-slider-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);	

		$this->add_control(
			'read_more_typography',
			[
				'label' => esc_html__( 'Typography:', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
				'description'	=>	'<strong>Change Font family, Font Size, Line Height etc.</strong>',
			]
		);

		$this->add_control(
			'read_more_padding',
			[
				'label' => __( 'Padding', 'post-slider-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);*/
		$this->add_control(
			'more_feature_five',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_btn_hover_tab',
			[
				'label' => __( 'Hover', 'wpce' ),
			]
		);

		$this->add_control(
			'cart_btn_hover_color',
			[
				'label' => __( 'Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpce_add_to_cart_btn:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'cart_btn_hover_border',
				'label' => __( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce_add_to_cart_btn:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'cart_btn_hover_background',
				'label' => __( 'Background', 'plugin-domain' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpce_add_to_cart_btn:hover',
			]
		);

		$this->add_control(
			'ajax_added_to_cart_btn_hover_color',
			[
				'label' => __( 'Added to Cart Text Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .added_to_cart:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();


		// Arrow Style
		$this->start_controls_section(
			'nav_arrow_style_section',
			[
				'label' => esc_html( 'Navigation Arrow Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);


		$this->start_controls_tabs(
			'nav_arrow_style_tabs'
		);

		$this->start_controls_tab(
			'nav_arrow_normal_tab',
			[
				'label' => __( 'Normal', 'wpce' ),
			]
		);

		$this->add_control(
			'nav_arrow_color',
			[
				'label' => __( 'Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpce-arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'nav_arrow_border',
				'label' => __( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce-arrow',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'nav_arrow_background',
				'label' => __( 'Background', 'plugin-domain' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpce-arrow',
			]
		);

		/*$this->add_control(
			'nav_arrow_left_icon',
			[
				'label' => __( 'Left Icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'nav_arrow_right_icon',
			[
				'label' => __( 'Right Icon', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'nav_arrow_width',
			[
				'label' => __( 'Width', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'nav_arrow_height',
			[
				'label' => __( 'Height', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->add_control(
			'nav_arrow_typography',
			[
				'label' => esc_html__( 'Typography:', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
				'description'	=>	'<strong>Change Font family, Font Size, Line Height etc.</strong>',
			]
		);*/
		$this->add_control(
			'more_feature_six',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'nav_arrow_hover_tab',
			[
				'label' => __( 'Hover', 'wpce' ),
			]
		);

		$this->add_control(
			'nav_arrow_hover_color',
			[
				'label' => __( 'Color', 'wpce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpce-arrow:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'nav_arrow_border_hover',
				'label' => __( 'Border', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce-arrow:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'nav_arrow_hover_background',
				'label' => __( 'Background', 'plugin-domain' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpce-arrow:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'others_style_section',
			[
				'label' => esc_html( 'Others Style', 'wpce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'full_content_box_shadow',
				'label' => __( 'Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce_single_item',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'label' => __( 'Image Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .wpce_thumbnail img',
			]
		);

		$this->add_control(
			'more_feature_seven',
			[
				'label' => __( '<strong>Need More Options:</strong>', 'news-ticker-for-elementor' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label_block' => false,
				'separator'	=> 'before',
				'button_type' => 'danger',
				'text' => __( '<a style="color: #fff; font-size: 12px; padding: 0 10px; height: 100%; display: block; line-height: 28px;" href="'.WPCE_PRO_LINK.'" target="_blank" >Buy Pro</a>', 'plugin-domain' ),
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$element_id = 'wb_wpce_'.$this->get_id();

		$template_style = $settings['template_style'];
		
		$slide_to_show = isset($settings['slide_to_show']) && $settings['slide_to_show'] ? $settings['slide_to_show'] : 3;
		$slides_to_scroll = isset($settings['slides_to_scroll']) && $settings['slides_to_scroll'] ? $settings['slides_to_scroll'] : 3;

		$display_rating = isset($settings['display_rating']) && $settings['display_rating'] ? $settings['display_rating'] : 'no';
		$display_price = isset($settings['display_price']) && $settings['display_price'] ? $settings['display_price'] : 'no';


		$args = array();

		// $args['post_type'] = 'product';
		$args['status'] = 'publish';
		if( isset($settings['post_status']) && is_array($settings['post_status']) && !empty($settings['post_status']) ){
			$args['status'] = $settings['post_status'];
		}

		if( isset($settings['product_types']) && is_array($settings['product_types']) && !empty($settings['product_types']) ){
			$args['type'] = $settings['product_types'];
		}


		if( isset($settings['posts_per_page']) && intval($settings['posts_per_page']) > 0 ){
			$args['limit'] = $settings['posts_per_page'];
		}

		if( isset($settings['posts_per_page']) && intval($settings['posts_per_page']) == -1 ){
			$args['limit'] = $settings['posts_per_page'];
		}
  

        echo '<div
        		class="wpce_slider_wrapper wpce_slider_wrapper_'.$template_style.'"
        		id="wpce_slider_wrapper_'.esc_attr($element_id).'"
        		data-slide-to-show="'.$slide_to_show.'"
        		data-slides-to-scroll="'.$slides_to_scroll.'"
        	>';
        	
        $products = wc_get_products($args);
        if( $products ){
        	$count=0;
			foreach( $products as $product ){
				$count++;
				$thumbnail_id = $product->get_image_id();
				// $product = wc_get_product(get_the_ID());
				if( $template_style === 'default' ){
					require( WPCE_PATH . 'templates/style-1/template.php' );
				}
			}
		}
		echo "</div>";
		
		
		?>
			<div class="wpce-arrow wb-arrow-prev">
				<i class="fa fa-angle-left"></i>
			</div>
			<div class="wpce-arrow wb-arrow-next">
				<i class="fa fa-angle-right"></i>
			</div>
		<?php
			//echo apply_filters('wpce_arrow_left_container', $arrow_left_container);
			//echo apply_filters('wpce_arrow_right_container', $arrow_right_container);


	}


}
