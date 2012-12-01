<?php
function count_files_in_dir( $dir ) {
    $gdir = glob( $dir );
    return ( $gdir != false ) ? count( $gdir ) : 0;
}
?>
