
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

//////////////////////////////////Redirection 301/////////////////////////////////




// Activation Hook: Initialize options
register_activation_hook(__FILE__, function () {
    add_option('simple_redirects', []);
    add_option('simple_404_errors', []);
});

// Deactivation Hook: Clean up options
register_deactivation_hook(__FILE__, function () {
    delete_option('simple_redirects');
    delete_option('simple_404_errors');
});

// Admin Menu for Redirection Plugin
add_action('admin_menu', function () {
    add_options_page(
        'Simple Redirection',    // Page Title
        'Redirection 301',       // Menu Title
        'read',        // Capability
        'CFP Redirection 301',   // Menu Slug
        function () {
            // Handle adding new redirect
            if ($_POST['action'] === 'add_redirect') {
                $redirects = get_option('simple_redirects', []);
                $redirects[] = [
                    'from' => esc_url_raw($_POST['from']), // Accept full URL
                    'to'   => esc_url_raw($_POST['to']),   // Accept full URL
                ];
                update_option('simple_redirects', $redirects);
            }

            // Handle clearing 404 logs
            if ($_POST['action'] === 'clear_404_logs') {
                delete_option('simple_404_errors');
            }

            // Handle deleting a specific redirect
            if ($_POST['action'] === 'delete_redirect') {
                $redirects = get_option('simple_redirects', []);
                $index = $_POST['index'];
                unset($redirects[$index]);
                $redirects = array_values($redirects); // Reindex array
                update_option('simple_redirects', $redirects);
            }

            // Display admin page content
            $redirects = get_option('simple_redirects', []);
            $errors = get_option('simple_404_errors', []);
            ?>
            <div class="wrap">
                <h1>Redirection 301</h1>

                <form method="post">
                    <input type="hidden" name="action" value="add_redirect">
                    <table class="form-table">
                        <tr>
                            <th><label for="from">From URL (full):</label></th>
                            <td><input type="url" name="from" id="from" required></td>
                        </tr>
                        <tr>
                            <th><label for="to">To URL (full):</label></th>
                            <td><input type="url" name="to" id="to" required></td>
                        </tr>
                    </table>
                    <button type="submit" class="button-primary">Add Redirect</button>
                </form>

                <h2>Existing Redirects</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>From URL</th>
                            <th>To URL</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($redirects as $index => $redirect): ?>
                            <tr>
                                <td><?php echo esc_html($redirect['from']); ?></td>
                                <td><?php echo esc_html($redirect['to']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_redirect">
                                        <input type="hidden" name="index" value="<?php echo $index; ?>">
                                        <button type="submit" class="button-link">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2>Logged 404 Errors</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Error URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($errors): ?>
                            <?php foreach ($errors as $error): ?>
                                <tr>
                                    <td><?php echo esc_html($error); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>No 404 errors logged.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <form method="post">
                    <input type="hidden" name="action" value="clear_404_logs">
                    <button type="submit" class="button-secondary" onclick="return confirm('Are you sure you want to clear all 404 logs?')">Clear 404 Logs</button>
                </form>
            </div>

            <?php
        }
    );
});

// Handle Redirects for Full URLs
add_action('template_redirect', function () {
    $redirects = get_option('simple_redirects', []);
    $current_url = home_url($_SERVER['REQUEST_URI']); // Get the full current URL

    foreach ($redirects as $redirect) {
        if (trailingslashit($redirect['from']) === trailingslashit($current_url)) {
            wp_redirect($redirect['to'], 301);
            exit;
        }
    }
});

// Log 404 Errors with Full URL
add_action('wp', function () {
    if (is_404()) {
        $errors = get_option('simple_404_errors', []);
        $current_url = home_url($_SERVER['REQUEST_URI']); // Get the full current URL

        // Avoid duplicate logging
        if (!in_array($current_url, $errors)) {
            $errors[] = $current_url;
            update_option('simple_404_errors', $errors);
        }
    }
});


