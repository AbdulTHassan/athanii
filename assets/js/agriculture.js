document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("agricultureForm");
    const result = document.getElementById("agricultureResult");
    const resetButton = document.getElementById("resetAgriculture");

    const amountInput = document.getElementById("harvest_amount");
    const unitSelect = document.getElementById("measurement_unit");
    const customMeasurement = document.getElementById("customMeasurement");
    const customWeight = document.getElementById("custom_weight");
    const conversionPreview = document.getElementById("conversionPreview");
    const convertedWeight = document.getElementById("convertedWeight");


    /*
    |--------------------------------------------------------------------------
    | Convert Measurement To KG
    |--------------------------------------------------------------------------
    */

    function convertToKg() {

        const amount = parseFloat(amountInput.value) || 0;
        const unit = unitSelect.value;
        const weight = parseFloat(customWeight.value) || 0;

        switch (unit) {

            case "kg":
                return amount;

            case "bag50":
                return amount * 50;

            case "bag100":
                return amount * 100;

            case "ton":
                return amount * 1000;

            case "custom":
                return weight > 0 ? amount * weight : 0;

            default:
                return 0;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Number Format
    |--------------------------------------------------------------------------
    */

    function formatNumber(number) {

        return new Intl.NumberFormat("en-US", {
            maximumFractionDigits: 2
        }).format(number);

    }


    /*
    |--------------------------------------------------------------------------
    | Conversion Preview
    |--------------------------------------------------------------------------
    */

    function updateConversion() {

        const amount = parseFloat(amountInput.value) || 0;
        const unit = unitSelect.value;

        if (amount <= 0 || !unit) {

            conversionPreview.classList.add("hidden");

            return;
        }


        if (unit === "custom") {

            const weight =
                parseFloat(customWeight.value) || 0;

            if (weight <= 0) {

                conversionPreview.classList.add("hidden");

                return;
            }
        }


        const kg = convertToKg();

        if (kg <= 0) {

            conversionPreview.classList.add("hidden");

            return;
        }


        convertedWeight.textContent =
            formatNumber(kg) + " kg";

        conversionPreview.classList.remove("hidden");

    }


    /*
    |--------------------------------------------------------------------------
    | Custom Measurement
    |--------------------------------------------------------------------------
    */

    function updateCustomMeasurement() {

        if (unitSelect.value === "custom") {

            customMeasurement.classList.remove("hidden");

        } else {

            customMeasurement.classList.add("hidden");

            customWeight.value = "";
        }

        updateConversion();

    }


    /*
    |--------------------------------------------------------------------------
    | Input Events
    |--------------------------------------------------------------------------
    */

    amountInput.addEventListener(
        "input",
        updateConversion
    );

    unitSelect.addEventListener(
        "change",
        updateCustomMeasurement
    );

    customWeight.addEventListener(
        "input",
        updateConversion
    );


    /*
    |--------------------------------------------------------------------------
    | Form Validation
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        "submit",
        function (event) {

            const amount =
                parseFloat(amountInput.value) || 0;

            const unit =
                unitSelect.value;

            const irrigation =
                document.getElementById("irrigation").value;


            if (amount <= 0) {

                event.preventDefault();

                alert(
                    "Please enter a valid harvest amount."
                );

                amountInput.focus();

                return;
            }


            if (!unit) {

                event.preventDefault();

                alert(
                    "Please select a measurement unit."
                );

                unitSelect.focus();

                return;
            }


            if (unit === "custom") {

                const weight =
                    parseFloat(customWeight.value) || 0;

                if (weight <= 0) {

                    event.preventDefault();

                    alert(
                        "Please enter the weight of one custom unit."
                    );

                    customWeight.focus();

                    return;
                }
            }


            if (!irrigation) {

                event.preventDefault();

                alert(
                    "Please select an irrigation method."
                );

                document
                    .getElementById("irrigation")
                    .focus();

                return;
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    |
    | Reload the Agriculture page completely.
    | This removes PHP POST result and all form data.
    |
    */

    if (resetButton) {

        resetButton.addEventListener(
            "click",
            function (event) {

                event.preventDefault();

                window.location.href =
                    window.location.pathname +
                    "?type=agriculture";

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | AUTOMATIC SCROLL TO RESULT
    |--------------------------------------------------------------------------
    |
    | If PHP has generated the result section,
    | automatically scroll to it.
    |
    */

    if (result) {

        setTimeout(function () {

            result.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        }, 500);

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateCustomMeasurement();

});