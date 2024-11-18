
<?php


// Add a menu item under "Tools" menu
function search_replace_menu() {
    add_submenu_page(
        'tools.php',
        'Search & Replace Plugin',
        'Search & Replace',
        'read',
        'search_replace_plugin',
        'search_replace_page'
    );
}
add_action('admin_menu', 'search_replace_menu');

// Display the search replace page
function search_replace_page() {
    if (isset($_POST['submit'])) {
        $search = sanitize_text_field($_POST['search']);
        $replace = sanitize_text_field($_POST['replace']);

        if (!empty($search) && !empty($replace)) {
            // Perform search and replace in the database
            global $wpdb;
            $wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $search, $replace));

            echo '<div class="updated"><p>Search and replace completed.</p></div>';
        } else {
            echo '<div class="error"><p>Both search and replace fields are required.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h2>Search Replace Plugin</h2>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="search">Search for:</label></th>
                    <td><input type="text" name="search" id="search" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="replace">Replace with:</label></th>
                    <td><input type="text" name="replace" id="replace" class="regular-text"></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Search Replace"></p>
        </form>
    </div>
    <?php
    echo '<p style="text-align: center; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>';
    echo '<p style="text-align: center; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved</p>';
}




////////////////////////////////////auto replace//////////////////////





if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AutoSearchAndReplace {

    public function __construct() {
        // Add the plugin page to the Tools menu
        add_action('admin_menu', array($this, 'create_plugin_menu'));
    }

    // Create a new item under Tools in the WordPress Admin
    public function create_plugin_menu() {
        add_management_page(
            'Auto Search & Replace',  // Page title
            'Auto S & R',  // Menu title
            'read',           // Capability required to view the page
            'auto-search-and-replace',  // Menu slug
            array($this, 'plugin_page') // Function to display the page
        );
    }

    // Create plugin page for input and actions
    public function plugin_page() {
        ?>
        <div class="wrap">
            <h1>Auto Search and Replace</h1>
            <form method="post" enctype="multipart/form-data">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="txt_file">Upload .txt file</label>
                        </th>
                        <td>
                            <input type="file" name="txt_file" id="txt_file" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="replace_text">Replace with</label>
                        </th>
                        <td>
                            <input type="text" name="replace_text" id="replace_text" required>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Process URLs'); ?>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['txt_file'])) {
                $this->process_file();
            }
            ?>
        </div>
<?php
echo '<p style="text-align: center; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>';
    echo '<p style="text-align: center; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved</p>';
                ?>
        <?php
    }

    // Function to process the uploaded .txt file
    public function process_file() {
        if (isset($_FILES['txt_file']['tmp_name'])) {
            global $wpdb;
            
            // Get the content of the uploaded file
            $file_content = file_get_contents($_FILES['txt_file']['tmp_name']);
            $replace_text = sanitize_text_field($_POST['replace_text']);
            
            // Split the file content into an array of URLs (one URL per line)
            $urls = explode(PHP_EOL, $file_content);

            // Loop through each URL and replace it in the WordPress database
            foreach ($urls as $url) {
                $url = trim($url);  // Remove any surrounding whitespace
                if (!empty($url)) {
                    // Search and replace in posts and pages content
                    $query = "
                        UPDATE $wpdb->posts 
                        SET post_content = REPLACE(post_content, %s, %s)
                        WHERE post_content LIKE %s
                    ";
                    $wpdb->query($wpdb->prepare($query, $url, $replace_text, '%' . $wpdb->esc_like($url) . '%'));

                    // Search and replace in meta fields (e.g. custom fields)
                    $query_meta = "
                        UPDATE $wpdb->postmeta 
                        SET meta_value = REPLACE(meta_value, %s, %s)
                        WHERE meta_value LIKE %s
                    ";
                    $wpdb->query($wpdb->prepare($query_meta, $url, $replace_text, '%' . $wpdb->esc_like($url) . '%'));
                }
            }

            // Output a success message
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>URLs processed and replaced successfully.</p>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Error uploading the file.</p></div>';
        }
    }
}

// Instantiate the plugin class
new AutoSearchAndReplace();


