<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2025 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

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
    $ufake = new ufake($dbo);
    $phone = new phone($dbo);

    //

    $us_eu_girls_directory = 'ufake/us-eu-girls';
    $us_eu_girls_names_file = 'ufake/us-girl-names.txt';
    $us_eu_girls_surnames_file = 'ufake/us-surnames.txt';
    $us_eu_girls_photos = [];
    $us_eu_girls_surnames = [];
    $us_eu_girls_names = [];

    $us_eu_mens_directory = 'ufake/us-eu-mens';
    $us_eu_mens_names_file = 'ufake/us-men-names.txt';
    $us_eu_mens_surnames_file = 'ufake/us-surnames.txt';
    $us_eu_mens_photos = [];
    $us_eu_mens_surnames = [];
    $us_eu_mens_names = [];

    $us_eu_girls_photos = $ufake->getFileNames($us_eu_girls_directory);
    $us_eu_girls_names = $ufake->getFileLines($us_eu_girls_names_file);
    $us_eu_girls_surnames = $ufake->getFileLines($us_eu_girls_surnames_file);

    $us_eu_mens_photos = $ufake->getFileNames($us_eu_mens_directory);
    $us_eu_mens_names = $ufake->getFileLines($us_eu_mens_names_file);
    $us_eu_mens_surnames = $ufake->getFileLines($us_eu_mens_surnames_file);

    $us_locations_file = 'ufake/us-locations.txt';
    $us_locations = $ufake->getFileLines($us_locations_file);

    $ageFrom = 18;
    $ageTo = 32;

    //

    $error = false;
    $error_message = '';

    if (isset($_GET['action'])) {

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        $action = helper::clearText($action);
        $action = helper::escapeText($action);

        $id = helper::clearInt($id);

        if ($accessToken === admin::getAccessToken() && $admin_info['access_level'] < ADMIN_ACCESS_LEVEL_MODERATOR_RIGHTS) {

            switch($action) {

                case 'deactivate': {

                    $ufake->deactivateAll();

                    break;
                }

                default: {

                    break;
                }
            }

            header("Location: /admin/ufake");
            exit;
        }
    }

    if (!empty($_POST)) {

        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $f_gender = isset($_POST['f_gender']) ? $_POST['f_gender'] : 0;
        $f_count = isset($_POST['f_count']) ? $_POST['f_count'] : 0;

        $f_gender = helper::clearInt($f_gender);
        $f_count = helper::clearInt($f_count);

        if ($authToken === helper::getAuthenticityToken() && $admin_info['access_level'] < ADMIN_ACCESS_LEVEL_MODERATOR_RIGHTS) { //&& !APP_DEMO

            if ($f_count != 0) {

                if ($f_gender == 0) {

                    $ufake->createAccounts($us_locations, $us_eu_mens_names, $us_eu_mens_surnames, $us_eu_mens_photos, 'us-eu-mens', $ageFrom, $ageTo, SEX_MALE, $f_count);

                } else {

                    $ufake->createAccounts($us_locations, $us_eu_girls_names, $us_eu_girls_surnames, $us_eu_girls_photos, 'us-eu-girls', $ageFrom, $ageTo, SEX_FEMALE, $f_count);
                }
            }
        }

        header("Location: /admin/ufake");
        exit;
    }

    $page_id = "ufake";

    helper::newAuthenticityToken();

    $css_files = array("mytheme.css");
    $page_title = "Fake Accounts | Admin Panel";

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
                            <li class="breadcrumb-item active">Fake Accounts</li>
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

                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">

                                <?php

                                    $accounts_m = $ufake->getAccountsCountByGender(0);
                                    $accounts_f = $ufake->getAccountsCountByGender(1);
                                ?>

                                <h4 class="card-title">Info About Fake Accounts (Active In Project Database)</h4>
                                <h6 class="card-subtitle">Active Fake Accounts (Men): <?php echo $accounts_m; ?></h6>
                                <h6 class="card-subtitle">Active Fake Accounts (Women): <?php echo $accounts_f; ?></h6>

                                <?php

                                    if ($accounts_m != 0 || $accounts_f != 0) {

                                        ?>
                                            <div class="form-group mb-0">
                                                <div class="col-xs-12">
                                                    <a href="/admin/ufake?action=deactivate&access_token=<?php echo admin::getAccessToken(); ?>" class="btn btn-info text-uppercase waves-effect waves-light" type="submit">Deactivate all</a>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                ?>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">Info About Men Fake Accounts (Source Database)</h4>
                                <h6 class="card-subtitle">Photos count: <?php echo count($us_eu_mens_photos); ?></h6>
                                <h6 class="card-subtitle">Names: <?php echo count($us_eu_mens_names); ?></h6>
                                <h6 class="card-subtitle">SurNames: <?php echo count($us_eu_mens_surnames); ?></h6>

                                <h4 class="card-title">Info About Women Fake Accounts (Source Database)</h4>
                                <h6 class="card-subtitle">Photos count: <?php echo count($us_eu_girls_photos); ?></h6>
                                <h6 class="card-subtitle">Names: <?php echo count($us_eu_girls_names); ?></h6>
                                <h6 class="card-subtitle">SurNames: <?php echo count($us_eu_girls_surnames); ?></h6>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Create Fake Accounts</h4>
                                <h6 class="card-subtitle hide">Some tips:</h6>

                                <form class="form-material m-t-40"  method="post" action="/admin/ufake" enctype="multipart/form-data">

                                    <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="form-control" name="f_gender">
                                            <option value="0" selected="selected">Men</option>
                                            <option value="1">Women</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="f_count" class="active">Count (for example: 100)</label>
                                        <input class="form-control" id="f_count" type="number" size="3" name="f_count" value="10">
                                    </div>

                                    <div class="form-group mb-0">
                                        <div class="col-xs-12">
                                            <button class="btn btn-info text-uppercase waves-effect waves-light" type="submit">Create</button>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">How to add/change data to create fake accounts? (Source Database)</h4>
                                <h6 class="card-subtitle">Photos of women: Remove or add jpg files to the directory "/ufake/us-eu-girls"</h6>
                                <h6 class="card-subtitle">Photos of men: Remove or add jpg files to the directory "/ufake/us-eu-mens"</h6>
                                <h6 class="card-subtitle">Names of women: Remove or add lines in text file "/ufake/us-girl-names.txt"</h6>
                                <h6 class="card-subtitle">Names of women: Remove or add lines in text file "/ufake/us-men-names.txt"</h6>
                                <h6 class="card-subtitle">SurNames of women and men: Remove or add lines in text file "/ufake/us-surnames.txt"</h6>
                                <h6 class="card-subtitle">Locations of women and men: Remove or add lines in text file "/ufake/us-locations.txt"</h6>

                                <h4 class="card-title">Authorization in a fake account</h4>
                                <h6 class="card-subtitle">It is possible to log into any fake account from the application</h6>
                                <h6 class="card-subtitle">For login use username (without @ symbol, can be seen in the account profile under the name)</h6>
                                <h6 class="card-subtitle">For the password, use username + 111</h6>
                                <h6 class="card-subtitle">Example: if the username of the fake account is "@uf12345677f", then the login is "uf12345677f" and the password is "uf12345677f111"</h6>

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

