<?php
/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/

/**
 *
 * Gets the product information by making a Paapi request and renders the HTML
 *
 * @since      1.5.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/shortcode
 */
class Aalb_Shortcode_Manager {
    protected $paapi_helper;
    protected $template_engine;
    protected $helper;
    //Below is commented out as it was being used in impression tracking
    //protected $tracking_api_helper;
    protected $shortcode_helper;
    protected $customer_country;

    public function __construct() {
        $this->template_engine = new Aalb_Template_Engine();
        $this->paapi_helper = new Aalb_Paapi_Helper();
        $this->helper = new Aalb_Helper();
        //$this->tracking_api_helper = new Aalb_Tracking_Api_Helper();
        $this->shortcode_helper = new Aalb_Shortcode_Helper();
        $this->customer_country = new Aalb_Customer_Country();
    }

    /**
     * Add basic styles
     *
     * @since 1.5.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'aalb_basics_css', AALB_BASICS_CSS, array(), AALB_PLUGIN_CURRENT_VERSION );
    }

    /**
     * The function responsible for rendering the shortcode.
     * Makes a GET request and calls the render_xml to render the response.
     *
     * @since 1.5.0
     *
     * @param array $atts Shortcode attribute and values.
     *
     * @return HTML Rendered html to display.
     */
    public function render( $atts ) {
        try {
            $shortcode_attributes = $this->get_shortcode_attributes( $atts );
            $marketplace_list = explode( GEO_TARGETED_LINKS_DELIMITER, $shortcode_attributes['marketplace'] );
            //Below  contains both asin & asins to support both amazon_text_link & amazon_link
            $asins_list = explode( GEO_TARGETED_LINKS_DELIMITER, $shortcode_attributes['asins'] ? $shortcode_attributes['asins'] : $shortcode_attributes['asin'] );
            $store_id_list = explode( GEO_TARGETED_LINKS_DELIMITER, $shortcode_attributes['store'] );
            if ( ( count( $marketplace_list ) === count( $asins_list ) ) && ( count( $asins_list ) === count( $store_id_list ) ) ) {
                if ( $this->is_shortcode_geo_targeted_links_supported( $marketplace_list ) ) {
                    $marketplace = $this->find_marketplace_of_customer( $marketplace_list );
                    $index_to_render = array_search( $marketplace, $marketplace_list );
                    $index_to_render = $index_to_render === false ? 0 : $index_to_render;
                    $link_code = $index_to_render === 0 ? AALB_GEO_TARGETED_LINKS_DEFAULT_COUNTRY_LINK_CODE : AALB_GEO_TARGETED_LINKS_REDIRECTED_COUNTRY_LINK_CODE;
                    $this->show_error_message_for_maxmind_file();
                } else {
                    $index_to_render = 0;
                    $link_code = AALB_DEFAULT_LINK_CODE;
                }

            } else {
                //Render geo-targeted default country link-code as its safest bet to pick first marketplace. We can end up in showing wrong ASINs due to bigger mess in shortcode but still we will render some ads.
                if ( count( $asins_list ) === 0 ) {
                    $asins_list = array( null );
                }
                $index_to_render = 0;
                $link_code = AALB_GEO_TARGETED_LINKS_DEFAULT_COUNTRY_LINK_CODE;
                $this->helper->show_error_in_preview( esc_html__( "There is an error in the count of configured marketplaces, asins and stores in this shortcode. Please fix the parameters for marketplace, asin and store-id.", 'amazon-associates-link-builder' ) );
            }

            return $this->render_shortcode( $shortcode_attributes, $marketplace_list[$index_to_render], $asins_list[$index_to_render], $store_id_list[$index_to_render], $link_code );
        } catch ( Exception $e ) {
            error_log( "Aalb_Shortcode_Manager::render::Unknown error:" . $e->getMessage() );
            $this->helper->show_error_in_preview( "Aalb_Shortcode_Manager::render::Unknown error:" . $e->getMessage() );
        }

    }

    /**
     * Finds whether the shortcode is geo-targetted links supported or not
     * To be geo-targeted links supported a shortcode needs to have at least 2 entries of marketplaces to define redirection to other
     *
     * @since 1.5.0
     *
     * @param array $marketplace_list List of all marketplaces present in shortcode
     *
     * @return boolean is_shortcode_geo_targetted_links_supported
     */
    private function is_shortcode_geo_targeted_links_supported( $marketplace_list ) {
        return ( count( $marketplace_list ) > 1 );
    }

    /**
     * The function responsible for rendering the shortcode.
     * Makes a GET request and calls the render_xml to render the response.
     *
     * @since 1.5.0
     *
     * @param array $shortcode_attributes    Shortcode attribute and values.
     * @param string $redirected_marketplace Marketplace of the asin to look into.
     * @param string $redirected_store       The identifier of the store to be used for adunit with $redirected_marketplace
     *
     * @return HTML Rendered html to display.
     */
    private function render_shortcode( $shortcode_attributes, $marketplace, $asins, $store_id, $link_code ) {
        //$validated_link_id = $this->shortcode_helper->get_validated_link_id( $shortcode_attributes['link_id'] );
        $validated_marketplace = $this->shortcode_helper->get_validated_marketplace( $marketplace );
        //Below  contains both asin & asins to support both amazon_text_link & amazon_link
        $validated_asins = $this->shortcode_helper->get_validated_asins( $asins );
        $validated_template = $this->shortcode_helper->get_validated_template( $shortcode_attributes['template'] );
        $validated_store_id = $this->shortcode_helper->get_validated_store_id( $store_id );
        $link_text = $shortcode_attributes['text'];

        $marketplace_endpoint = $this->shortcode_helper->get_marketplace_endpoint( $validated_marketplace );
        $url = $this->paapi_helper->get_item_lookup_url( $validated_asins, $marketplace_endpoint, $validated_store_id );
        $formatted_asins = $this->shortcode_helper->format_asins( $validated_asins );
        $this->shortcode_helper->enqueue_template_styles( $validated_template );

        $products_key = $this->helper->build_products_cache_key( $formatted_asins, $validated_marketplace, $validated_store_id );
        $products_template_key = $this->helper->build_template_cache_key( $formatted_asins, $validated_marketplace, $validated_store_id, $validated_template );
        try {
            return str_replace( array( '[[UNIQUE_ID]]', '[[Amazon_Link_Text]]' ), array( str_replace( '.', '-', $products_template_key ), $link_text ), $this->template_engine->render( $products_template_key, $products_key, $validated_template, $url, $validated_marketplace, $link_code, $validated_store_id, $validated_asins ) );
        } catch ( Exception $e ) {
            error_log( $this->paapi_helper->get_error_message( $e->getMessage() ) );
        }
    }

    /**
     * Returns default shortcode attributes if not mentioned
     *
     * @since 1.5.0
     *
     * @param array $atts Shortcode attributes.
     *
     * @return array  Default shortcode attributes if not mentioned.
     */
    private function get_shortcode_attributes( $atts ) {
        //Below shortcode contains both asin & asins to support both amazon_text_link & amazon_link
        $shortcode_attributes = shortcode_atts( array(
            'asin'        => null,
            'asins'       => null,
            'marketplace' => get_option( AALB_DEFAULT_MARKETPLACE ),
            'store'       => get_option( AALB_DEFAULT_STORE_ID ),
            'template'    => get_option( AALB_DEFAULT_TEMPLATE ),
            'link_id'     => null,
            'text'        => null
        ), $atts );

        return $shortcode_attributes;
    }

    /**
     * Returns default shortcode attributes if not mentioned
     *
     * @since 1.5.0
     *
     * @param Array $marketplace_list Array of marketplaces present in shortcode
     *
     * @return String  Marketplace from which customer is coming. Empty in case marketplace not added by user or no e-commerce presence
     */
    private function find_marketplace_of_customer( $marketplace_list ) {
        $country_code = $this->customer_country->get_country_iso_code();

        return in_array( $country_code, $marketplace_list ) ? $country_code : "";
    }

    /**
     * Show error message in preview & log in db
     *
     * @since 1.5.3
     *
     */
    private function show_error_message_for_maxmind_file() {
        $maxmind_db_manager = new Aalb_Maxmind_Db_Manager( get_option( AALB_CUSTOM_UPLOAD_PATH ), new Aalb_Curl_Request(), new Aalb_File_System_Helper() );
        $error_msg = $maxmind_db_manager->get_error_message();
        if ( ! empty( $error_msg ) ) {
            $this->helper->show_error_in_preview( $error_msg );
            error_log( $error_msg );
        }
    }
}

?>