
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

//////////////////////////////////////////////////user//////////////////////////





// Add the submenu item under Users
function custom_user_creator_menu() {
    add_submenu_page(
        'users.php', // Parent menu slug (Users)
        'Create User',
        'Create User',
        'manage_options',
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
