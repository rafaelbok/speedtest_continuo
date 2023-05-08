<html>
<head>
	<title>Resultados de velocidade</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f0f0f0;
		}
		h1 {
			background-color: #005ea5;
			color: #fff;
			padding: 10px;
			text-align: center;
		}
		table {
			margin: 20px auto;
			border-collapse: collapse;
			background-color: #fff;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
		}
		th, td {
			padding: 10px;
			text-align: center;
		}
		th {
			background-color: #005ea5;
			color: #fff;
		}
		tr:nth-child(even) {
			background-color: #f2f2f2;
		}
		.resultados {
			text-align: center;
			font-size: 24px;
			margin-bottom: 20px;
		}
		.resultados span {
			font-size: 24px;
			font-weight: bold;
			color: #005ea5;
		}
		.verde {
			color: green;
		}
		.vermelho {
			color: red;
		}
		.summary-table {
			width: 60%;
			margin: 20px auto;
			border-collapse: collapse;
			background-color: #fff;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
		}

		.summary-table th,
		.summary-table td {
			padding: 10px;
			text-align: center;
			border: 1px solid #ccc;
		}

		.summary-table th {
			background-color: #005ea5;
			color: #fff;
		}
.up-arrow {
    display: inline-block;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 10px solid green;
    margin-left: 5px;
}

.down-arrow {
    display: inline-block;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 10px solid red;
    margin-left: 5px;
}
table td {
    text-align: right;
}
table th,
table td {
    padding: 15px 30px;
}

	</style>
</head>
<body>
	<h1><p>Resultados do mês passado</p> </h1>
	<table class="summary-table">
		<tr>
			<th>Checks</th>
			<th>Uptime Down <span class="down-arrow"></th>
			<th>Uptime Up<span class="up-arrow"></th>
			<th>Down Médio</th>
			<th>Up Médio</th>
			<th>Ping Médio</th>
			<th>Falhas de Download</th>
			<th>Falhas de Upload</th>
		</tr>
		<tr>
			<td><span id="total_verificacoes"></span></td>
			<td><span id="sla_download"></span>%</td>
			<td><span id="sla_upload"></span>%</td>
			<td><span id="media_download"></span>Mb/s</td>
			<td><span id="media_upload"></span>Mb/s</td>
			<td><span id="media_ping"></span>ms</td>
			<td><span id="abaixo_download"></span></td>
			<td><span id="abaixo_upload"></span></td>
		</tr>
	</table>

	</div>
	<table>
			<th>Data</th>
			<th>Horário</th>
			<th>Download Mb/s</th>
			<th>Upload Mb/s</th>
			<th>Ping</th>
		</tr>
		<?php
		$mes_anterior = date('M', strtotime('-1 month'));
		$arquivo = "/var/www/html/internet_teste/speedtest_results_" . $mes_anterior . ".csv";
		if (file_exists($arquivo)) {
			$resultados = array();
			$total_download = 0;
			$total_upload = 0;
			$total_ping = 0;
			$abaixo_download = 0;
			$abaixo_upload = 0;
			$linhas = file($arquivo);
			for ($i = 1; $i < count($linhas); $i++) {
				$campos = explode(",", $linhas[$i]);
				$data = $campos[0];
				$hora = $campos[1];
				$download = $campos[2];
				$upload = $campos[3];
				$ping = $campos[7];
				$total_download += $download;
				$total_upload += $upload;
				$total_ping += $ping;
				if ($download < 240) {
					$abaixo_download++;
				}
				if ($upload < 150) {
					$abaixo_upload++;
				}
				array_push($resultados, array("data"=>$data, "hora"=>$hora, "download"=>$download, "upload"=>$upload, "ping"=>$ping));
			}
			$num_resultados = count($resultados);
			$media_download = round($total_download / $num_resultados, 2);
			$media_upload = round($total_upload / $num_resultados, 2);
			$media_ping = round($total_ping / $num_resultados, 3);
			$sla_download = round(($num_resultados - $abaixo_download) / $num_resultados * 100, 2);
			$sla_upload = round(($num_resultados - $abaixo_upload) / $num_resultados * 100, 2);
			for ($i = 0; $i < $num_resultados; $i++) {
    $data = $resultados[$i]["data"];
    $hora = $resultados[$i]["hora"];
    $download = $resultados[$i]["download"];
    $upload = $resultados[$i]["upload"];
    $ping = $resultados[$i]["ping"];

    $downloadArrow = $download >= 240 ? '<span class="up-arrow"></span>' : '<span class="down-arrow"></span>';
    $uploadArrow = $upload >= 150 ? '<span class="up-arrow"></span>' : '<span class="down-arrow"></span>';

    echo "<tr><td>$data</td><td>$hora</td><td>$download $downloadArrow</td><td>$upload $uploadArrow</td><td>$ping ms</td></tr>";


			}
			echo "<tr><th>Média</th><td></td><td>$media_download Mb/s</td><td>$media_upload Mb/s</td><td>$media_ping ms</td></tr>";
			echo "<tr><th>SLA</th><td></td><td>$sla_download  %</td><td>$sla_upload %</td><td></td></tr>";
			echo "<tr><th>Falhas</th><td></td><td>$abaixo_download / $num_resultados</td><td>$abaixo_upload /
 			$num_resultados</td><td></td></tr>";} 
			else {echo "Arquivo não encontrado.";}
			echo "<script>document.getElementById('sla_download').innerText = '$sla_download'; document.getElementById('sla_upload').innerText = 			'$sla_upload';</script>";
			echo "<script>document.getElementById('media_download').innerText = '$media_download'; document.getElementById('media_upload').innerText 			= '$media_upload'; document.getElementById('sla_download').innerText = '$sla_download'; document.getElementById('sla_upload').innerText 			= '$sla_upload';</script>";
			echo "<script>document.getElementById('media_download').innerText = '$media_download'; document.getElementById('media_upload').innerText 			= '$media_upload'; document.getElementById('sla_download').innerText = '$sla_download'; document.getElementById('sla_upload').innerText 			= '$sla_upload'; document.getElementById('abaixo_download').innerText = '$abaixo_download / $num_resultados'; 							document.getElementById('abaixo_upload').innerText = '$abaixo_upload / $num_resultados';</script>";
			echo "<script>document.getElementById('total_verificacoes').innerText = '$num_resultados'; 										document.getElementById('media_download').innerText = '$media_download'; document.getElementById('media_upload').innerText = 					'$media_upload'; document.getElementById('sla_download').innerText = '$sla_download'; document.getElementById('sla_upload').innerText = 			'$sla_upload'; document.getElementById('abaixo_download').innerText = '$abaixo_download / $num_resultados'; 							document.getElementById('abaixo_upload').innerText = '$abaixo_upload / $num_resultados';</script>";
			echo "<script>document.getElementById('total_verificacoes').innerText = '$num_resultados'; 										document.getElementById('media_download').innerText = '$media_download'; document.getElementById('media_upload').innerText = 					'$media_upload'; document.getElementById('media_ping').innerText = '$media_ping'; document.getElementById('sla_download').innerText = 			'$sla_download'; document.getElementById('sla_upload').innerText = '$sla_upload'; document.getElementById('abaixo_download').innerText = 			'$abaixo_download / $num_resultados'; document.getElementById('abaixo_upload').innerText = '$abaixo_upload / 							$num_resultados';</script>";
		
?>

</table>
</body>
</html>