<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2025 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (!admin::isSession()) {

        header("Location: /admin/login");
        exit;
    }

    // Administrator info

    $admin = new admin($dbo);
    $admin->setId(admin::getCurrentAdminId());

    $admin_info = $admin->get();

    //

    $stats = new stats($dbo);
    $settings = new settings($dbo);

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $paypal_enabled = isset($_POST['paypal_enabled']) ? $_POST['paypal_enabled'] : 0;
        $paypal_client_id = isset($_POST['paypal_client_id']) ? $_POST['paypal_client_id'] : "";
        $paypal_secret_key = isset($_POST['paypal_secret_key']) ? $_POST['paypal_secret_key'] : "";
        $paypal_sandbox_url = isset($_POST['paypal_sandbox_url']) ? $_POST['paypal_sandbox_url'] : "";
        $paypal_currency = isset($_POST['paypal_currency']) ? $_POST['paypal_currency'] : "USD";
        $paypal_mode = isset($_POST['paypal_mode']) ? $_POST['paypal_mode'] : 0;
        $paypal_count = isset($_POST['paypal_count']) ? $_POST['paypal_count'] : 250;
        $paypal_price = isset($_POST['paypal_price']) ? $_POST['paypal_price'] : 5;

        if (admin::getAccessToken() === $access_token && $admin_info['access_level'] < ADMIN_ACCESS_LEVEL_MODERATOR_RIGHTS) {

            $settings->setValue("paypal_enabled", $paypal_enabled);
            $settings->setValue("paypal_client_id", 0, $paypal_client_id);
            $settings->setValue("paypal_secret_key", 0, $paypal_secret_key);
            $settings->setValue("paypal_sandbox_url", 0, $paypal_sandbox_url);
            $settings->setValue("paypal_currency", 0, $paypal_currency);
            $settings->setValue("paypal_mode", $paypal_mode);
            $settings->setValue("paypal_count", $paypal_count);
            $settings->setValue("paypal_price", $paypal_price);

            header("Location: /admin/payments");
            exit;
        }
    }

    //

    $settings_result = $settings->get();

    //

    $page_id = "payments";

    $error = false;
    $error_message = '';

    $css_files = array("mytheme.css");
    $page_title = "Payments Settings";

    include_once("html/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">

    <div id="main-wrapper">

        <?php

            include_once("html/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once("html/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper">

            <div class="container-fluid">

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/main">Home</a></li>
                            <li class="breadcrumb-item active">Payments Settings</li>
                        </ol>
                    </div>
                </div>

                <?php

                    if (!$admin_info['error'] && $admin_info['access_level'] > ADMIN_ACCESS_LEVEL_READ_WRITE_RIGHTS) {

                        ?>
                        <div class="card">
                            <div class="card-body collapse show">
                                <h4 class="card-title">Warning!</h4>
                                <p class="card-text">Your account does not have rights to make changes in this section! The changes you've made will not be saved.</p>
                            </div>
                        </div>
                        <?php
                    }
                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Payments Settings</h4>
                                <h6 class="card-subtitle">How to setup PayPal: <a class="link" href="https://raccoonsquare.com/help/how_to_setup_paypal/" target="_blank">https://raccoonsquare.com/help/how_to_setup_paypal/</a></h6>

                                <?php

                                // $settings_result['S3_AMAZON']['intValue'] == 1

                                ?>

                                <form action="/admin/payments" method="post" class="form-material">

                                    <input type="hidden" name="access_token" value="<?php echo admin::getAccessToken(); ?>">

                                    <div class="form-group">
                                        <h4>PayPal</h4>
                                    </div>

                                    <div class="form-group">
                                        <label>PayPal Enabled</label>
                                        <select class="form-control" name="paypal_enabled">
                                            <option <?php if ($settings_result['paypal_enabled']['intValue'] == 0) echo 'selected="selected"'; ?> value="0">Disabled</option>
                                            <option <?php if ($settings_result['paypal_enabled']['intValue'] == 1) echo 'selected="selected"'; ?> value="1">Enabled</option>
                                        </select>
                                    </div>

                                    <?php

                                    $paypal_client_id = $settings_result['paypal_client_id']['textValue'];

                                    if ($admin_info['access_level'] > ADMIN_ACCESS_LEVEL_READ_WRITE_RIGHTS && strlen($paypal_client_id) > 10) {

                                        $paypal_client_id = "*****".substr($paypal_client_id, -10);
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="paypal_client_id" class="active">PayPal Client ID</label>
                                        <input class="form-control" id="paypal_client_id" type="text" size="512" name="paypal_client_id" value="<?php echo $paypal_client_id; ?>">
                                    </div>

                                    <?php

                                    $paypal_secret_key = $settings_result['paypal_secret_key']['textValue'];

                                    if ($admin_info['access_level'] > ADMIN_ACCESS_LEVEL_READ_WRITE_RIGHTS && strlen($paypal_secret_key) > 10) {

                                        $paypal_secret_key = "*****".substr($paypal_secret_key, -10);
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="paypal_secret_key" class="active">PayPal Secret Key</label>
                                        <input class="form-control" id="paypal_secret_key" type="text" size="512" name="paypal_secret_key" value="<?php echo $paypal_secret_key; ?>">
                                    </div>

                                    <div class="form-group d-none">
                                        <label for="paypal_sandbox_url" class="active">PayPal Sandbox URL</label>
                                        <input class="form-control" id="paypal_sandbox_url" type="text" size="512" name="paypal_sandbox_url" value="<?php echo $settings_result['paypal_sandbox_url']['textValue']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>PayPal Mode</label>
                                        <select class="form-control" name="paypal_mode">
                                            <option <?php if ($settings_result['paypal_mode']['intValue'] == 0) echo 'selected="selected"'; ?> value="0">SANDBOX</option>
                                            <option <?php if ($settings_result['paypal_mode']['intValue'] == 1) echo 'selected="selected"'; ?> value="1">LIVE</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>PayPal Currency</label>
                                        <select class="form-control" name="paypal_currency">
                                            <option <?php if ($settings_result['paypal_currency']['textValue'] === "USD") echo 'selected="selected"'; ?> value="USD">US Dollar (USD)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="paypal_count" class="active">Count (virtual currency)</label>
                                        <input class="form-control" id="paypal_count" type="number" size="6" name="paypal_count" value="<?php echo $settings_result['paypal_count']['intValue']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="paypal_price" class="active">Price (format: 1 = 1; example: 1$ = 1)</label>
                                        <input class="form-control" id="paypal_price" type="number" size="6" name="paypal_price" value="<?php echo $settings_result['paypal_price']['intValue']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <button class="btn btn-info text-uppercase waves-effect waves-light" type="submit">Save</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>



            </div> <!-- End Container fluid  -->

            <?php

                include_once("html/common/admin_footer.inc.php");
            ?>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>