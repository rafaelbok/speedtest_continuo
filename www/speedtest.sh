#!/bin/bash

# Verifica se o mês mudou e cria um novo arquivo de resultados
mes_atual=$(date +%b)
if [ ! -f /home/ubuntu/speedtest_results_${mes_atual}.csv ]; then
    echo "Data,Hora,Download,Upload,ID do Servidor,Nome do Servidor,IP,Ping" > /var/www/html/internet_teste/speedtest_results_${mes_atual}.csv
fi

if [ ! -f /home/ubuntu/speedtest_results_low_${mes_atual}.csv ]; then
    echo "Data,Hora,Download,Upload,ID do Servidor,Nome do Servidor,IP,Ping" > /var/www/html/internet_teste/speedtest_results_low_${mes_atual}.csv
fi

data=$(date '+%d %b %Y')
hora=$(date '+%H:%M')
minutos=$(date '+%M')
echo "$data | $hora"
if [ $minutos -eq 0 ] || [ $minutos -eq 30 ]; then
    echo "Horário não permitido, não executando o teste."
else
    speedtest --secure --json > results.json
    download=$(printf "%.2f" $(cat results.json | jq '.download / 1000000'))
    upload=$(printf "%.2f" $(cat results.json | jq '.upload / 1000000'))
    server_id=$(cat results.json | jq -r '.server.id')
    server_name=$(cat results.json | jq -r '.server.name')
    isp=$(cat results.json | jq -r '.isp')
    connection_type=$(cat results.json | jq -r '.interface')
    ip=$(cat results.json | jq -r '.client.ip')
    ping=$(printf "%.3f" $(cat results.json | jq '.ping'))
    jitter=$(cat results.json | jq '.jitter')
    echo "Download: $download Mb/s"
    echo "Upload: $upload Mb/s"
    echo "IP: $ip"
    echo "Ping: $ping ms"

    if (( $(echo "$download < 240" | bc -l) )) || (( $(echo "$upload < 150" | bc -l) )); then
        echo "$data,$hora,${download} Mb/s,${upload} Mb/s,$server_id,\"$server_name\",$ip,$ping" >> /var/www/html/internet_teste/speedtest_results_low_${mes_atual}.csv
        echo "$data,$hora,${download} Mb/s,${upload} Mb/s,$server_id,\"$server_name\",$ip,$ping" >> /var/www/html/internet_teste/speedtest_results_${mes_atual}.csv
        echo "Resultado salvo!"
    else
        echo "$data,$hora,${download} Mb/s,${upload} Mb/s,$server_id,\"$server_name\",$ip,$ping" >> /var/www/html/internet_teste/speedtest_results_${mes_atual}.csv
        echo "Resultado não atende aos critérios, não salvo!"
    fi
fi
