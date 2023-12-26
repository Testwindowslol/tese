<?php

include_once __DIR__ . "/php/core.php";

?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Panel - Stats</title>
</head>
<body style="background: #0c1427; color: #FFFFFF; font-family: Poppins, sans-serif;">
	<div id="loading">
		<h1>Loading data, please wait a few seconds...</h1>
	</div>
	<div>
		<canvas id="chart"></canvas>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		const CHART_COLORS = {
			red: 'rgb(255, 99, 132)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(153, 102, 255)',
			grey: 'rgb(201, 203, 207)'
		};

		function buildDatasets(data) {
			let datasets = [];

			for (let i = 0; i < data["methods"].length; i++) {
				let method = data["methods"][i];

				datasets.push(buildDataset(method, data));
			}

			return datasets;
		}

		function buildDataset(method, data) {
			let attacksData = [];

			for (let i = 0; i < data["data"].length; i++) {
				attacksData.push(data["data"][i]["attacks"][method]);
			}

			return {
				label: method,
				data: attacksData
			}
		}

		let xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (this.readyState === this.DONE && this.status === 200) {
				let logsData = JSON.parse(this.responseText);

				let labels = logsData["days"];
				let dataCount = labels.length;

				let data = {
					labels: labels,
					datasets: buildDatasets(logsData)
				};

				let config = {
					type: 'bar',
					data: data,
					options: {
						plugins: {
						title: {
							display: true,
							text: 'Hexstresser.org - Attacks'
						},
						},
						responsive: true,
						scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
						}
					}
				};

				new Chart(ctx, config);

				document.getElementById("loading").remove();
			}  
		};
		xhr.open("GET", "https://hexstresser.org/panel/api/data.php");
		xhr.send();

		const ctx = document.getElementById('chart');

		function randomNumbers(numberCfg) {
			let output = [];
			for (let i = 0; i < numberCfg.count; i++) {
				output.push(Math.random() * 10000);
			}
			console.log(output);
			return output;
		}

        window.addEventListener("resize", (event) => {
            for (let id in Chart.instances) {
                Chart.instances[id].resize();
            }

            // Fix a weird glitch
            setTimeout(() => {
                for (let id in Chart.instances) {
                    Chart.instances[id].resize();
                }
            }, 200);
        });

		/*
		const actions = [
			{
				name: 'Randomize',
				handler(chart) {
					chart.data.datasets.forEach(dataset => {
						dataset.data = randomNumbers(NUMBER_CFG);
					});
					chart.update();
				}
			},
		];
		*/
		</script>
</body>
</html>