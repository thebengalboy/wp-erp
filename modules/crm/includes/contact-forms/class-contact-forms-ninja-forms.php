<?php
namespace WeDevs\ERP\CRM\ContactForms;

use WeDevs\ERP\Framework\Traits\Hooker;

class Ninja_Forms {

    use Hooker;

    public function __construct() {
        $this->filter( 'erp_contact_forms_plugin_list', 'add_to_plugin_list' );
        $this->action( 'crm_get_ninja_forms_forms', 'get_forms' );
        $this->action( 'nf_save_sub', 'after_form_submit' );
    }

    /**
     * Add Ninja Forms to the integration plugin list
     *
     * @param array
     *
     * @return array
     */
    public function add_to_plugin_list( $plugins ) {
        $plugins['ninja_forms'] = [
            'title' => 'Ninja Forms',
            'is_active' => class_exists( 'Ninja_Forms' )
        ];

        return $plugins;
    }

    /**
     * Get all Ninja Forms forms and their fields
     *
     * @return array
     */
    public function get_forms() {
        $forms = [];
        $saved_settings = get_option( 'wperp_crm_contact_forms', '' );

        $nf = Ninja_forms();

        if ( !nf_is_freemius_on() ) {
            /* Support for non-freemius version */
            $form_ids = $nf->forms()->get_all();

            if ( !empty( $form_ids ) ) {
                foreach ( $form_ids as $form_id ) {
                    $form = $nf->form( $form_id );

                    $forms[ $form_id ] = [
                        'name' => $form_id,
                        'title' => $form->settings['form_title'],
                        'fields' => [],
                        'contactGroup' => '0'
                    ];

                    foreach ( $form->fields as $i => $field ) {
                        $forms[ $form_id ]['fields'][ $field['id'] ] = $field['data']['label'];

                        if ( !empty( $saved_settings['ninja_forms'][ $form_id ]['map'][ $field['id'] ] ) ) {
                            $crm_option = $saved_settings['ninja_forms'][ $form_id ]['map'][ $field['id'] ];
                        } else {
                            $crm_option = '';
                        }

                        $forms[ $form_id ]['map'][ $field['id'] ] = !empty( $crm_option ) ? $crm_option : '';
                    }


                    if ( !empty( $saved_settings['ninja_forms'][ $form_id ]['contact_group'] ) ) {
                        $forms[ $form_id ]['contactGroup'] = $saved_settings['ninja_forms'][ $form_id ]['contact_group'];
                    }
                }
            }

        } else {
            /* Support for freemius version */
            $nf_forms = $nf->form()->get_forms();

            foreach ( $nf_forms as $i => $nform ) {
                $form_id = $nform->get_id();
                $form_settings = $nform->get_settings();
                $fields = $nf->form( $form_id )->get_fields();

                $forms[ $form_id ] = [
                    'name' => $form_id,
                    'title' => $form_settings['title'],
                    'fields' => [],
                    'contactGroup' => '0'
                ];

                foreach ( $fields as $i => $field ) {
                    $field_id = $field->get_id();
                    $field_settings = $field->get_settings();

                    $forms[ $form_id ]['fields'][ $field_id ] = $field_settings['label'];

                    if ( !empty( $saved_settings['ninja_forms'][ $form_id ]['map'][ $field_id ] ) ) {
                        $crm_option = $saved_settings['ninja_forms'][ $form_id ]['map'][ $field_id ];
                    } else {
                        $crm_option = '';
                    }

                    $forms[ $form_id ]['map'][ $field_id ] = !empty( $crm_option ) ? $crm_option : '';
                }

                if ( !empty( $saved_settings['ninja_forms'][ $form_id ]['contact_group'] ) ) {
                    $forms[ $form_id ]['contactGroup'] = $saved_settings['ninja_forms'][ $form_id ]['contact_group'];
                }
            }
        }

        return $forms;
    }

    /**
     * After Ninja Forms submission hook
     *
     * @return void
     */
    public function after_form_submit( $sub_id ) {
        $nf = Ninja_forms();
        $form_id = 0;
        $data = [];

        if ( !nf_is_freemius_on() ) {
            /* Support for non-freemius version */
            $sub = $nf->sub( $sub_id );
            $form_id = $sub->form_id;
            $data = $sub->field;

        } else {
            /* Support for freemius version */
            $sub = $nf->form()->get_sub( $sub_id );

            $formData = $_POST['formData'];
            $formData = str_replace( '\"' , '"', $formData);
            $formData = json_decode( $formData, true );

            $form_id = $formData['id'];

            foreach ( $formData['fields'] as $i => $field ) {
                $data[ $field['id'] ] = $field['value'];
            }
        }

        // first check if submitted form has settings or not
        $cfi_settings = get_option( 'wperp_crm_contact_forms', '' );
        $nf_settings = $cfi_settings['ninja_forms'];

        if ( in_array( $form_id , array_keys( $nf_settings ) ) ) {
            do_action( "wperp_integration_ninja_forms_form_submit", $data, 'ninja_forms', $form_id );
        }
    }

}