<?php

if ( ! defined( 'BASEPATH' ) ) {
    exit( 'No direct script access allowed' );
}

/**
 * Ct_mod_price_mods_ext
 *
 * @package     Ct_mod_price_mods_ext
 * @author      Peter J. Herrel <peter@chilli.be>
 * @version     1.0.0
 */
class Ct_mod_price_mods_ext
{
    private $EE;

    public $settings       = array();
    public $name           = 'Cartthrob modify price modifiers';
    public $version        = '1.0.0';
    public $description    = 'Cartthrob modify price modifiers';
    public $docs_url       = 'https://github.com/diggy/ct_mod_price_mods';
    public $settings_exist = 'n';

    public $logged_in      = 0;
    public $price_retail   = 'price_retail'; // field name of extra column in Matrix-based price modifier

    /**
     * Constructor
     */
    public function __construct()
    {
        // get EE instance
        $this->EE = &get_instance();

        // returns 0 if not logged in
        $this->logged_in = $this->EE->session->userdata( 'member_id' );
    }

    /* PUBLIC METHODS *********************************************************/

    /**
     * Modify Cartthrob's price modifiers data
     *
     * If NOT logged in, set price modifiers default `price` value to `price_retail` value
     *
     * @access  public
     * @param   array $price_modifiers
     * @return  array
     */
    public function cartthrob_get_all_price_modifiers_end( $price_modifiers )
    {
        if( ! empty( $this->logged_in ) )
            return $price_modifiers;

        foreach( $price_modifiers as $mod_key => $data )
        {
            foreach( $data as $k => $v )
            {
                if ( isset( $v[$this->price_retail] ) ) {
                    $price_modifiers[$mod_key][$k]['price'] = $v[$this->price_retail];
                }
            }
        }

        return $price_modifiers;
    }

    /* REQUIRED METHODS *******************************************************/

    /**
     * Activate extension
     *
     * @access  public
     * @return  void
     */
    public function activate_extension()
    {
        $this->EE->db->insert( 'extensions', array(
            'class'    => __CLASS__,
            'method'   => 'cartthrob_get_all_price_modifiers_end',
            'hook'     => 'cartthrob_get_all_price_modifiers_end',
            'settings' => '',
            'priority' => 25,
            'version'  => $this->version,
            'enabled'  => 'y'
        ) );
    }

    /**
     * Update extension
     *
     * @access  public
     * @param   string $current
     * @return  void
     */
    public function update_extension( $current = '' )
    {
        if ( $current == '' || $current == $this->version )
        {
            return false;
        }

        $this->EE->db->where( 'class', __CLASS__ );

        $this->EE->db->update( 'extensions', array( 'version' => $this->version ) );
    }

    /**
     * Disable extension
     *
     * @access  public
     * @return  void
     */
    public function disable_extension()
    {
        $this->EE->db->where( 'class', __CLASS__ )->delete( 'extensions' );
    }

}

// end of file ext.ct_mod_price_mods.php
