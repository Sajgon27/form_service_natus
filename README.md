# Serwis Natu

Plugin umożliwiający wyświetlenie zaawansowanego wieloetapowego formularza do usługi serwisu akwarystycznego.

## Instalacja

1. Pobierz plugin jako plik ZIP
2. Przejdź do panelu administracyjnego WordPress
3. Wybierz Wtyczki > Dodaj nową > Wyślij wtyczkę > wybierz pobrany plik ZIP
4. Aktywuj plugin po instalacji

## Użytkowanie

Aby dodać formularz na stronie, użyj shortcode:

```
[serwis_natu_form]
```

## Struktura formularza

Formularz składa się z 4 kroków:

1. **Wybór potrzeb akwariów**
   - Wybór trybu współpracy
   - Określenie ilości akwariów
   - Specyfikacja potrzeb dla każdego akwarium

2. **Dane kontaktowe i lokalizacja**
   - Dane osobowe klienta
   - Adres, na którym znajdują się akwaria

3. **Dopasowany pakiet i wstępna wycena**
   - Wygenerowana propozycja usług
   - Wstępna wycena

4. **Podsumowanie i wysyłka zgłoszenia**
   - Podsumowanie wybranych opcji
   - Wysyłka formularza

## Rozwijanie pluginu

Plugin zaprojektowany jest modułowo, co umożliwia łatwe rozszerzanie funkcjonalności:

- Pliki kroków znajdują się w katalogu `/templates/`
- Style CSS w `/assets/css/`
- Skrypty JavaScript w `/assets/js/`

## Wsparcie

W razie pytań lub problemów, proszę o kontakt przez stronę [Natuscape](https://natuscape.pl/kontakt).
