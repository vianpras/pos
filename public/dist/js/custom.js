/**
 * @author Abdo-Hamoud <abdo.host@gmail.com>
 * https://github.com/Abdo-Hamoud/bootstrap-show-password
 * version: 1.0
 */

!(function ($) {
    //eyeOpenClass: 'fa-eye',
    //eyeCloseClass: 'fa-eye-slash',
    "use strict";

    $(function () {
        $('[data-toggle="password"]').each(function () {
            var input = $(this);
            var eye_btn = $(this).parent().find(".input-group-text");
            eye_btn.css("cursor", "pointer").addClass("input-password-hide");
            eye_btn.on("click", function () {
                if (eye_btn.hasClass("input-password-hide")) {
                    eye_btn
                        .removeClass("input-password-hide")
                        .addClass("input-password-show");
                    eye_btn
                        .find(".fa")
                        .removeClass("fa-eye")
                        .addClass("fa-eye-slash");
                    input.attr("type", "text");
                } else {
                    eye_btn
                        .removeClass("input-password-show")
                        .addClass("input-password-hide");
                    eye_btn
                        .find(".fa")
                        .removeClass("fa-eye-slash")
                        .addClass("fa-eye");
                    input.attr("type", "password");
                }
            });
        });
    });
})(window.jQuery);
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

const chartDonut = (selector, label, datasets, typeTooltips) => {
    $(function () {
        "use strict";
        // generate Donut
        var donutChartCanvas = $(selector).get(0).getContext("2d");

        var donutData = {
            labels: label,
            datasets: datasets,
        };
        var donutOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: false,
            },

            animation: {
                animateScale: true,
                animateRotate: true,
            },
            tooltips: {
                // mode: "index",
                // intersect: false,
                xAlign: "center",
                yAlign: "center",
                callbacks: {
                    label: function (tooltipItem, data) {
                        let title = data.labels[tooltipItem.index];
                        let _value =
                            data.datasets[tooltipItem.datasetIndex].data[
                                tooltipItem.index
                            ];
                        let value = "";
                        let result = "";
                        switch (typeTooltips) {
                            case "currency":
                                value = _value.toLocaleString("id-id", {
                                    style: "currency",
                                    currency: "IDR",
                                });
                                result = title.toUpperCase() + " : " + value;

                                break;

                            case "number":
                                value = formatNumber(_value);
                                result = title.toUpperCase() + " : " + value;

                                break;

                            case "percent":
                                //ambil dataset
                                var dataset =
                                    data.datasets[tooltipItem.datasetIndex];
                                //total dataset
                                var total = dataset.data.reduce(function (
                                    previousValue,
                                    currentValue,
                                    currentIndex,
                                    array
                                ) {
                                    return previousValue + currentValue;
                                });
                                //ambil item dataset
                                var currentValue =
                                    dataset.data[tooltipItem.index];
                                //persentase dataset per item
                                var percentage = Math.floor(
                                    (currentValue / total) * 100 + 0.5
                                );

                                result =
                                    title.toUpperCase() + " : " + percentage;

                                break;

                            default:
                                return "";
                                break;
                        }
                        return result;
                    },
                },
            },
        };
        new Chart(donutChartCanvas, {
            type: "doughnut",
            data: donutData,
            options: donutOptions,
        });
    });
};
const chartLine = (selector, label, datasets, fCurrency) => {
    $(function () {
        "use strict";

        var salesChartCanvas = $(selector).get(0).getContext("2d");
        var salesChartData = {
            labels: label,
            datasets: datasets,
        };

        var salesChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: true,
                fontStyle: "bold",

                labels: {
                    color: "#454D55",
                    generateLabels: function (chart) {
                        let labels =
                            Chart.defaults.global.legend.labels.generateLabels(
                                chart
                            );
                        let result = [];
                        labels.forEach((label) => {
                            var objct = label;
                            var labelText = label.text.toUpperCase();
                            objct.text = labelText;
                            result.push(objct);
                        });
                        return result;
                    },
                },
            },

            tooltips: {
                mode: "index",
                intersect: false,
                xAlign: "center",
                yAlign: "center",
                callbacks: {
                    label: function (tooltipItem, data) {
                        let title =
                            data.datasets[tooltipItem.datasetIndex].label;
                        let value = "";
                        if (fCurrency) {
                            value = tooltipItem.yLabel.toLocaleString("id-id", {
                                style: "currency",
                                currency: "IDR",
                            });
                        } else {
                            value = formatNumber(tooltipItem.yLabel);
                        }
                        let result = title.toUpperCase() + " : " + value;
                        return result;
                    },
                },
            },

            scales: {
                xAxes: [
                    {
                        // display: true,
                        gridLines: {
                            display: false,
                        },
                    },
                ],
                yAxes: [
                    {
                        ticks: {
                            callback: function (value, index, values) {
                                let valueyAxes = "";
                                if (fCurrency) {
                                    valueyAxes = value.toLocaleString("id-id", {
                                        style: "currency",
                                        currency: "IDR",
                                    });
                                } else {
                                    valueyAxes = formatNumber(value);
                                }
                                // let result = value;
                                return valueyAxes;
                            },
                        },
                        gridLines: {
                            display: false,
                        },
                    },
                ],
            },
        };

        var salesChart = new Chart(salesChartCanvas, {
            type: "line",
            data: salesChartData,
            options: salesChartOptions,
        });
    });
};
function preview_image(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById("output_image");
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
function canvasDestroy(chartDiv, chartCanvas) {
    $(chartDiv).empty();
    $(chartDiv).append(
        '<canvas id="' +
            chartCanvas +
            '" height="200" style="height: 200px;"></canvas>'
    );
}
function collapseCard(selector, collapse) {
    if (collapse) {
        $(selector).show();
    } else {
        $(selector).hide();
    }
}

function liveTime() {
    var currentTime = new Date();
    var tahun = currentTime.getFullYear();
    var bulan = currentTime.getMonth();
    var tanggal = currentTime.getDate();
    var hari = currentTime.getDay();
    var jam = currentTime.getHours();
    var menit = currentTime.getMinutes();
    var detik = currentTime.getSeconds();
    switch (hari) {
        case 0:
            hari = "Minggu";
            break;
        case 1:
            hari = "Senin";
            break;
        case 2:
            hari = "Selasa";
            break;
        case 3:
            hari = "Rabu";
            break;
        case 4:
            hari = "Kamis";
            break;
        case 5:
            hari = "Jum'at";
            break;
        case 6:
            hari = "Sabtu";
            break;
    }
    switch (bulan) {
        case 0:
            bulan = "Januari";
            break;
        case 1:
            bulan = "Februari";
            break;
        case 2:
            bulan = "Maret";
            break;
        case 3:
            bulan = "April";
            break;
        case 4:
            bulan = "Mei";
            break;
        case 5:
            bulan = "Juni";
            break;
        case 6:
            bulan = "Juli";
            break;
        case 7:
            bulan = "Agustus";
            break;
        case 8:
            bulan = "September";
            break;
        case 9:
            bulan = "Oktober";
            break;
        case 10:
            bulan = "November";
            break;
        case 11:
            bulan = "Desember";
            break;
    }
    if (menit < 10) {
        menit = "0" + menit;
    }
    if (detik < 10) {
        detik = "0" + detik;
    }
    var waktu =
        hari +
        ", " +
        tanggal +
        " " +
        bulan +
        " " +
        tahun +
        "  	&bull;  " +
        jam +
        ":" +
        menit +
        ":" +
        detik +
        " ";

    document.getElementById("liveDateTime").innerHTML = waktu;
    setTimeout(liveTime, 1000);
}

function maskRupiah(selector, value) {
    if (selector == "") {
        let data = parseInt(value).toLocaleString("id-id", {
            style: "currency",
            currency: "IDR",
        });
        return data;
    } else {
        $(selector).text(
            parseInt(value).toLocaleString("id-id", {
                style: "currency",
                currency: "IDR",
            })
        );
    }
}
function _previewImg(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById("_oPreviewImg");
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function doBeforeSend(e) {
    $("button").attr("disabled", e);
    if (e) {
        $("#loader").show();
    } else {
        $("#loader").hide();
    }
}

function popToast(icon, message) {
    var Toast = Swal.mixin({
        toast: true,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: icon,
        title: "&nbsp;&nbsp;&nbsp;&nbsp;" + message,
    });
}
function bsCheck(_selector) {
    let element = $("#" + _selector + " input[type=checkbox]");
    let check = element.prop("checked")
        ? element.prop("checked", false)
        : element.prop("checked", true);
}
function _Check() {
    let element = this.$("input[type=checkbox]");
    let check = element.prop("checked")
        ? element.prop("checked", false)
        : element.prop("checked", true);
}

function formatNumber(n) {
    // format number 1000000 to 1,234,567
    let val = n.toString();
    return val.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function scrollXMenu(race) {
    const mouseWheel = document.querySelector(".scrollXMenu");
    if (mouseWheel) {
        mouseWheel.addEventListener("wheel", function (e) {
            if (e.deltaY > 0)
                // Scroll right
                mouseWheel.scrollLeft += race;
            // Scroll left
            else mouseWheel.scrollLeft -= race;
            e.preventDefault();
        });
    }
}
function formatCurrency(input, blur) {
    // appends $ to value, validates decimal side
    // and puts cursor back in right position.

    // get input value
    var input_val = input.val();

    // don't validate empty input
    if (input_val === "") {
        return;
    }

    // original length
    var original_len = input_val.length;

    // initial caret position
    var caret_pos = input.prop("selectionStart");

    // check for decimal
    if (input_val.indexOf(",") >= 0) {
        // get position of first decimal
        // this prevents multiple decimals from
        // being entered
        var decimal_pos = input_val.indexOf(",");

        // split number by decimal point
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);

        // add commas to left side of number
        left_side = formatNumber(left_side);

        // validate right side
        right_side = formatNumber(right_side);

        // On blur make sure 2 numbers after decimal
        if (blur === "blur") {
            right_side += "00";
        }

        // Limit decimal to only 2 digits
        right_side = right_side.substring(0, 2);

        // join number by .
        input_val = "Rp " + left_side + "," + right_side;
    } else {
        // no decimal entered
        // add commas to number
        // remove all non-digits
        input_val = formatNumber(input_val);
        input_val = "Rp " + input_val;

        // final formatting
        if (blur === "blur") {
            input_val += ",00";
        }
    }

    // send updated string to input
    input.val(input_val);

    // put caret back in the right position
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

function setDefaultCollapse() {
    let collapse = localStorage.getItem("collapse");
    if (collapse == "1") {
        $("body").addClass("sidebar-collapse");
    } else {
        $("body").removeClass("sidebar-collapse");
    }
}
function setCollapse() {
    setDefaultCollapse();
    $("#menu-icon").on("click", function (e) {
        let collapse = localStorage.getItem("collapse");
        if (collapse == "1") {
            localStorage.setItem("collapse", 0);
            $("body").addClass("sidebar-collapse");
        } else {
            localStorage.setItem("collapse", 1);
            $("body").removeClass("sidebar-collapse");
        }
    });

    // set default
    let collapse = localStorage.getItem("collapse");
    if (collapse == "1") {
        $("body").addClass("sidebar-collapse");
    } else {
        $("body").removeClass("sidebar-collapse");
    }
}
function actionShowImageItem() {
    let itemShow = localStorage.getItem("itemShow");
    if (itemShow == "1") {
        $("#itemShow").removeClass("bg-maroon");
        $("#itemShow").addClass("bg-olive");
        $("#itemShow").text("Aktifkan");
        $(".img-items").hide();
        localStorage.setItem("itemShow", 0);
    }
    if (itemShow == "0") {
        $("#itemShow").removeClass("bg-olive");
        $("#itemShow").addClass("bg-maroon");
        $("#itemShow").text("Non-aktifkan");
        $(".img-items").show();
        localStorage.setItem("itemShow", 1);
    }
}
function showImageItem() {
    // set default / from localstorage
    let itemShow = localStorage.getItem("itemShow");
    if (itemShow == "1" || itemShow === null) {
        $("#itemShow").removeClass("bg-olive");
        $("#itemShow").addClass("bg-maroon");
        $("#itemShow").text("Non-aktifkan");
        $(".img-items").show();
        localStorage.setItem("itemShow", 1);

    }
    if (itemShow == "0") {
        $("#itemShow").removeClass("bg-maroon");
        $("#itemShow").addClass("bg-olive");
        $("#itemShow").text("Aktifkan");
        $(".img-items").hide();
    }
}

$(document).ready(function () {
    // Format mata uang.
    $(".uang").mask("000.000.000.000.000.000", { reverse: true });
    setCollapse();
    showImageItem();
});
