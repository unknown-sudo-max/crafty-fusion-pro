
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
                
                // Get the values from the form without encoding or decoding
                $from_url = $_POST['from'];
                $to_url = $_POST['to'];
                
                // Add the redirect to the list
                $redirects[] = [
                    'from' => $from_url,
                    'to'   => $to_url,
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
        function encodeSpecialCharacters(url) {
            return url.split('').map(char => {
                // Check if the character is Arabic and encode it
                if (/[\u0600-\u06FF]/.test(char)) {
                    return encodeURIComponent(char).toUpperCase();
                }

                // Encode special characters like < and > to their respective URL-encoded values
                const specialChars = {
                    '<': '%3C',
                    '>': '%3E',
                    '"': '%22',
                    ' ': '%20',  // Encode space as %20
                    ';': '%3B'
                };

                // If the character is in the specialChars map, replace it
                if (specialChars[char]) {
                    return specialChars[char];
                }

                // Keep other characters unchanged (letters, numbers, etc.)
                return char;
            }).join('');
        }

        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(input => {
            input.addEventListener('blur', function () {
                if (this.value.trim() !== "") {
                    this.value = encodeSpecialCharacters(this.value.trim());
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

 
});

////////////////minifier///////////////////////



class HTML_CSS_JS_Minifier_Plugin {
    
    public function __construct() {
        add_action('init', [$this, 'init_hooks']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_run_minifier', [$this, 'run_minifier']);
    }

    public function init_hooks() {
        ob_start([$this, 'minify_content']);
    }

    public function minify_content($buffer) {
        if (is_admin()) {
            return $buffer;
        }
        
        // Minify HTML
        $original_html_size = strlen($buffer);
        $minified_html = $this->html_minify($buffer);
        $minified_html_size = strlen($minified_html);
        $html_savings = $original_html_size - $minified_html_size;

        // Minify CSS
        preg_match_all('/<style.*?>(.*?)<\/style>/is', $minified_html, $css_matches);
        $original_css_size = 0;
        $minified_css_size = 0;
        foreach ($css_matches[1] as $css) {
            $original_css_size += strlen($css);
            $minified_css = $this->css_minify($css);
            $minified_css_size += strlen($minified_css);
        }

        // Minify JS
        preg_match_all('/<script.*?>(.*?)<\/script>/is', $minified_html, $js_matches);
        $original_js_size = 0;
        $minified_js_size = 0;
        foreach ($js_matches[1] as $js) {
            $original_js_size += strlen($js);
            $minified_js = $this->js_minify($js);
            $minified_js_size += strlen($minified_js);
        }

        // Calculate savings
        $total_savings = ($html_savings + $original_css_size - $minified_css_size + $original_js_size - $minified_js_size);

        // Save the stats
        update_option('minifier_stats', [
            'html_original_size' => $original_html_size,
            'html_minified_size' => $minified_html_size,
            'html_savings' => $html_savings,
            'css_original_size' => $original_css_size,
            'css_minified_size' => $minified_css_size,
            'js_original_size' => $original_js_size,
            'js_minified_size' => $minified_js_size,
            'total_savings' => $total_savings,
            'total_percentage' => $this->calculate_percentage($original_html_size + $original_css_size + $original_js_size, $total_savings),
        ]);

        return $minified_html;
    }

    private function html_minify($html) {
        $search = [
            '/\>[^\S ]+/s',  // Remove spaces after tags
            '/[^\S ]+\</s',  // Remove spaces before tags
            '/(\s)+/s'       // Remove multiple spaces
        ];
        $replace = [
            '>',
            '<',
            '\\1'
        ];
        return preg_replace($search, $replace, $html);
    }

    private function css_minify($css) {
        // Basic CSS minification
        $css = preg_replace('/\s+/s', ' ', $css); // Remove extra spaces
        $css = preg_replace('/\s*([{}|:;,])\s*/', '$1', $css); // Remove spaces around symbols
        $css = preg_replace('/\/\*.*?\*\//s', '', $css); // Remove comments
        return trim($css);
    }

    private function js_minify($js) {
        // Basic JS minification (you can use a library like JSMin or UglifyJS for more advanced features)
        $js = preg_replace('/\s+/s', ' ', $js); // Remove extra spaces
        $js = preg_replace('/\s*([{}|:;,])\s*/', '$1', $js); // Remove spaces around symbols
        $js = preg_replace('/\/\*.*?\*\//s', '', $js); // Remove comments
        return trim($js);
    }

    private function calculate_percentage($original_size, $savings) {
        if ($original_size > 0) {
            return round(($savings / $original_size) * 100, 2);
        }
        return 0;
    }

    public function add_admin_menu() {
        add_menu_page(
            'Minifier',
            'Minifier',
            'read',
            'minifier',
            [$this, 'admin_page'],
            'dashicons-analytics',
            100
        );
    }

    public function admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $stats = get_option('minifier_stats', [
            'html_original_size' => 0,
            'html_minified_size' => 0,
            'html_savings' => 0,
            'css_original_size' => 0,
            'css_minified_size' => 0,
            'js_original_size' => 0,
            'js_minified_size' => 0,
            'total_savings' => 0,
            'total_percentage' => 0
        ]);

        echo '<div class="wrap">';
        echo '<h1>HTML, CSS, and JS Minifier</h1>';
        echo '<p><strong>HTML:</strong> ' . esc_html($stats['html_savings']) . ' bytes saved (' . esc_html($this->calculate_percentage($stats['html_original_size'], $stats['html_savings'])) . '%)</p>';
        echo '<p><strong>CSS:</strong> ' . esc_html($stats['css_original_size'] - $stats['css_minified_size']) . ' bytes saved</p>';
        echo '<p><strong>JS:</strong> ' . esc_html($stats['js_original_size'] - $stats['js_minified_size']) . ' bytes saved</p>';
        echo '<p><strong>Total Savings:</strong> ' . esc_html($stats['total_savings']) . ' bytes (' . esc_html($stats['total_percentage']) . '%)</p>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="run_minifier">';
        submit_button('Run Minifier');
        echo '</form>';
        echo '</div>';
    }

    public function run_minifier() {
        global $wpdb;

        $total_html_original_size = 0;
        $total_html_minified_size = 0;
        $total_html_savings = 0;
        $total_css_original_size = 0;
        $total_css_minified_size = 0;
        $total_js_original_size = 0;
        $total_js_minified_size = 0;
        $total_savings = 0;

        // Minify all posts and pages
        $posts = $wpdb->get_results("SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type IN ('post', 'page')");

        foreach ($posts as $post) {
            $original_content = $post->post_content;
            $original_html_size = strlen($original_content);
            $minified_html = $this->html_minify($original_content);
            $minified_html_size = strlen($minified_html);
            $html_savings = $original_html_size - $minified_html_size;

            // Process CSS
            preg_match_all('/<style.*?>(.*?)<\/style>/is', $minified_html, $css_matches);
            $original_css_size = 0;
            $minified_css_size = 0;
            foreach ($css_matches[1] as $css) {
                $original_css_size += strlen($css);
                $minified_css_size += strlen($this->css_minify($css));
            }

            // Process JS
            preg_match_all('/<script.*?>(.*?)<\/script>/is', $minified_html, $js_matches);
            $original_js_size = 0;
            $minified_js_size = 0;
            foreach ($js_matches[1] as $js) {
                $original_js_size += strlen($js);
                $minified_js_size += strlen($this->js_minify($js));
            }

            // Update post content in the database
            $wpdb->update(
                $wpdb->posts,
                ['post_content' => $minified_html],
                ['ID' => $post->ID],
                ['%s'],
                ['%d']
            );

            // Update totals
            $total_html_original_size += $original_html_size;
            $total_html_minified_size += $minified_html_size;
            $total_html_savings += $html_savings;
            $total_css_original_size += $original_css_size;
            $total_css_minified_size += $minified_css_size;
            $total_js_original_size += $original_js_size;
            $total_js_minified_size += $minified_js_size;
        }

        // Save total stats to options
        $total_savings = ($total_html_savings + $total_css_original_size - $total_css_minified_size + $total_js_original_size - $total_js_minified_size);
        update_option('minifier_stats', [
            'html_original_size' => $total_html_original_size,
            'html_minified_size' => $total_html_minified_size,
            'html_savings' => $total_html_savings,
            'css_original_size' => $total_css_original_size,
            'css_minified_size' => $total_css_minified_size,
            'js_original_size' => $total_js_original_size,
            'js_minified_size' => $total_js_minified_size,
            'total_savings' => $total_savings,
            'total_percentage' => $this->calculate_percentage($total_html_original_size + $total_css_original_size + $total_js_original_size, $total_savings)
        ]);

        wp_redirect(admin_url('admin.php?page=minifier'));
        exit;
    }
}

new HTML_CSS_JS_Minifier_Plugin();

