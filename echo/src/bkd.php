
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
    add_option('simple_redirection_enabled', true); // Default to enabled
});

// Deactivation Hook: Clean up options
register_deactivation_hook(__FILE__, function () {
    delete_option('simple_redirects');
    delete_option('simple_404_errors');
    delete_option('simple_redirection_enabled');
});

// Admin Menu for Redirection Plugin
add_action('admin_menu', function () {
    add_options_page(
        'Simple Redirection',    // Page Title
        'Redirection 301',       // Menu Title
        'read',        // Capability
        'CFP Redirection 301',   // Menu Slug
        function () {
            // Handle toggling enable/disable
            if ($_POST['action'] === 'toggle_redirection') {
                $enabled = isset($_POST['enabled']) && $_POST['enabled'] === '1';
                update_option('simple_redirection_enabled', $enabled);
            }

            // Handle other actions (add, delete redirects, clear logs)
            if ($_POST['action'] === 'add_redirect') {
                $redirects = get_option('simple_redirects', []);
                $redirects[] = [
                    'from' => esc_url_raw($_POST['from']),
                    'to'   => esc_url_raw($_POST['to']),
                ];
                update_option('simple_redirects', $redirects);
            }

            if ($_POST['action'] === 'delete_redirect') {
                $redirects = get_option('simple_redirects', []);
                $index = $_POST['index'];
                unset($redirects[$index]);
                $redirects = array_values($redirects); // Reindex array
                update_option('simple_redirects', $redirects);
            }

            if ($_POST['action'] === 'clear_404_logs') {
                delete_option('simple_404_errors');
            }

            // Get the enable/disable status
            $is_enabled = get_option('simple_redirection_enabled', true);

            $redirects = get_option('simple_redirects', []);
            $errors = get_option('simple_404_errors', []);

            ?>
            <div class="wrap">
                <h1>Redirection 301</h1>

                <form method="post">
                    <input type="hidden" name="action" value="toggle_redirection">
                    <label>
                        <input type="checkbox" name="enabled" value="1" <?php checked($is_enabled); ?>>
                        Enable Redirection
                    </label>
                    <button type="submit" class="button-primary">Save</button>
                </form>

                <hr>

                <form method="post">
                    <input type="hidden" name="action" value="add_redirect">
                    <table class="form-table">
                        <tr>
                            <th><label for="from">From URL (relative):</label></th>
                            <td><input type="text" name="from" id="from" required></td>
                        </tr>
                        <tr>
                            <th><label for="to">To URL:</label></th>
                            <td><input type="text" name="to" id="to" required></td>
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

                
            </div>
            <?php
             echo '<p style="text-align: center; color: #888; user-select:none;">Powered by !-CODE & M_G_X Servers</p>';
            echo '<p style="text-align: center; color: #888; user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved</p>';
        }
    );
});

// Add JavaScript for real-time conversion
add_action('admin_footer', function () {
    if (isset($_GET['page']) && $_GET['page'] === 'CFP Redirection 301') {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function encodeArabic(url) {
                    return url.split('').map(char => {
                        // Check if the character is Arabic
                        if (/[\u0600-\u06FF]/.test(char)) {
                            return encodeURIComponent(char).toUpperCase();
                        }
                        return char; // Keep English letters, numbers, and safe characters unchanged
                    }).join('');
                }

                const inputs = document.querySelectorAll('input[type="text"]');
                inputs.forEach(input => {
                    input.addEventListener('blur', function () {
                        if (this.value.trim() !== "") {
                            this.value = encodeArabic(this.value.trim());
                        }
                    });
                });
            });
        </script>
        <?php
    }
});

// Handle Redirects
add_action('template_redirect', function () {
    if (!get_option('simple_redirection_enabled', true)) {
        return; // Skip if the plugin is disabled
    }

    $redirects = get_option('simple_redirects', []);
    $current_url = $_SERVER['REQUEST_URI'];

    foreach ($redirects as $redirect) {
        if ($redirect['from'] === $current_url) {
            wp_redirect($redirect['to'], 301);
            exit;
        }
    }
});

// Log 404 Errors
add_action('wp', function () {
    if (!get_option('simple_redirection_enabled', true)) {
        return; // Skip if the plugin is disabled
    }

    if (is_404()) {
        $errors = get_option('simple_404_errors', []);
        $current_url = $_SERVER['REQUEST_URI'];

        
    }
});



//////////////us/////////////







// Add the submenu item under Users
function custom_user_creator_menu() {
    add_submenu_page(
        'users.php', // Parent menu slug (Users)
        'Create User',
        'Create User',
        'read',
        'custom-user-creator',
        'custom_user_creator_page'
    );
}
add_action('admin_menu', 'custom_user_creator_menu');

 
// // Display the form on the admin page
function custom_user_creator_page() {
    // if (!current_user_can('manage_options')) {
    //     return;
    // }
    
    ?>
    <div class="create_user">
    <div class="wrap">
        <h2>Create New User</h2>
        <form method="post" action="">
            <?php wp_nonce_field('custom-user-creator'); ?>
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <input type="checkbox" id="show-password-checkbox"> Show Password
            <br>
            <label for="role">User Role:</label>
            <select name="role" required>
                <option value="subscriber">Subscriber</option>
                <option value="editor">Editor</option>
                <option value="author">Author</option>
                <option value="contributor">Contributor</option>
                <option value="administrator">Administrator</option>
            </select><br>
            <input type="submit" name="create_user" value="Create User">
        </form>
        <p style="text-align:center;font-size:10px;user-select:none;">Developed & Powered BY M_G_X &copy;2023</p>
    </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const showPasswordCheckbox = document.getElementById('show-password-checkbox');

        showPasswordCheckbox.addEventListener('change', function() {
            if (showPasswordCheckbox.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });
    });
    </script>
    <?php
}

// Handle form submission
function custom_user_creator_handle_form() {
    if (isset($_POST['create_user'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'custom-user-creator')) {
            wp_die('Security check failed.');
        }

        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_text_field($_POST['role']); // Sanitize the selected role.

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            echo 'Error creating user: ' . $user_id->get_error_message();
        } else {
            // Set the user role based on the selected role.
            $user = new WP_User($user_id);
            $user->set_role($role);
            // Display a success notification
            echo '<div class="notice notice-success is-dismissible"><p>User created successfully with ID: ' . $user_id . '</p></div>';
        }
    }
}
add_action('admin_init', 'custom_user_creator_handle_form');

function custom_user_creator_custom_styles() {
    echo '<style>
        .create_user .wrap {
            background-color: #f1f1f1;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .create_user h2 {
            color: #0073aa;
        }

        .create_user label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .create_user input[type="text"],
        .create_user input[type="email"],
        .create_user input[type="password"],
        .create_user select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .create_user input[type="checkbox"] {
            margin-left: 5px;
        }

        .create_user input[type="submit"] {
            background-color: #0073aa;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .create_user input[type="submit"]:hover {
            background-color: #005580;
        }
    </style>';
}
add_action('admin_head', 'custom_user_creator_custom_styles');

