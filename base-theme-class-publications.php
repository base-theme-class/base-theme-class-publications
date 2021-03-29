<?php
/*
+----------------------------------------------------------------------
| Copyright (c) 2018,2019,2020 Genome Research Ltd.
| This is part of the Wellcome Sanger Institute extensions to
| wordpress.
+----------------------------------------------------------------------
| This extension to Worpdress is free software: you can redistribute
| it and/or modify it under the terms of the GNU Lesser General Public
| License as published by the Free Software Foundation; either version
| 3 of the License, or (at your option) any later version.
|
| This program is distributed in the hope that it will be useful, but
| WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
| Lesser General Public License for more details.
|
| You should have received a copy of the GNU Lesser General Public
| License along with this program. If not, see:
|     <http://www.gnu.org/licenses/>.
+----------------------------------------------------------------------

# Support functions to make ACF managed pages easier to render..
# This is a very simple class which defines templates {and an
# associated template language which can then be used to render
# page content... more easily...}
#
# See foot of file for documentation on use...
#
# Author         : js5
# Maintainer     : js5
# Created        : 2018-02-09
# Last modified  : 2018-02-12

 * @package   BaseThemeClass/Publications
 * @author    JamesSmith james@jamessmith.me.uk
 * @license   GLPL-3.0+
 * @link      https://jamessmith.me.uk/base-theme-class/
 * @copyright 2018 James Smith
 *
 * @wordpress-plugin
 * Plugin Name: Website Base Theme Class - Publications support
 * Plugin URI:  https://jamessmith.me.uk/base-theme-class/
 * Description: Support functions define the [publications] short code and wrapper around the Pagesmith Publications database
 * Version:     0.1.0
 * Author:      James Smith
 * Author URI:  https://jamessmith.me.uk
 * Text Domain: base-theme-class-locale
 * License:     GNU Lesser General Public v3
 * License URI: https://www.gnu.org/licenses/lgpl.txt
 * Domain Path: /lang
*/

namespace BaseThemeClass;

class Publications {
  var $self;
  function __construct( $self ) {
    $this->self = $self;
    add_action( 'customize_register',              [ $this, 'publications_theme_params' ] );
    add_shortcode( 'publications', array( $this, 'publications_shortcode' ) );
  }

  function publications_theme_params( $wp_customizer ) {
    $this->self->_create_custom_theme_params( $wp_customizer, 'base-theme-class', 'Base theme class settings', [
      'publication_options' => [
        'type'        => 'text',
        'section'     => 'base-theme-class',
        'default'     => '',
        'description' => 'Options for publications listings',
      ],
    ] );
  }

  function publications_shortcode( $atts, $content = null ) {
    if( ! ( isset( $atts ) && is_array( $atts ) && sizeof( $atts ) ) ) {
      return '';
    }
    $attr_string = implode( ' ', $atts );
    if( $atts == 'undefined' ) {
      return '';
    }
    $atts_components = [];
    foreach( $atts as $k => $v ) {
      $atts_components[] = substr($k,0,1) == '-' ? "$k=$v" : $v;
    }
    $attr_string = implode( ' ', $atts_components );
    $class='pub-simple';
    if( isset( $atts['class'] ) ) {
      $class=$atts['class'];
      unset($atts['class']);
    } else {
      $class = 'publications_list';
    }
    $random_id = $this->self->sequence_id();
    return sprintf(
'
<div id="pub-%s" class="%s" data-ids="%s %s"><span class="loading_publications">Loading publications...</span></div>
',
      $random_id,
      $class,
      HTMLentities( get_theme_mod( 'publication_options' ) ),
      $attr_string
    ).
    $this->self->add_script( '', 'show_pubs("#pub-'.$random_id.'")' );
  }

}
