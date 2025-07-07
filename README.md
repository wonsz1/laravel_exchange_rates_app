
## Projekt: Currency Exchange Rate Notifier

### Wyzwania i Rozwiązania architektoniczne
**Tabele**:\
**users** (domyślna tabela, już stworzona).\
**currencies**: id, symbol (np. 'USD'), name (np. 'United States Dollar').\
**currency_rates_history**: id, from_currency_id, to_currency_id, rate (decimal), rate_date (date/datetime).\
**subscriptions**: id, user_id, from_currency_id, to_currency_id, threshold (decimal), direction (enum: 'above', 'below'), last_notified_at (nullable datetime), is_active (boolean).\
tabele **jobs** - jobs, job_batches i failed_jobs

**Threshold** w tabeli subscriptions reprezentuje wartość kursu wymiany, która wyzwala powiadomienie dla użytkownika.
Przykład użycia: \
Jeśli użytkownik chce otrzymywać powiadomienia, gdy 1 USD jest wart więcej niż 4,50 PLN \
Utworzyłby subskrypcję z: \
from currency: USD \
to currency: PLN \
threshold: 4.50 \
direction: 'above' \

**API kursów walut**: \
Korzystam z API NBP - https://api.nbp.pl/, które pozwala na pobieranie kursów walut tylko w przeliczeniu na złotówki. Można jednak dodać serwis komunikujący się z innym API i zmienić polecenia importu - np. zostosować wzorzec strategii i zmieniać integrację w zależności od parametru w .env, w komendzie konsolowej czy nawet dać do wyboru w interfejsie użytkownika. 

**API Platform**

Dostępne pod adresem:
http://localhost:8080/api/docs

**Import kursów**: \
Wielokrotne importowanie kursów dla tego samego dnia powoduje błąd przy tworzeniu rekordów w tabeli currency_rates_history(kurs waluty dla danego dnia musi być unikalny). Taka sytuacja może pojawiać się przy manualnym imporcie pryz użyciu komend konsolowych. Dlatego zastosowalem metode upsert w ImportCurrentCurrencyRates oraz w ImportCurrencyRatesHistory.

Dla importu kursów została przygotowana komenda konsolowa **currency:import-current-rates**, która została dodana do routes/console.php i może być uruchomiona przez cron.
Dla obsługi subskrypcji została przygotowana komenda konsolowa **app:subscription-notification**, która również została dodana do routes/console.php i może być uruchomiona przez cron. Uruchomienie komendy powoduje dodanie do kolejki emaili z powiadomieniami dla użytkowników jeśli subskrypcja jest aktywna i kurs jest powyżej lub poniżej progu ustawionego przez użytkownika.
notyfikacje można wysłać workerm w cron:
* * * * * cd /var/www/html/ && php artisan queue:work --queue=mail-queue

### Uruchamianie:
docker-compose up --build -d \
docker-compose exec node10 npm install && npm run build \
docker-compose exec php composer run dev 

### Testy
docker-compose exec php ./vendor/bin/phpstan analyse app --memory-limit=1G

### Plan projektu

### Etap 1: Inicjalizacja Projektu i Podstawowa Konfiguracja
- Przygotowanie środowiska z użyciem docker compose
- Inicjalizacja projektu Laravel
- Instalacja zależności
- Konfiguracja bazy danych

### Etap 2: Tworzenie Modeli i Migracji
- Definicja Schematu Bazy Danych oraz migracje
- Tworzenie modeli
- Tworzenie seederów

### Etap 3: Integracja z Zewnętrznym API Kursów Walut
- Napisanie serwisu komunikującego się z API
- Napisanie komendy konsolowej do pobierania kursów
- Napisanie komendy konsolowej do wysyłania powiadomień
- Scheduler i job do Wysyłania Powiadomień 

### Etap 4: API
- API Platform [in progress]
- Api Platform Admin [todo]
- Zabezpieczenie API [todo]

### Etap 5: Interfejs Użytkownika
- Wyświetlenie listy dostępnych walut
- Wyświetlenie wykresu kursów wybraje waluty

[todo]

- Dodanie możliwości zmiany zakresu dat dla wykresu
- Dodawanie, edycja i usuwanie subskrypcji
- Wyświetlenie listy subskrypcji
- Panel Admina

### Etap 6: Logowanie i rejestracja [todo]
- Logowanie za pomocą Google i Facebook

### Etap 7: Implementacja możliwości zmiany API kursów walut [todo]

### Etap 8: Monitoring Prometheus i Grafana [todo]