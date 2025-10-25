// Side bar set
$(document).ready(function () {
    function checkWidth() {
        if ($(window).width() < 1024) {
            $('body').addClass('side-hide');
        } else {
            $('body').removeClass('side-hide');
        }
    }

    checkWidth();

    $(window).resize(function () {
        checkWidth();
    });

});

// sidebar function
function sidebar() {
    $("body").toggleClass("side-hide");
}

// dashboard 5 store sale item comparison chart
const ctx = document.getElementById("storeSalesChart");

new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep"],
        datasets: [
            {
                label: "Store 1",
                data: [30, 40, 25, 15, 35, 28],
                backgroundColor: "#ED1C24",
            },
            {
                label: "Store 2",
                data: [70, 45, 40, 25, 75, 40],
                backgroundColor: "#ED631C",
            },
            {
                label: "Store 3",
                data: [75, 50, 0, 0, 65, 30],
                backgroundColor: "#ED1CA1",
            },
            {
                label: "Store 4",
                data: [40, 60, 70, 45, 40, 25],
                backgroundColor: "#ED3F1C",
            },
            {
                label: "Store 5",
                data: [20, 48, 80, 20, 25, 50],
                backgroundColor: "#262D34",
            },
        ],
    },
    options: {
        responsive: true,
        scales: {
            x: {
                grid: {
                    display: false,
                },
                stacked: false,
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 20,
                },
            },
        },
        plugins: {
            legend: {
                display: false, // hide legend if you want to match your image
            },
        },
    },
});

