#!/bin/bash
data=$(date '+%d %b %Y')
hora=$(date '+%H:%M')
echo "$data | $hora"
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

if (( $(echo "$download < 240" | bc -l) )) && (( $(echo "$upload < 150" | bc -l) )); then
    echo "$data,$hora,$download,$upload,$server_id,\"$server_name\",$ip,$ping" >> /home/ubuntu/speedtest_results_low.csv
    echo "Resultado salvo!"
else
    echo "Resultado não atende aos critérios, não salvo!"
fi
