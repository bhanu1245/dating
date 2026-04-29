<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    $page_id = "about";

    $css_files = array("my.css");
    $page_title = $LANG['page-about'];

    include_once("html/common/site_header.inc.php");

    ?>

<body class="about-page sn-hide">


    <?php
        include_once("html/common/site_topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <section class="standard-page">

                    <?php

                    if (file_exists("html/about/".$LANG['lang-code'].".inc.php")) {

                        include_once("html/about/".$LANG['lang-code'].".inc.php");

                    } else {

                        include_once("html/about/en.inc.php");
                    }
                    ?>

                </section>

            </div>

        </div>

    </div>

    <?php

        include_once("html/common/site_footer.inc.php");
    ?>


</body
</html>