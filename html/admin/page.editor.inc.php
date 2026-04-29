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

    $error = false;
    $error_message = '';

    $id = "terms";
    $title = "Edit Terms";
    $file = 'html/terms/en.inc.php';
    $url = '/terms';


    if (isset($_GET['id'])) {

        $id = isset($_GET['id']) ? $_GET['id'] : "";
        $data = isset($_GET['data']) ? $_GET['data'] : "";
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : "";

        switch ($id) {

            case "terms": {

                $id = "terms";
                $title = "Edit Terms";
                $file = 'html/terms/en.inc.php';
                $url = '/terms';

                break;
            }

            case "about": {

                $id = "about";
                $title = "Edit About";
                $file = 'html/about/en.inc.php';
                $url = '/about';

                break;
            }

            case "gdpr": {

                $id = "gdpr";
                $title = "Edit Gdpr";
                $file = 'html/gdpr/en.inc.php';
                $url = '/gdpr';

                break;
            }

            case "privacy": {

                $id = "privacy";
                $title = "Edit Privacy";
                $file = 'html/privacy/en.inc.php';
                $url = '/privacy';

                break;
            }

            default: {

                $id = "terms";
                $title = "Edit Terms";
                $file = 'html/terms/en.inc.php';
                $url = '/terms';

                break;
            }
        }
    }

    if (!empty($_POST)) {

        $data = isset($_POST['data']) ? $_POST['data'] : "";
        $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : "";
        $id_ = isset($_POST['id']) ? $_POST['id'] : "";

        switch ($id_) {

            case "terms": {

                $file_ = 'html/terms/en.inc.php';

                break;
            }

            case "about": {

                $file_ = 'html/about/en.inc.php';

                break;
            }

            case "gdpr": {

                $file_ = 'html/gdpr/en.inc.php';

                break;
            }

            case "privacy": {

                $file_ = 'html/privacy/en.inc.php';

                break;
            }

            default: {

                $file_ = 'html/terms/en.inc.php';

                break;
            }
        }

        if ($accessToken === admin::getAccessToken() && $admin_info['access_level'] < ADMIN_ACCESS_LEVEL_MODERATOR_RIGHTS) {

            file_put_contents($file_, $data);
        }

        exit;
    }

    helper::newAuthenticityToken();

    $page_id = $id;

    $css_files = array("mytheme.css");
    $page_title = $title;

    include_once("html/common/admin_header.inc.php");
?>

<style>
    .ck.ck-editor {

        width: 100%;
    }
</style>

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
                            <li class="breadcrumb-item active"><?php echo $title; ?></li>
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
                    <div class="col-sm-12">
                        <div class="card card-body">

                            <h4 class="card-title"><?php echo $title; ?></h4>
                            <div>
                                <a href="<?php echo $url; ?>" target="_blank" class="btn btn-info text-uppercase waves-effect waves-light">Open page in browser (New Tab)</a>
                                <button class="btn btn-info text-uppercase waves-effect waves-light" onclick="saveData();">Save</button>
                            </div>
                            <br>


                            <?php


                            $data = file_get_contents($file, true);

                            $app_title = APP_TITLE;
                            $app_name = APP_NAME;
                            $app_url = APP_URL;

                            $data = str_replace('<?php echo APP_TITLE; ?>', $app_title, $data);
                            $data = str_replace('<?php echo APP_NAME; ?>', $app_name, $data);
                            $data = str_replace('<?php echo APP_URL; ?>', $app_url, $data);

                            ?>

                            <div class="form-material" action="/admin/app" method="post">
                                <div class="form-group">
                                    <div id="editor" style="width: 100%">
                                        <p><?= $data ?></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div> <!-- End Container fluid  -->

            <?php

                include_once("html/common/admin_footer.inc.php");
            ?>

            <script type="importmap">
                {
                    "imports": {
                        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
                        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/"
                    }
                }
            </script>

            <script type="module">
                import {
                    ClassicEditor,
                    AccessibilityHelp,
                    Autoformat,
                    AutoLink,
                    Autosave,
                    Bold,

                    Essentials,
                    GeneralHtmlSupport,
                    Heading,
                    HtmlComment,
                    Italic,
                    Link,
                    Paragraph,
                    SelectAll,
                    SourceEditing,
                    SpecialCharacters,
                    SpecialCharactersArrows,
                    SpecialCharactersCurrency,
                    SpecialCharactersEssentials,
                    SpecialCharactersLatin,
                    SpecialCharactersMathematical,
                    SpecialCharactersText,
                    TextTransformation,
                    Underline,
                    Undo
                } from 'ckeditor5';

                const editorConfig = {
                    width: '75%',
                    toolbar: {
                        items: [
                            'undo',
                            'redo',
                            '|',
                            'sourceEditing',
                            '|',
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'underline',

                            '|',
                            'specialCharacters',
                            'link'
                        ],
                        shouldNotGroupWhenFull: false
                    },
                    plugins: [
                        AccessibilityHelp,
                        Autoformat,
                        AutoLink,
                        Autosave,
                        Bold,
                        Essentials,
                        GeneralHtmlSupport,
                        Heading,
                        HtmlComment,
                        Italic,
                        Link,
                        Paragraph,
                        SelectAll,
                        SourceEditing,
                        SpecialCharacters,
                        SpecialCharactersArrows,
                        SpecialCharactersCurrency,
                        SpecialCharactersEssentials,
                        SpecialCharactersLatin,
                        SpecialCharactersMathematical,
                        SpecialCharactersText,
                        TextTransformation,
                        Underline,
                        Undo
                    ],
                    heading: {
                        options: [
                            {
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            }
                        ]
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /^.*$/,
                                styles: true,
                                attributes: true,
                                classes: true
                            }
                        ]
                    },
                    link: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        decorators: {
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    }
                };

                ClassicEditor.create(document.querySelector('#editor'), editorConfig);
            </script>

            <script>

                function saveData() {



                    $('.ck-content').find('[data-cke-filler]').last().parent().remove();
                    $('.ck-content').find('[data-cke-filler]').first().parent().remove();
                    $('.ck-content').find("[data-placeholder]").remove();
                    var htmldata = $('.ck-content').html();

                    //alert(htmldata);

                    $.ajax({
                        type: 'POST',
                        url: '/admin/editor',
                        data: 'data=' + htmldata + '&access_token=' + "<?php echo admin::getAccessToken(); ?>" + "&id=" + "<?php echo $id; ?>",
                        timeout: 30000,
                        success: function (response) {

                            //
                            alert("Changes saved! Click \"Open in browser\" if you want to see the changes on the site page.")
                        },
                        error: function (xhr, type) {

                            alert("Error, something went wrong...")
                        }
                    });
                }

            </script>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>

<?php

