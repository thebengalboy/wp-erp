<?php $search_keys = erp_crm_get_serach_key(); ?>
<div class="wrap erp-crm-customer" id="wp-erp">

    <h2><?php _e( 'Contact', 'wp-erp' ); ?>
        <a href="#" id="erp-customer-new" class="erp-contact-new add-new-h2" data-type="contact" title="<?php _e( 'Add New Contact', 'wp-erp' ); ?>"><?php _e( 'Add New Contact', 'wp-erp' ); ?></a>
    </h2>

    <div class="erp-advance-search-filter" id="erp-crm-save-search" v-cloak v-bind:class="classObject">
        <div v-if="showAdvanceFilter" class="erp-filter-search-wrapper"> <!-- transition="filter-search-expand" -->

            <form action="" method="post" id="erp-crm-save-search-form">
                <input type="hidden" name="page" value="erp-sales-customers">

                <div class="erp-save-search-wrapper" id="erp-save-search-wrapper">
                    <div class="erp-save-search-item" v-for="( index, searchFields ) in searchItem">
                        <save-search :search-fields="searchFields" :index="index" :total-search-item="totalSearchItem"></save-search>
                    </div>
                </div>

                <div class="erp-save-serach-action">
                    <input type="hidden" name="erp_crm_http_referer" value="<?php echo add_query_arg( ['page'=>'erp-sales-customers'], admin_url( 'admin.php' ) ); ?>">
                    <?php wp_nonce_field( 'wp-erp-crm-save-search-nonce-action', 'wp-erp-crm-save-search-nonce' ); ?>

                    <input type="submit" class="button" name="save_search_submit" value="<?php _e( 'Search', 'wp-erp' ); ?>" v-if="!isNewSave">

                    <div class="save-new-search-wrap " v-if="isNewSave">
                        <input type="text" id="erp_save_search_name" name="erp_save_search_name" placeholder="<?php _e( 'Search name..', 'wp-erp' ); ?>">
                        <input type="hidden" name="erp_save_serach_make_global" value="0">
                        <label for="erp_save_serach_make_global">
                            <input type="checkbox" id="erp_save_serach_make_global" name="erp_save_serach_make_global" value="1">
                            <?php _e( 'Make as global for all', 'wp-erp' ); ?>
                        </label>
                    </div>
                    <input v-on:click.prevent="createNewSearch" type="submit" class="button" name="save_search_action" value="<?php _e( 'Save', 'wp-erp' ); ?>" v-if="isNewSave">
                    <input v-on:click.prevent="cancelSaveSearch" type="submit" class="button" name="save_search_save_cancel" value="<?php _e( 'Cancel', 'wp-erp' ); ?>" v-if="isNewSave">
                    <input v-on:click.prevent="saveSearch" type="submit" class="button" name="save_search_action" value="<?php _e( 'Save as new', 'wp-erp' ); ?>" v-if="!isNewSave">
                </div>

                <div class="clearfix"></div>

            </form>

        </div>

        <input type="button" @click.prevent="toggleAdvanceSearchFilter" name="erp-advance-serach-filter" value="{{ searchFilterButtonText }}" class="erp-advance-search-button button">
    </div>

    <div class="list-table-wrap erp-crm-list-table-wrap">
        <div class="list-table-inner">

            <form method="get">
                <input type="hidden" name="page" value="erp-sales-customers">
                <?php
                $customer_table = new \WeDevs\ERP\CRM\Contact_List_Table( 'contact' );
                $customer_table->prepare_items();
                $customer_table->search_box( __( 'Search Contact', 'wp-erp' ), 'erp-customer-search' );
                $customer_table->views();

                $customer_table->display();
                ?>
            </form>

        </div><!-- .list-table-inner -->
    </div><!-- .list-table-wrap -->
</div>
<?php
    //echo '<pre>'; var_dump( $_SERVER["QUERY_STRING"] ); echo '</pre>';

?>
