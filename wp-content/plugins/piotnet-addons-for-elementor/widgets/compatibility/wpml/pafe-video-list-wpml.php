<?php

namespace PAFE\widgets\compatibility\wpml;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Video_List extends WPML_Elementor_Module_With_Items {
    public function get_items_field() {
        return 'pafe_video_playlist';
    }

    public function get_fields() {
        return array(
            'pafe_video_playlist_item_title',
        );
    }

    protected function get_title( $field ) {
        switch ( $field ) {
            case 'pafe_video_playlist_item_title':
                return esc_html__( 'Video List: Title Item', 'pafe' );

            default:
                return '';
        }
    }

    protected function get_editor_type( $field ) {
        switch ( $field ) {
            case 'pafe_video_playlist_item_title':
                return 'LINE';

            default:
                return '';
        }
    }

}
