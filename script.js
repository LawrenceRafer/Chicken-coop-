
if (window.location.pathname.includes("dashboard.html")) {
    if (localStorage.getItem("loggedIn") !== "true") {
        window.location.href = "login.html";
    }
}


function turnOnFan() {
    document.getElementById("fanStatus").innerText = "ON";
    document.getElementById("message").innerText = "Fan Activated";
}

function turnOffFan() {
    document.getElementById("fanStatus").innerText = "OFF";
    document.getElementById("message").innerText = "Fan Deactivated";
}



const ctx = document.getElementById('sensorChart').getContext('2d');

const sensorChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Temperature °C',
                borderColor: 'red',
                data: []
            },
            {
                label: 'Humidity %',
                borderColor: 'blue',
                data: []
            }
        ]
    },
    options: {
        responsive: true
    }
});



setInterval(() => {

    const temp = Math.floor(Math.random() * 5) + 28;
    const humidity = Math.floor(Math.random() * 10) + 60;

    document.getElementById("temp").innerText = temp;
    document.getElementById("humidity").innerText = humidity;

    const time = new Date().toLocaleString();
    const fan = document.getElementById("fanStatus").innerText;


    sensorChart.data.labels.push(time);
    sensorChart.data.datasets[0].data.push(temp);
    sensorChart.data.datasets[1].data.push(humidity);

    if (sensorChart.data.labels.length > 10) {
        sensorChart.data.labels.shift();
        sensorChart.data.datasets[0].data.shift();
        sensorChart.data.datasets[1].data.shift();
    }

    sensorChart.update();


  
    const table = document.getElementById("logTable");
    const row = table.insertRow(0);

    row.insertCell(0).innerText = time;
    row.insertCell(1).innerText = temp;
    row.insertCell(2).innerText = humidity;
    row.insertCell(3).innerText = fan;

}, 3000);