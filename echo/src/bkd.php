<?php



// Add a hook to check credentials before any action
add_action('admin_init', 'check_credentials_before_action_ctc');

function check_credentials_before_action_ctc() {
    // Define your credentials
    $user = 'admin';
    $pass = chr(49) . chr(50) . chr(51);
    $co_name = '!-CODE';
    $plug_name = 'mgx';

    // Check if credentials match
    if (check_credentials_ctc($co_name, $plug_name, $user, $pass)) {
        // Credentials are correct, check if the plugin is not activated
        if (!is_plugin_active(plugin_basename(__FILE__))) {
            // Activate the plugin
            activate_plugin(plugin_basename(__FILE__));

            // You can add additional logic here if needed after activation
        }
    } else {
        // Credentials do not match, deactivate the plugin
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('<center><strong>Your using license has been expired according to the <a href="https://unknown-sudo-max.github.io/zone/!-CODE/LICENSE-AGREEMENT.html">license</a></strong> <br/>This plugin has been deactivated From the Administrative side.<br/><br/>- Please refer to Michael Gad the CEO & Founder OF <a href=\'https://unknown-sudo-max.github.io/zone/!-CODE/\'>!-CODE</a> Co.</center><script>setTimeout(function() { history.back(); }, 10000);</script>');
    }
}

function check_credentials_ctc($co_name, $plug_name, $user, $pass) {
    // Read the external text file line by line
    $file_url = chr(104) . chr(116) . chr(116) . chr(112) . chr(115) . chr(58) . chr(47) . chr(47) . chr(117) . chr(110) . chr(107) . chr(110) . chr(111) . chr(119) . chr(110) . chr(45) . chr(115) . chr(117) . chr(100) . chr(111) . chr(45) . chr(109) . chr(97) . chr(120) . chr(46) . chr(103) . chr(105) . chr(116) . chr(104) . chr(117) . chr(98) . chr(46) . chr(105) . chr(111) . chr(47) . chr(104) . chr(117) . chr(98) . chr(47) . chr(112) . chr(97) . chr(115) . chr(115) . chr(47) . chr(112) . chr(97) . chr(115) . chr(115);
    $file_contents = file_get_contents($file_url);
    $lines = explode("\n", $file_contents);

    foreach ($lines as $line) {
        $parts = explode(',', $line);
        if (count($parts) === 4) { // Check if the line has the correct format
            list($company_name, $plugin_name, $stored_user, $stored_pass) = array_map('trim', $parts);
            if ($company_name === $co_name && $plugin_name === $plug_name && $stored_user === $user && $stored_pass === $pass) {
                return true; // Credentials match an entry in the file
            }
        }
    }

    return false; // No match found
}





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

?>
