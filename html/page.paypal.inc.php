<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2025 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    error_reporting(E_ALL);

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    //

    auth::newAuthenticityToken();

    $page_id = "update";

    $css_files = array("my.css");
    $page_title = APP_TITLE;

    include_once("html/common/site_header.inc.php");
?>

<body class="remind-page sn-hide">

    <?php

        include_once("html/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">
        <div class="main-column">
            <div class="main-content">

                <div class="standard-page">

                    <h1>Hello world, This is PayPal Integration with Javascript</h1>

                    <input id="donate-amount" type="Enter Amount">
                    <div id="paypal-button-container">
                    </div>

                </div>

            </div>
        </div>

    </div>

    <?php

        include_once("html/common/site_footer.inc.php");
    ?>

    <script src="https://www.paypal.com/sdk/js?client-id=ASNyRNLvTsew99J-NqYnsYWXGmGbO-1bfH44syvqo4XqbsT3BVsEPKDgwrsg0nT_68Fmu9k31PXMUGKZ" ></script>

    <script>

        function initPayPalButton() {
            paypal
                .Buttons({
                    style: {
                        shape: "rect",
                        color: "gold",
                        layout: "vertical",
                        label: "pay",
                        height: 33,
                        borderRadius: 3,
                        disableMaxWidth: true,
                    },

                    createOrder: function (data, actions) {

                        return actions.order.create({
                            purchase_units: [
                                { amount: { currency_code: "USD", value: 10 } },
                            ],
                        });
                    },

                    onApprove: function (data, actions) {
                        return actions.order.capture().then(function (orderData) {
                            // Full available details
                            console.log(
                                "Capture result",
                                orderData,
                                JSON.stringify(orderData, null, 2)
                            );

                            // Show a success message within this page, for example:
                            const element = document.getElementById("paypal-button-container");
                            element.innerHTML = "";
                            element.innerHTML = "<h3>Thank you for your payment!</h3>";

                            // Or go to another URL:  actions.redirect('thank_you.html');
                        });
                    },

                    onError: function (err) {
                        console.log(err);
                    },
                })
                .render("#paypal-button-container");
        }

        initPayPalButton();

    </script>

</body>
</html>