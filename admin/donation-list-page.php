<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Burkina_seamless_donations_List_Table class that will display our custom table
 * This class was copied from somewhere but I can't rememeber where from
 */
class burkina_seamless_donations_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] Declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'donation',
            'plural' => 'donations',
        ));
    }

    /**
    * [REQUIRED] this is a default column renderer
    *
    * @param $item - row (key, value array)
    * @param $column_name - string (key)
    * @return HTML
    */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
    * [OPTIONAL] this is example, how to render specific column
    *
    * method name must be like this: "column_[column_name]"
    *
    * @param $item - row (key, value array)
    * @return HTML
    */
    function column_amount( $item )
    {
        return '<em>' . number_format( intval( $item['amount'] ), 2, ",", ".") . '</em>';
    }

    /**
    * [OPTIONAL] this is example, how to render column with actions,
    * when you hover row "Edit | Delete" links showed
    *
    * @param $item - row (key, value array)
    * @return HTML
    */
    function column_fname( $item )
    {
        /*
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=persons_form&id=%s">%s</a>', $item['bsd_id'], __('Edit', 'burkina-seamless-donations')),
        );

        return sprintf('%s %s',
            $item['fname'],
            $this->row_actions($actions)
        );
        */
        return $item['fname'];
    }

    /**
    * [REQUIRED] This method return columns to display in table
    * you can skip columns that you do not want to show
    * like content, or description
    *
    * @return array
    */
    function get_columns()
    {
        $columns = array(
            'bsd_id' => 'ID',
            'fname'  => 'Name',
            'lname'  => 'Last Name',
            'email'  => 'E-Mail',
            'phone'  => 'Phone',
            'amount' => 'Amount',
        );
        return $columns;
    }

    /**
    * [OPTIONAL] This method return columns that may be used to sort table
    * all strings in array - is column names
    * notice that true on name column means that its default sort
    *
    * @return array
*/
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'bsd_id' => array('bsd_id', true),
            'fname'  => array('fname', false),
            'lname'  => array('lname', false),
            'email'  => array('email', false),
            'phone'  => array('phone', false),
            'amount' => array('amount', false),
        );
        return $sortable_columns;
    }

    /**
    * [OPTIONAL] Return array of bult actions if has any
    *
    * @return array
    */
    function get_bulk_actions()
    {
        /*
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
        */
    }

    /**
    * [OPTIONAL] This method processes bulk actions
    * it can be outside of class
    * it can not use wp_redirect coz there is output already
    * in this example we are processing delete action
    * message about successful deletion will be shown on page in next part
    */
    function process_bulk_action()
    {
        /*
        global $wpdb;
        $table_name = $wpdb->prefix . 'burkina_seamless_donations'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['bsd_id']) ? $_REQUEST['bsd_id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE bsd_id IN($ids)");
            }
        }
        */
    }

    /**
    * [REQUIRED] This is the most important method
    *
    * It will get rows from database and prepare them to be showed in table
    */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'burkina_seamless_donations'; // do not forget about tables prefix

        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array( $columns, $hidden, $sortable );

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var( "SELECT COUNT(bsd_id) FROM $table_name" );

        // prepare query params, as usual current page, order by and order direction
        $paged = isset( $_REQUEST['paged'] ) ? ( $per_page * max( 0, intval( $_REQUEST['paged'] ) - 1) ) : 0;
        $orderby = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'bsd_id';
        $order = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ) ) ) ? $_REQUEST['order'] : 'desc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );

        // [REQUIRED] configure pagination
        $this->set_pagination_args( array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
        ));
    }
}

/**
* List page handler
*
* This function renders our custom table
* Notice how we display message about successfull deletion
* Actualy this is very easy, and you can add as many features
* as you want.
*
* Look into /wp-admin/includes/class-wp-*-list-table.php for examples
*/
global $wpdb;

$table = new burkina_seamless_donations_List_Table();
$table->prepare_items();

$message = '';
if ( 'delete' === $table->current_action() ) {
    $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d', 'burkina-seamless-donations' ), count( $_REQUEST['id'] ) ) . '</p></div>';
}
?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e( 'Persons', 'burkina-seamless-donations' )?> <a class="add-new-h2"
        href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=persons_form' );?>"><?php _e( 'Add new', 'burkina-seamless-donations' )?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>