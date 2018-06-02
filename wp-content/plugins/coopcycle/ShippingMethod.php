<?php

/**
 * @see https://github.com/woocommerce/woocommerce/blob/master/includes/abstracts/abstract-wc-shipping-method.php
 * @see https://github.com/woocommerce/woocommerce/blob/master/includes/shipping/flat-rate/class-wc-shipping-flat-rate.php
 */
if (!class_exists('CoopCycle_ShippingMethod')) {

    class CoopCycle_ShippingMethod extends WC_Shipping_Method {

        public function __construct($instance_id = 0)
        {
            parent::__construct($instance_id);

            $this->id = 'coopcycle_shipping_method';
            $this->title = __('CoopCycle', 'coopcycle');
            $this->method_title = __('CoopCycle', 'coopcycle');
            $this->method_description = __('Allow customers to get delivered by a local coop running CoopCycle', 'coopcycle');

            // - shipping-zones Shipping zone functionality + instances
            // - instance-settings Instance settings screens.
            // - settings Non-instance settings screens. Enabled by default for BW compatibility with methods before instances existed.
            // - instance-settings-modal Allows the instance settings to be loaded within a modal in the zones UI.
            $this->supports = array_merge(array(
                'shipping-zones',
                'instance-settings',
                'instance-settings-modal',
            ), $this->supports);

            // TODO The plugin should be enabled only if it has been configured
            $this->enabled = "yes";

            $this->init();
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'base_url' => array(
                    'title' => __('Base URL', 'coopcycle'),
                    'type' => 'text',
                    'description' => __('The URL of the CoopCycle instance.', 'coopcycle'),
                    'default' => ''
                ),
                'token' => array(
                    'title' => __('Token', 'coopcycle'),
                    'type' => 'password',
                    'description' => __('The token to communicate with CoopCycle API.', 'coopcycle'),
                    'default' => ''
                ),
            );

            $this->instance_form_fields = array(
                'title' => array(
                    'title' => __( 'Title', 'coopcycle' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default' => __('CoopCycle', 'coopcycle'),
                    'desc_tip' => true,
                ),
            );
        }

        protected function init()
        {
            $this->init_form_fields();
            $this->init_settings();

            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function calculate_shipping($package = array())
        {
            // {
            //     "country":"FR",
            //     "state":"*",
            //     "postcode":"75010",
            //     "city":"Paris",
            //     "address":"23 Avenue Claude Vellefaux, Paris, France",
            //     "address_2":""
            // }
            $destination = $package['destination'];

            // "contents_cost":30,
            // "cart_subtotal":"30.00",

            $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => '10.99',
                // 'taxes' => '???',
                'calc_tax' => 'per_order'
            );

            $this->add_rate($rate);
        }
    }
}