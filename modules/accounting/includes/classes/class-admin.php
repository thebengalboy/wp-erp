<?php

namespace WeDevs\ERP\Accounting\Includes\Classes;

/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_menu' ] );
        add_action( 'admin_init', [ $this, 'init_hooks' ], 5 );
        add_action( 'erp_hr_employee_new', [ $this, 'make_people_from_employee' ], 10, 2 );
        add_action( 'admin_init', [ $this, 'save_accounting_settings' ] );
    }

    /**
     * Register the admin menu
     *
     * @return void
     */
    public function admin_menu() {
        $dashboard      = 'erp_ac_view_dashboard';
        $customer       = 'erp_ac_view_customer';
        $vendor         = 'erp_ac_view_vendor';
        $sale           = 'erp_ac_view_sale';
        $expense        = 'erp_ac_view_expense';
        $account_charts = 'erp_ac_view_account_lists';
        $bank           = 'erp_ac_view_bank_accounts';
        $journal        = 'erp_ac_view_journal';
        $reports        = 'erp_ac_view_reports';

        $slug = 'erp-accounting';

        add_submenu_page( 'erp', __( 'Accounting', 'erp' ), 'Accounting', 'erp_ac_manager', $slug, [ $this, 'erp_accounting_page' ] );

        erp_add_menu_header( 'accounting', 'Accounting', '<svg id="Group_235" data-name="Group 235" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 239 341.4"><defs><style>.cls-1{fill:#9ca1a6}</style></defs><path id="Path_281" data-name="Path 281" class="cls-1"/></svg>' );

        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Dashboard', 'erp' ),
				'capability' => $dashboard,
				'slug'       => 'dashboard',
				'position'   => 1,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Users', 'erp' ),
				'capability' => $customer,
				'slug'       => 'users',
				'position'   => 5,
			]
        );
        erp_add_submenu(
            'accounting',
            'users',
            [
				'title'      => __( 'Customers', 'erp' ),
				'capability' => $customer,
				'slug'       => 'users/customers',
				'position'   => 5,
			]
        );
        erp_add_submenu(
            'accounting',
            'users',
            [
				'title'      => __( 'Vendors', 'erp' ),
				'capability' => $vendor,
				'slug'       => 'users/vendors',
				'position'   => 10,
			]
        );
        erp_add_submenu(
            'accounting',
            'users',
            [
				'title'      => __( 'Employees', 'erp' ),
				'capability' => 'erp_ac_manager',
				'slug'       => 'users/employees',
				'position'   => 15,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Transactions', 'erp' ),
				'capability' => $expense,
				'slug'       => 'transactions',
				'position'   => 25,
			]
        );
        erp_add_submenu(
            'accounting',
            'transactions',
            [
				'title'      => __( 'Sales', 'erp' ),
				'capability' => $sale,
				'slug'       => 'transactions/sales',
				'position'   => 5,
			]
        );
        erp_add_submenu(
            'accounting',
            'transactions',
            [
				'title'      => __( 'Expenses', 'erp' ),
				'capability' => $expense,
				'slug'       => 'transactions/expenses',
				'position'   => 10,
			]
        );
        erp_add_submenu(
            'accounting',
            'transactions',
            [
				'title'      => __( 'Purchases', 'erp' ),
				'capability' => $sale,
				'slug'       => 'transactions/purchases',
				'position'   => 15,
			]
        );
        erp_add_submenu(
            'accounting',
            'transactions',
            [
				'title'      => __( 'Journals', 'erp' ),
				'capability' => $journal,
				'slug'       => 'transactions/journals',
				'position'   => 25,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Chart of Accounts', 'erp' ),
				'capability' => $account_charts,
				'slug'       => 'charts',
				'position'   => 30,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Bank Accounts', 'erp' ),
				'capability' => $bank,
				'slug'       => 'banks',
				'position'   => 35,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Products', 'erp' ),
				'capability' => 'erp_ac_view_sale',
				'slug'       => 'products',
				'position'   => 45,
			]
        );
        erp_add_submenu(
            'accounting',
            'products',
            [
				'title'      => __( 'Products & Services', 'erp' ),
				'capability' => 'erp_ac_view_sale',
				'slug'       => 'products/product-service',
				'position'   => 5,
			]
        );
        erp_add_submenu(
            'accounting',
            'products',
            [
				'title'      => __( 'Product Categories', 'erp' ),
				'capability' => 'erp_ac_view_sale',
				'slug'       => 'products/product-categories',
				'position'   => 10,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Tax', 'erp' ),
				'capability' => 'erp_ac_view_sale',
				'slug'       => 'taxes',
				'position'   => 45,
			]
        );
        erp_add_submenu(
            'accounting',
            'taxes',
            [
				'title'      => __( 'Tax Rates', 'erp' ),
				'capability' => $sale,
				'slug'       => 'taxes/tax-rates',
				'position'   => 5,
			]
        );
        erp_add_submenu(
            'accounting',
            'taxes',
            [
				'title'      => __( 'Tax Payments', 'erp' ),
				'capability' => $sale,
				'slug'       => 'taxes/tax-records',
				'position'   => 10,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Reports', 'erp' ),
				'capability' => $reports,
				'slug'       => 'reports',
				'position'   => 100,
			]
        );
        erp_add_menu(
            'accounting',
            [
				'title'      => __( 'Help', 'erp' ),
				'capability' => $dashboard,
				'slug'       => 'erp-ac-help',
				'position'   => 200,
			]
        );
    }

    /**
     * Render Admin bar menu
     */
    public function add_admin_bar_menu() {
        global $wp_admin_bar;

        /* Check that the admin bar is showing and user has permission... */
        if ( ! is_admin_bar_showing() ) {
            return;
        }

        $hide   = [];
        $header = erp_get_menu_headers();
        if ( ! current_user_can( 'administrator' ) ) {

            if ( ! current_user_can( 'erp_hr_manager' ) && ! current_user_can( 'erp_recruiter' ) && ! current_user_can( 'erp_list_employee' ) ) {
                unset( $header['hr'] );
                $hide[] = true;
            }

            if ( ! current_user_can( 'erp_crm_manager' ) && ! current_user_can( 'erp_crm_agent' ) ) {
                unset( $header['crm'] );
                $hide[] = true;
            }

            if ( ! current_user_can( 'erp_ac_manager' ) ) {
                unset( $header['accounting'] );
                $hide[] = true;
            }
        }

        if ( count( $hide ) === 3 ) {
            return;
        }

        $menu = erp_acct_quick_access_menu();

        if ( current_user_can( 'erp_ac_manager' ) ) {
            $wp_admin_bar->add_menu(
                array(
                    'parent' => 'top-secondary',
                    'id'     => 'wp-erp-acct',
                    'title'  => '<span class="ab-icon"><?xml version="1.0" encoding="UTF-8"?><svg width="17px" height="17px" viewBox="0 0 37 37" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="erp-logo" fill="#82878C" fill-rule="nonzero"><path d="M30.3450389,6.04059132 C25.6652413,7.67566309 21.1099947,9.64748663 16.7150389,11.9405913 C15.7850389,12.4605913 14.8150389,13.0405913 13.7150389,13.7205913 C13.0443119,14.2840176 12.1907283,14.5827719 11.3150389,14.5605913 C10.3150389,14.4305913 8.90503889,13.5605913 9.00503889,12.4205913 C9.14503889,10.8305913 11.7350389,9.50059132 12.9150389,8.73059132 C14.7401826,7.56812598 16.67637,6.58998289 18.6950389,5.81059132 C21.1378916,4.73528564 23.7587368,4.12160777 26.4250389,4.00059132 C26.9947028,3.99290686 27.5629047,4.06014969 28.1150389,4.20059132 C28.9137259,4.74357385 29.6602644,5.35955176 30.3450389,6.04059132 Z" id="Shape-Copy-2"></path><path d="M30.7933348,15.9696304 C29.4433348,16.4596304 17.5233348,20.8296304 15.7933348,24.3896304 C15.3633836,25.3434159 15.3633836,26.4358449 15.7933348,27.3896304 C16.3919857,28.7511327 17.7917314,29.5797177 19.2733348,29.4496304 C19.6790059,29.4462835 20.0837104,29.4094922 20.4833348,29.3396304 C21.6712939,29.1478656 22.8417823,28.8602599 23.9833348,28.4796304 C24.9833348,28.1296304 25.9833348,27.6996304 26.9833348,27.2896304 L27.8133348,26.9496304 C30.7383636,25.6650404 33.5343329,24.1046551 36.1633348,22.2896304 C34.4182609,29.8341877 28.08799,35.4468207 20.3887538,36.2759094 C12.6895175,37.104998 5.30944369,32.968757 1.99813915,25.9686936 C-1.31316539,18.9686301 0.170333077,10.6395718 5.69491091,5.21327782 C11.2194887,-0.213016153 19.5738319,-1.54678143 26.5133348,1.88963044 C16.9833348,3.59963044 12.0633348,7.18963044 9.18333477,9.75963044 L8.86333477,10.0896304 C7.93333477,11.0396304 6.66333477,12.3296304 7.47333477,14.4196304 C7.9783997,15.5890182 9.06827343,16.4007074 10.3333348,16.5496304 L10.7033348,16.5496304 C11.6270831,16.4741709 12.5076261,16.1261302 13.2333348,15.5496304 C14.2333348,14.8796304 15.2333348,14.3096304 16.1533348,13.7996304 C21.2778133,11.1185988 26.6326398,8.90303934 32.1533348,7.17963044 C33.6733348,7.84963044 36.5733348,11.2496304 36.3833348,14.1796304 C34.6633348,14.7396304 32.2733348,15.4396304 30.7933348,15.9696304 Z" id="Shape-Copy-3"></path><path d="M34.3722313,17.49 C34.3726197,18.4970161 34.2754999,19.501704 34.0822313,20.49 C31.869491,22.1051295 29.5283527,23.5365684 27.0822313,24.77 C24.8622313,25.93 22.7822313,27.32 20.2422313,27.89 C19.0122313,28.16 16.9222313,27.42 17.0022313,25.89 C17.0622313,24.8 19.0022313,23.54 19.7922313,22.98 C21.2689784,21.9825786 22.8358063,21.1255103 24.4722313,20.42 C27.682491,19.0175052 30.9997216,17.8738632 34.3922313,17 C34.3622313,17.16 34.3722313,17.32 34.3722313,17.49 Z" id="Shape-Copy-4"></path></g></g></svg></span>New Transaction',
                )
            );
        }

        foreach ( $menu as $component => $items ) {
            $wp_admin_bar->add_menu(
                array(
                    'parent' => 'wp-erp-acct',
                    'id'     => 'wp-erp-acct-' . $items['slug'],
                    'href'   => admin_url( 'admin.php?page=erp-accounting' ) . '#/' . $items['url'],
                    'title'  => $items['title'],
                )
            );
        }

    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {
        // load styles
        wp_enqueue_style( 'accounting-admin' );

        // load hook
        wp_enqueue_script( 'accounting-bootstrap' );

        // JS do action
        do_action( 'erp_acct_js_hook_loaded' );

        // load scripts
        wp_enqueue_script( 'accounting-admin' );
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function erp_accounting_page() {
        ?>
        <script>
            window.erpAcct = JSON.parse('<?php echo addslashes(
                json_encode( apply_filters( 'erp_localized_data', [] ) )
            ); ?>');
        </script>
        <?php
        echo '<div class="wrap"><div id="erp-accounting"></div></div>';

        $component = 'accounting';
        $menu      = erp_menu();
        $menu      = $menu[ $component ];
        $section   = ( isset( $_GET['section'] ) && isset( $menu[ $_GET['section'] ] ) ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : 'dashboard';
        $sub       = ( isset( $_GET['sub-section'] ) && ! empty( $menu[ $section ]['submenu'][ $_GET['sub-section'] ] ) ) ? sanitize_text_field( wp_unslash( $_GET['sub-section'] ) ) : false;
    }

    /**
     * Make employee as people
     *
     * @param integer $id
     * @param array $data
     * @return bool
     */
    public function make_people_from_employee( $id, $data ) {

        if ( ! current_user_can( 'erp_create_employee' ) ) {
            return;
        }

        $first_name = isset( $data['personal']['first_name'] ) ? $data['personal']['first_name'] : '';
        $last_name  = isset( $data['personal']['last_name'] ) ? $data['personal']['last_name'] : '';
        $mobile     = isset( $data['personal']['mobile'] ) ? $data['personal']['mobile'] : '';
        $phone      = isset( $data['personal']['phone'] ) ? $data['personal']['phone'] : '';
        $email      = isset( $data['user_email'] ) ? $data['user_email'] : '';

        $args = array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'mobile'     => $mobile,
            'phone'      => $phone,
            'type'       => 'employee',
        );

        $people_id = erp_insert_people( $args );

        if ( is_wp_error( $people_id ) ) {
            return new WP_Error( 'crate_people', __( 'Could not create people', 'erp' ) );
        }

        return $people_id;
    }

    /**
     * Save Financial Year settings
     *
     * @param integer $id
     * @param array $data
     * @return bool
     */
    public function save_accounting_settings() {
        global $wpdb;
        $fin_years = [];

        if (
            empty( $_POST['ob_names'] )
            || empty( $_POST['ob_starts'] )
            || empty( $_POST['ob_ends'] )
        ) {
            return;
        }

        $ob_names   = $_POST['ob_names'];
        $ob_starts  = $_POST['ob_starts'];
        $ob_ends    = $_POST['ob_ends'];
        $created_by = get_current_user_id();

        if ( ! empty( $ob_names ) ) {
            for ( $i = 0; $i < count( $ob_names ); $i++ ) {
                $fin_years['ob_names'][] = $ob_names[ $i ];
            }
        }

        if ( ! empty( $ob_starts ) ) {
            for ( $i = 0; $i < count( $ob_starts ); $i++ ) {
                $fin_years['ob_starts'][] = $ob_starts[ $i ];
            }
        }

        if ( ! empty( $ob_ends ) ) {
            for ( $i = 0; $i < count( $ob_ends ); $i++ ) {
                $fin_years['ob_ends'][] = $ob_ends[ $i ];
            }
        }

        $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'erp_acct_financial_years' );

        for ( $i = 0; $i < count( $ob_names ); $i++ ) {
            $wpdb->insert(
                $wpdb->prefix . 'erp_acct_financial_years',
                [
					'name'       => $fin_years['ob_names'][ $i ],
					'start_date' => $fin_years['ob_starts'][ $i ],
					'end_date'   => $fin_years['ob_ends'][ $i ],
					'created_at' => date( 'Y-m-d' ),
					'created_by' => $created_by
                ],
                ['%s', '%s', '%s', '%s', '%d']
            );
        }
    }
}
