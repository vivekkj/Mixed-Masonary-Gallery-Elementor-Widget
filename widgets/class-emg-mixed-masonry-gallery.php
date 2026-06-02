<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EMG_Mixed_Masonry_Gallery_Widget_V105' ) ) {

	class EMG_Mixed_Masonry_Gallery_Widget_V105 extends \Elementor\Widget_Base {

		public function get_name() {
			return 'emg_mixed_masonry_gallery_v105';
		}

		public function get_title() {
			return esc_html__( 'Mixed Masonry Gallery', 'emg' );
		}

		public function get_icon() {
			return 'eicon-gallery-masonry';
		}

		public function get_categories() {
			return [ 'general' ];
		}

		public function get_keywords() {
			return [ 'gallery', 'masonry', 'video', 'image', 'lightbox' ];
		}

		public function get_style_depends() {
			return [ 'emg-masonry-gallery-v105' ];
		}

		public function get_script_depends() {
			return [ 'emg-masonry-gallery-v105' ];
		}

		protected function register_controls() {

			$this->start_controls_section(
				'section_gallery',
				[
					'label' => esc_html__( 'Gallery Items', 'emg' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'item_type',
				[
					'label'   => esc_html__( 'Item Type', 'emg' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'image',
					'options' => [
						'image' => esc_html__( 'Image', 'emg' ),
						'video' => esc_html__( 'Video', 'emg' ),
					],
				]
			);

			$repeater->add_control(
				'item_title',
				[
					'label'       => esc_html__( 'Title', 'emg' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => esc_html__( 'Gallery Item', 'emg' ),
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'item_image',
				[
					'label'   => esc_html__( 'Image / Video Thumbnail', 'emg' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			$repeater->add_control(
				'video_url',
				[
					'label'       => esc_html__( 'Video URL', 'emg' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__( 'YouTube / Vimeo / MP4 URL', 'emg' ),
					'label_block' => true,
					'condition'   => [
						'item_type' => 'video',
					],
				]
			);

			$this->add_control(
				'gallery_items',
				[
					'label'       => esc_html__( 'Gallery Items', 'emg' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'item_type'  => 'image',
							'item_title' => esc_html__( 'Image Item', 'emg' ),
							'item_image' => [
								'url' => \Elementor\Utils::get_placeholder_image_src(),
							],
						],
						[
							'item_type'  => 'video',
							'item_title' => esc_html__( 'Video Item', 'emg' ),
							'item_image' => [
								'url' => \Elementor\Utils::get_placeholder_image_src(),
							],
							'video_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
						],
					],
					'title_field' => '{{{ item_title }}}',
					'button_text' => esc_html__( 'Add Gallery Item', 'emg' ),
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_layout',
				[
					'label' => esc_html__( 'Responsive Layout', 'emg' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label'          => esc_html__( 'Columns', 'emg' ),
					'description'    => esc_html__( 'Set different masonry columns for desktop, tablet, and mobile.', 'emg' ),
					'type'           => \Elementor\Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'        => [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
					'selectors'      => [
						'{{WRAPPER}} .emg-masonry-gallery' => 'column-count: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'gap',
				[
					'label'          => esc_html__( 'Gap', 'emg' ),
					'type'           => \Elementor\Controls_Manager::SLIDER,
					'size_units'     => [ 'px' ],
					'range'          => [
						'px' => [
							'min' => 0,
							'max' => 60,
						],
					],
					'default'        => [
						'size' => 16,
						'unit' => 'px',
					],
					'tablet_default' => [
						'size' => 14,
						'unit' => 'px',
					],
					'mobile_default' => [
						'size' => 10,
						'unit' => 'px',
					],
					'selectors'      => [
						'{{WRAPPER}} .emg-masonry-gallery' => 'column-gap: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .emg-gallery-item'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'emg' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'default'    => [
						'size' => 12,
						'unit' => 'px',
					],
					'selectors'  => [
						'{{WRAPPER}} .emg-gallery-link, {{WRAPPER}} .emg-gallery-image' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			if ( empty( $settings['gallery_items'] ) || ! is_array( $settings['gallery_items'] ) ) {
				return;
			}

			$gallery_id = 'emg-gallery-' . $this->get_id();
			echo '<div class="emg-masonry-gallery" data-emg-gallery="' . esc_attr( $gallery_id ) . '">';

			$index = 0;

			foreach ( $settings['gallery_items'] as $item ) {
				$type      = ! empty( $item['item_type'] ) ? $item['item_type'] : 'image';
				$image_url = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';
				$title     = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
				$link_url  = $image_url;

				if ( 'video' === $type && ! empty( $item['video_url'] ) ) {
					$link_url = $item['video_url'];
				}

				if ( empty( $image_url ) || empty( $link_url ) ) {
					continue;
				}

				echo '<div class="emg-gallery-item">';

				printf(
					'<a href="%1$s" class="emg-gallery-link js-emg-lightbox" data-emg-gallery="%2$s" data-emg-index="%3$d" data-emg-type="%4$s" data-emg-src="%1$s" data-emg-title="%5$s">',
					esc_url( $link_url ),
					esc_attr( $gallery_id ),
					(int) $index,
					esc_attr( $type ),
					esc_attr( $title )
				);

				printf(
					'<img class="emg-gallery-image" src="%1$s" alt="%2$s" loading="lazy">',
					esc_url( $image_url ),
					esc_attr( $title )
				);

				if ( 'video' === $type ) {
					echo '<span class="emg-play-icon" aria-hidden="true"></span>';
				}

				if ( ! empty( $title ) ) {
					printf( '<span class="emg-gallery-title">%s</span>', esc_html( $title ) );
				}

				echo '</a></div>';

				$index++;
			}

			echo '</div>';
		}
	}
}
