
## Projekt: Currency Exchange Rate Notifier

### Uruchamianie:
docker-compose up --build -d
docker-compose exec node10 npm install && npm run build
docker-compose exec php composer run dev

### Plan projektu

### Etap 1: Inicjalizacja Projektu i Podstawowa Konfiguracja
- Przygotowanie środowiska z użyciem docker compose
- Inicjalizacja projektu Laravel
- Instalacja zależności
- Konfiguracja bazy danych

### Etap 2: Tworzenie Modeli i Migracji
- Definicja Schematu Bazy Danych oraz migracje
users (domyślna tabela, już stworzona).
currencies: id, symbol (np. 'USD'), name (np. 'United States Dollar').
currency_rates_history: id, from_currency_id, to_currency_id, rate (decimal), rate_date (date/datetime).
subscriptions: id, user_id, from_currency_id, to_currency_id, threshold (decimal), direction (enum: 'above', 'below'), last_notified_at (nullable datetime), is_active (boolean).

- Tworzenie modeli
- Tworzenie migracji
- Tworzenie seederów
- ?Tworzenie kontrolerów
- ?Tworzenie widoków
- ?Tworzenie routów

### Etap 3: Integracja z Zewnętrznym API Kursów Walut

### Etap 4: Zadania w Tle
- Konfiguracja kolejek
- Scheduler i job do Sprawdzania Kursów Walut 
- Scheduler i job do Wysyłania Powiadomień 

### Etap 5: Interfejs Użytkownika