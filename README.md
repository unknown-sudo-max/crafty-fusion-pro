<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <title>Crafty Fusion Pro</title>
   <style>
      .img-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            text-align: center;
        }
        .img-popup img {
            max-width: 80%;
            max-height: 80%;
            margin: auto;
            margin-top: 3%;
        }

        .img-popup .caption {
            color: white;
            font-size: 16px;
            margin-top: 10px;
        }

        .src-img{
          width: 100%;
        }
        .closebtn:hover{

          color: white;

        }

    body {
      background-color: #f7f8fa;
      font-family: 'Helvetica Neue', sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      background-color: #0056b3;
      color: #ffffff;
      text-align: center;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
      font-size: 2.5rem;
      animation: fadeInDown 1s;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translate3d(0, -100%, 0);
      }
      to {
        opacity: 1;
        transform: none;
      }
    }

    .plugin-info {
      background-color: #ffffff;
      border: 1px solid #e0e0e0;
      border-radius: 5px;
      padding: 20px;
      margin-top: 20px;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
      transition: box-shadow 0.3s ease-in-out;
    }

    .plugin-info:hover {
      box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.1);
    }

    .logo {
  text-align: center;
  margin-bottom: 20px;
  animation: pulse 2s infinite;
}

.logo img {
  max-width: 150px;
  height: auto;
  border-radius: 30%;
  border: 5px solid #0056b3;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s;
}

.logo img:hover {
  transform: scale(1.1);
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}


    .btn {
      background-color: #0056b3;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 15px 30px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 1rem;
      margin: 10px 5px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn:hover {
      background-color: #004494;
      transform: translateY(-2px);
    }

    h2 {
      color: #0056b3;
      font-size: 1.5rem;
      margin-top: 20px;
      margin-bottom: 10px;
    }

    ul {
      list-style: disc;
      padding-left: 20px;
    }

    p {
      line-height: 1.6;
      color: #333;
    }

    a {
      text-decoration: none;
      color: #0056b3;
      transition: color 0.3s;
    }

    a:hover {
      color: #004494;
    }

    /* Additional styles for tabbed interface */
    .tab-menu {
      display: flex;
      justify-content: space-around;
      margin-top: 20px;
    }

    .tab-menu {
      display: flex;
      background-color: #fff;
      border-bottom: 1px solid #ccc;
      padding: 10px 0;
    }

    .tab-link {
      cursor: pointer;
      padding: 10px 20px;
      color: #0056b3;
      text-align: center;
      text-decoration: none;
      transition: background-color 0.3s;
      border: 1px solid transparent;
      border-radius: 5px 5px 0 0;
    }

    .tab-link.active {
      background-color: #fff;
      border-color: #ccc;
      border-bottom: 1px solid #fff;
      border-radius: 5px 5px 0 0;
    }

    .tab-content {
      display: none;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 0 0 5px 5px;
    }

    .tab-content.active {
      display: block;
    }

    /* Responsive adjustments for phones and tablets */
    @media (max-width: 992px) {
      .tab-menu {
        display: none;
      }

      .tab-link {
        display: block;
        text-align: center;
        padding: 10px 0;
        border: none;
        border-bottom: 1px solid #ccc;
      }
      .tab-content {
      display: block;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 0 0 5px 5px;
    }
    }

    @media (max-width: 576px) {
      .container {
        padding: 10px;
      }

      h1 {
        font-size: 1.8rem;
      }

      .btn {
        padding: 10px 20px;
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo">
        <img src="https://unknown-sudo-max.github.io/crafty-fusion-pro/cdn/img/logo.png" alt="Plugin Logo">
      </div>
      <h1>Crafty Fusion Pro</h1>
    </div>
    <div class="plugin-info">
      <!-- Tab Navigation Menu -->
      <div class="tab-menu">
        <a href="javascript:void(0)" class="tab-link active" onclick="showTab('tab1')">Crafty Fusion Pro</a>
        <a href="javascript:void(0)" class="tab-link" onclick="showTab('tab2')">Description</a>
        <a href="javascript:void(0)" class="tab-link" onclick="showTab('tab3')">Shortcuts</a>
        <a href="javascript:void(0)" class="tab-link" onclick="showTab('tab4')">What's New</a>
        <a href="javascript:void(0)" class="tab-link" onclick="showTab('tab5')">Screenshots</a>
        <a href="javascript:void(0)" class="tab-link" onclick="showTab('tab6')">Coming soon</a>
      </div>

      <!-- Tab Content Sections -->
      <div id="tab1" class="tab-content active">
        <h2>Crafty Fusion Pro</h2>
        <p>M_G_X Softwares</p>
        <p><strong>Version:</strong> 3.9</p>
        <p><strong>Author:</strong> !-CODE | M_G_X CEO & Founder</p>
        <p><strong>License:</strong> !-CODE LICENSE-AGREEMENT</p>
        <p><strong>Text-Domain:</strong> crafty-fusion-pro</p>
        <a href="https://unknown-sudo-max.github.io/zone/!-CODE/LICENSE-AGREEMENT.html" class="btn" target="_blank">View License Agreement</a>
      </div>

      <div id="tab2" class="tab-content">
        <h2>Description</h2>
        <p>Seamlessly Unleash the Power of Crafty Fusion Pro to elevate your WordPress multitasking capabilities, With blending advanced features to enhance your website's performance and user experience</p>
      </div>

      <div id="tab3" class="tab-content">
        <h2>Shortcuts</h2>
        <ul>
          <li>[mgx_custom_form_with_category]</li>
          <li>[mgx_custom_form]</li>
          <li>[mgx_contact_with_us_form]</li>
          <li>[mgx_page_excerpt]</li>
          <li>/#warranty-activation</li>
          <li>/#contact-us</li>
        </ul>
      </div>

      <div id="tab4" class="tab-content">
        <h2>What's New</h2>
        <ul>
          <li>The forms design enhanced</li>
          <li>Added id for the forms to redirect</li>
          <li>License updates</li>
          <li>SMTP Server online connect</li>
          <li>Plugin Activation</li>
          <li>Help Center</li>
          <li>Auto Update mode</li>
          <li>Tables to access your DB</li>
          <li>Added a Chat Widget</li>
        </ul>
      </div>

     <div id="tab5" class="tab-content">
    <h2>Screenshots</h2>
    <ul>
            <li><a href="javascript:void(0);" onclick="showImage('https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP.PNG', 'Click on Settings')"><img class="src-img" src="https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP.PNG" alt="Screenshot 1"></a><p style="text-align: center;">Click on Settings</p></li><br/>
            <li><a href="javascript:void(0);" onclick="showImage('https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP2.PNG', 'Activate the plugin')"><img class="src-img" src="https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP2.PNG" alt="Screenshot 2"></a><p style="text-align: center;">Activate the plugin</p></li><br/>
            <li><a href="javascript:void(0);" onclick="showImage('https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP3.PNG', 'Enable or disable the SMTP server Connect')"><img class="src-img" src="https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP3.PNG" alt="Screenshot 3"></a><p style="text-align: center;">Enable or disable the SMTP server Connect</p></li><br/>
            <li><a href="javascript:void(0);" onclick="showImage('https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP4.PNG', 'Check your activation is done')"><img class="src-img" src="https://unknown-sudo-max.github.io/crafty-fusion-pro/assets/CFP4.PNG" alt="Screenshot 4"></a><p style="text-align: center;">Check your activation is done</p></li><br/>
        </ul>
</div>

<div id="imgPopup" class="img-popup">
        <span onclick="closeImage()" style="position: absolute; top: 10px; right: 20px; font-size: 30px; cursor: pointer;" class="closebtn">&times;</span>
        <img id="popupImage" src="" alt="Popup Image">
        <p id="popupCaption" class="caption"></p>
    </div>


      <div id="tab6" class="tab-content">
        <h2>Coming soon - v 4.0</h2>
        <ul>
          <li>Access to Form configuration</li>
          <li>Access to the plugin general</li>
          <li>Link Checker</li>   
          <li>New branch in Help Center > Mail us</li>
        </ul>
      </div>
    </div>
  </div>

  <script>
    function showTab(tabId) {
      // Hide all tab content sections
      const tabContents = document.querySelectorAll('.tab-content');
      tabContents.forEach((tab) => {
        tab.classList.remove('active');
      });

      // Show the selected tab content
      document.getElementById(tabId).classList.add('active');

      // Update the active tab link
      const tabLinks = document.querySelectorAll('.tab-link');
      tabLinks.forEach((link) => {
        link.classList.remove('active');
      });
      document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
    }

    // Initially, show the first tab content
    showTab('tab1');



     // JavaScript functions for showing and closing the image popup
        function showImage(imageUrl,caption) {
            document.getElementById('popupImage').src = imageUrl;
             document.getElementById('popupCaption').textContent = caption;
            document.getElementById('imgPopup').style.display = 'block';
        }

        function closeImage() {
            document.getElementById('imgPopup').style.display = 'none';
        }
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
