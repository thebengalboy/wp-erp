<?php
namespace WeDevs\ERP\Accounting;

class PDF_Install {

    public $plugin_name        = 'WP ERP - PDF Invoice';
    public $plugin_uri         = 'http://wperp.com';
    public $plugin_version     = '1.0.0';
    public $plugin_description = 'PDF invoice for WP ERP';
    public $author             = 'weDevs';
    public $author_uri         = 'http://wedevs.com';
    public $text_domain        = 'erp_pdf';
    public $domain_path        = '/languages';
    public $network            = '';
    public $title              = 'WP ERP - PDF Invoice';
    public $author_name        = 'weDevs';

    public function ac_get_plugins( $plugins ) {
        $args = array(
            'path'         => ABSPATH . 'wp-content/plugins/',
            'preserve_zip' => false
        );

        foreach ( $plugins as $plugin ) {
            $this->ac_plugin_download( $plugin['path'], $args['path'] . $plugin['name'] . '.zip' );
            $this->ac_plugin_unpack( $args, $args['path'] . $plugin['name'] . '.zip');
            $this->ac_plugin_activate( $plugin['install'] );
        }
    }

    public function ac_plugin_download( $url, $path ) {
        $data = wp_remote_get( $url, array( 'timeout' => 60 ) );
        file_put_contents( $path, $data['body'] );
    }

    public function ac_plugin_unpack( $args, $target ) {
        if ( $zip = zip_open( $target ) ) {
            while ( $entry = zip_read($zip) ) {
                $is_file   = substr( zip_entry_name( $entry ), -1 ) == '/' ? false : true;
                $file_path = $args['path'] . zip_entry_name( $entry );

                if ( $is_file ) {
                    if ( zip_entry_open( $zip, $entry, 'r' ) ) {
                        $fstream = zip_entry_read( $entry, zip_entry_filesize( $entry ) );
                        file_put_contents( $file_path, $fstream );
                        chmod( $file_path, 0777 );
                    }
                    zip_entry_close( $entry );

                } else {
                    if ( zip_entry_name( $entry ) ) {
                        mkdir( $file_path );
                        chmod( $file_path, 0777 );
                    }
                }
            }

            zip_close($zip);
        }

        if ($args['preserve_zip'] === false) {
            unlink($target);
        }
    }

    public function ac_plugin_activate( $installer ) {        
        $cache_plugins = wp_cache_get( 'plugins', 'plugins' );

        if ( ! empty( $cache_plugins ) ) {
            $new_plugin = array(
                'Name'        => $this->plugin_name,
                'PluginURI'   => $this->plugin_uri,
                'Version'     => $this->plugin_version,
                'Description' => $this->plugin_description,
                'Author'      => $this->author_name,
                'AuthorURI'   => $this->author_uri,
                'TextDomain'  => $this->text_domain,
                'DomainPath'  => $this->domain_path,
                'Network'     => $this->network,
                'Title'       => $this->plugin_name,
                'AuthorName'  => $this->author_name,
            );
            
            $cache_plugins[''][$installer] = $new_plugin;
            wp_cache_set('plugins', $cache_plugins, 'plugins');
        }

        activate_plugin( $installer );
        $this->show_activation_notice();
    }

    public function show_activation_notice() {
        echo '<div class="updated notice is-dismissible"><p>';
        echo __( 'Plugin <strong>activated.</strong>', 'erp' );
        echo '</p></div>';
    }

}
