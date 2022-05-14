# Wykres temperatury + sterowanie wentlatorem (arduino)
Arduino odczytuje temperaturę z 3 sensorów. Jeśli temperatura na jednym z nich przekroczy zadeklarowaną wartość zostanie włączony wentylator. Aktualna temperatura jest też udostępniana poprzez prosty serwer http na arduino.<br><br>
Na komputerze zainstalowana jest baza danych Mysql oraz apache2. Co 5 minut (crontab) wykonywany jest skrypt upload.php pobierajacy aktualną temperaturę z arduino i zapisujący ją do bazy danych, jeśli mieści się w porządanym zakresie. Pozwala to zignorować błędy odczytu które co jakiś czas się zdarzają.<br><br> 
Aktualną temperaturę jak i dane z poprzednich dni na wykresie można zobaczyć poprzez przeglądarkę (index.php).
