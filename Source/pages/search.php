<?php
# Copyright (C) 2008	John Reese
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

require_once( config_get( 'plugin_path' ) . 'Source/Source.ViewAPI.php' );

access_ensure_global_level( plugin_config_get( 'view_threshold' ) );

$f_offset = gpc_get_int( 'offset', 1 );
$f_perpage = 25;

require_once( config_get( 'plugin_path' ) . 'Source' . DIRECTORY_SEPARATOR . 'Source.FilterAPI.php' );

# Generate listing
list( $t_filter, $t_permalink ) = Source_Generate_Filter();
list( $t_changesets, $t_count ) = $t_filter->find( $f_offset );
$t_repos = SourceRepo::load_by_changesets( $t_changesets );

html_page_top1( plugin_lang_get( 'title' ) );
html_page_top2();

?>

<br/>
<table class="width100" align="center" cellspacing="1">

<tr>
<td class="form-title" colspan="2"><?php echo plugin_lang_get( 'search' ), ' ', plugin_lang_get( 'changesets' ) ?></td>
<td class="right" colspan="2">
<?php
print_bracket_link( plugin_page( 'search' ) . $t_permalink, plugin_lang_get( 'permalink' ) );
print_bracket_link( plugin_page( 'search_page' ) . $t_permalink, plugin_lang_get( 'modify_search' ) );
print_bracket_link( plugin_page( 'search_page' ), plugin_lang_get( 'new_search' ) );
?>
</td>
</tr>

<?php Source_View_Changesets( $t_changesets, $t_repos ) ?>

<tr>
<td colspan="4" class="center">

<?php #PAGINATION

if ( $t_count > $f_perpage ) {

	$t_pages = ceil( $t_count / $f_perpage );
	$t_current = $f_offset;
	$t_page_set = array();

	$t_page_link_body = "if ( is_null( \$t ) ) { \$t = \$p; }
		return ( is_null( \$p ) ? '...' : ( \$p == $t_current ? \"<strong>\$p</strong>\" :
		'<a href=\"' . plugin_page( 'search' ) . '&offset=' . \$p . '$t_permalink' . '\">' . \$t . '</a>' ) );";
	$t_page_link = create_function( '$p, $t=null', $t_page_link_body ) or die( 'gah' );

	if ( $t_pages > 15 ) {
		$t_used_page = false;
		for( $i = 1; $i <= $t_pages; $i++ ) {
			if ( $i <= 3 || $i > $t_pages-3 ||
				( $i >= $t_current-4 && $i <= $t_current+4 ) ||
				$i % 10 == 0) {

				$t_page_set[] = $i;
				$t_used_page = true;
			} else if ( $t_used_page ) {
				$t_page_set[] = null;
				$t_used_page = false;
			}
		}

	} else {
		$t_page_set = range( 1, $t_pages );
	}

	if ( $t_current > 1 ) {
		echo $t_page_link( $f_offset-1, '<<' ), '&nbsp;&nbsp;';
	}

	$t_page_set = map( $t_page_link, $t_page_set );
	echo join( ' ', $t_page_set );

	if ( $t_current < $t_pages ) {
		echo '&nbsp;&nbsp;', $t_page_link( $f_offset+1, '>>' );
	}

}
?>
</td>
</tr>

</table>

<?php
html_page_bottom1( __FILE__ );

