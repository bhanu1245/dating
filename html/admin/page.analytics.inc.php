<?php

    /*!
     * https://raccoonsquare.com, https://raccoonjohn.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
     */

    if (!admin::isSession()) {

        header("Location: /admin/login");
        exit;
    }

    $analytics = new analytics($dbo);

    $page_id = "analytics";

    $css_files = array("mytheme.css");
    $page_title = "Analytics";

    include_once("html/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

        <?php

            include_once("html/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once("html/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper"> <!-- Page wrapper  -->

            <div class="container-fluid"> <!-- Container fluid  -->

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>

                <div class="row">

                    <!-- Column -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="lineChartOnline"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <!-- Column -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="lineChartPayments"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                </div>

                <div class="row">

                    <!-- Column -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="lineChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <!-- Column -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="lineChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                </div>

                <div class="row">

                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="pieChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="pieChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="pieChart3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <!-- Column -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body collapse show">
                                <div class="table-responsive" style="min-height: 350px;">
                                    <canvas id="barChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                </div>

            </div> <!-- End Container fluid  -->

            <?php

                include_once("html/common/admin_footer.inc.php");
            ?>

        </div> <!-- End Page wrapper  -->

    </div> <!-- End Main Wrapper -->

    <script>

        $(document).ready(function() {

            // Users Online and activity statistics (Line chart)

            const mCanvasIdChartOnline = "lineChartOnline";

            const szChartOnlineTitle = "Last 30 days: new users and online users";

            let mDataChartOnline = <?php echo json_encode($analytics->getItemsCount(30, "users", "regtime")); ?>;
            let mDataChartOnline2 = <?php echo json_encode($analytics->getItemsCount(30, "users", "last_authorize")); ?>;

            const szDataChartOnline = ["New Registered Users", "Online Users"];
            const colorsChartOnline = ['#ea0a33', '#13ca0c'];

            const datasetsChartOnline = szDataChartOnline.map((label, index) => ({
                label: label,
                data: [mDataChartOnline, mDataChartOnline2][index],
                fill: false,
                borderColor: colorsChartOnline[index],
                tension: 0.1
            }));

            showLineChart(mCanvasIdChartOnline, szChartOnlineTitle, datasetsChartOnline, mDataChartOnline.length);

            // Payments statistics (Line chart)

            const mPaymentsCanvasId = "lineChartPayments";

            const szPaymentsChartTitle = "Last 30 days: Number of purchases by day";

            let mPaymentsData = <?php echo json_encode($analytics->getTimedItemsCountByIntField(30, "payments", "createAt", "paymentType", PT_CARD)); ?>;
            let mPaymentsData2 = <?php echo json_encode($analytics->getTimedItemsCountByIntField(30, "payments", "createAt", "paymentType", PT_GOOGLE_PURCHASE)); ?>;
            let mPaymentsData3 = <?php echo json_encode($analytics->getTimedItemsCountByIntField(30, "payments", "createAt", "paymentType", PT_APPLE_PURCHASE)); ?>;
            let mPaymentsData4 = <?php echo json_encode($analytics->getTimedItemsCountByIntField(30, "payments", "createAt", "paymentType", PT_ADMOB_REWARDED_ADS)); ?>;
            let mPaymentsData5 = <?php echo json_encode($analytics->getTimedItemsCountByIntField(30, "payments", "createAt", "paymentType", PT_PAYPAL)); ?>;

            const szPaymentsData = ["Card", "Google in-app", "Apple in-app", "Admob rewarded ad", "PayPal"];
            const colorsPayments = ['#6366f1', '#10b981', '#b910ab', '#daa82e', '#e42121'];

            const datasetsPayments = szPaymentsData.map((label, index) => ({
                label: label,
                data: [mPaymentsData, mPaymentsData2, mPaymentsData3, mPaymentsData4, mPaymentsData5][index],
                fill: false,
                borderColor: colorsPayments[index],
                tension: 0.1
            }));

            showLineChart(mPaymentsCanvasId, szPaymentsChartTitle, datasetsPayments, mPaymentsData.length);

            // Main statistics (Line chart)

            const mCanvasId = "lineChart1";

            const szChartTitle = "Last 30 days stat";

            let mData = <?php echo json_encode($analytics->getItemsCount(30, "messages", "createAt")); ?>;
            let mData2 = <?php echo json_encode($analytics->getItemsCount(30, "friends", "createAt")); ?>;
            let mData3 = <?php echo json_encode($analytics->getItemsCount(30, "profile_followers", "create_at")); ?>;
            let mData4 = <?php echo json_encode($analytics->getItemsCount(30, "profile_likes", "createAt")); ?>;
            let mData5 = <?php echo json_encode($analytics->getItemsCount(30, "guests", "createAt")); ?>;

            const szData = ["New Messages", "Friends", "Follower requests", "New Profile Likes (Hot game also)", "Profile views"];
            const colors = ['#6366f1', '#10b981', '#b910ab', '#daa82e', '#e42121'];

            const datasets = szData.map((label, index) => ({
                label: label,
                data: [mData, mData2, mData3, mData4, mData5][index],
                fill: false,
                borderColor: colors[index],
                tension: 0.1
            }));

            showLineChart(mCanvasId, szChartTitle, datasets, mData.length);

            // Secondary statistics (Line chart)

            const mCanvasIdChart2 = "lineChart2";

            const szChartTitleChart2 = "Last 30 days stat";

            let mDataChart2 = <?php echo json_encode($analytics->getItemsCount(30, "gifts", "createAt")); ?>;
            let mData2Chart2 = <?php echo json_encode($analytics->getItemsCount(30, "photos", "createAt")); ?>;
            let mData3Chart2 = <?php echo json_encode($analytics->getItemsCount(30, "images_likes", "createAt")); ?>;
            let mData4Chart2 = <?php echo json_encode($analytics->getItemsCount(30, "images_comments", "createAt")); ?>;
            let mData5Chart2 = <?php echo json_encode($analytics->getItemsCount(30, "agora", "createAt")); ?>;

            const szDataChart2 = ["Send Gifts", "Gallery New Uploads", "Gallery Likes", "Gallery Comments", "Agora calls"];
            const colorsChart2 = ['#6366f1', '#10b981', '#b910ab', '#daa82e', '#e42121'];

            const datasetsChart2 = szDataChart2.map((label, index) => ({
                label: label,
                data: [mDataChart2, mData2Chart2, mData3Chart2, mData4Chart2, mData5Chart2][index],
                fill: false,
                borderColor: colorsChart2[index],
                tension: 0.1
            }));

            showLineChart(mCanvasIdChart2, szChartTitleChart2, datasetsChart2, mDataChart2.length);

            // Genders (Pie chart)

            const mCanvasIdPieChart = "pieChart1";

            const szChartTitlePieChart = "Genders";


            const mDataPieChart = [<?php echo json_encode($analytics->getItemsCountByIntField("users", "sex", SEX_MALE)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("users", "sex", SEX_FEMALE)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("users", "sex", SEX_ANY)); ?>];
            const szLabelsPieChart = ["Male", "Female", "Secret"];
            const colorsPieChart = ['#267315', '#b910b1', '#e4a721'];

            showPieChart(mCanvasIdPieChart, szChartTitlePieChart, mDataPieChart, szLabelsPieChart, colorsPieChart);

            // Authorizations (Pie chart)

            const mCanvasIdPieChart2 = "pieChart2";

            const szChartTitlePieChart2 = "Authorizations";


            const mDataPieChart2 = [<?php echo json_encode($analytics->getItemsCountByIntField("access_data", "appType", APP_TYPE_ANDROID)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("access_data", "appType", APP_TYPE_IOS)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("access_data", "appType", APP_TYPE_WEB)); ?>];
            const szLabelsPieChart2 = ["Android app", "iOS app", "Website"];
            const colorsPieChart2 = ['#2259a1', '#e42121', '#daa82e'];

            showPieChart(mCanvasIdPieChart2, szChartTitlePieChart2, mDataPieChart2, szLabelsPieChart2, colorsPieChart2);

            // Account linked to (Pie chart)

            const mCanvasIdPieChart3 = "pieChart3";

            const szChartTitlePieChart3 = "Users linked account to";


            const mDataPieChart3 = [<?php echo json_encode($analytics->getItemsCountByNotEmptyTextField("users", "email")); ?>, <?php echo json_encode($analytics->getItemsCountByNotEmptyTextField("users", "otpPhone")); ?>, <?php echo json_encode($analytics->getItemsCountByNotEmptyTextField("users", "gl_id")); ?>, <?php echo json_encode($analytics->getItemsCountByNotEmptyTextField("users", "ap_id")); ?>];
            const szLabelsPieChart3 = ["Email", "Phone number", "Google Login", "Apple Login"];
            const colorsPieChart3 = ['#498e09', '#b910b1', '#1b10b9', '#daa82e'];

            showPieChart(mCanvasIdPieChart3, szChartTitlePieChart3, mDataPieChart3, szLabelsPieChart3, colorsPieChart3);

            // Account linked to (Pie chart)

            const mBarChartCanvasId = "barChart1";

            const szBarChartTitle = "Users accounts state";


            const mDataBarChart = [<?php echo json_encode($analytics->getItemsCountByIntField("users", "state", ACCOUNT_STATE_ENABLED)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("users", "state", ACCOUNT_STATE_BLOCKED)); ?>, <?php echo json_encode($analytics->getItemsCountByIntField("users", "state", ACCOUNT_STATE_DISABLED)); ?>];
            const szLabelsBarChart = ["Active", "Blocked by admin", "Deleted by user"];
            const backgroundColorsBarChart = ['#d62ece', '#4a3fdd', '#dfb243'];
            const borderColorsBarChart = ['#b910b1', '#1b10b9', '#daa82e'];

            showBarGraph(mBarChartCanvasId, szBarChartTitle, mDataBarChart, szLabelsBarChart, backgroundColorsBarChart, borderColorsBarChart);

        });

        function showBarGraph(mCanvasId, szChartTitle, mData, szLabels, backgroundColors, borderColors) {

            const data = {
                labels: szLabels,
                datasets: [{
                    label: szChartTitle,
                    data: mData,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            };

            // Configuration options
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top', // Position of the legend
                        },
                        title: {
                            display: true,
                            text: szChartTitle // Chart title
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };

            // Render the chart
            var myChart = new Chart(document.getElementById(mCanvasId), config);
        }

        function showPieChart(mCanvasId, szChartTitle, mData, szLabels, colors) {

            const data = {
                labels: szLabels,
                datasets: [{
                    label: szChartTitle,
                    data: mData,
                    backgroundColor: colors,
                    hoverOffset: 4
                }]
            };

            // Configuration options
            const config = {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top', // Position of the legend
                        },
                        title: {
                            display: true,
                            text: szChartTitle // Chart title
                        }
                    }
                },
            };

            // Render the chart
            var myChart = new Chart(document.getElementById(mCanvasId), config);
        }

        function showLineChart(mCanvasId, szChartTitle, datasets, cnt) {

            var DAYS_30 = Array.from({ length: cnt}, (_, i) => {
                if (i === 0) return 'Today';
                if (i === 1) return 'Yesterday';
                return `${i} days ago`;
            }).reverse();

            const labels = DAYS_30;

            const data = {
                labels: labels,
                datasets: datasets
            };

            // Configuration options
            const config = {
                type: 'line', // Specifies the chart type
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top', // Position of the legend
                        },
                        title: {
                            display: true,
                            text: szChartTitle // Chart title
                        }
                    },
                    scales: { // Optional: configure axes
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };

            // Render the chart
            const myChart = new Chart(document.getElementById(mCanvasId), config);
        }

    </script>

</body>

</html>
