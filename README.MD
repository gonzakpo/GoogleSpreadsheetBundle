DreamlexGoogleSpreadsheetBundle
=======
Installing
--
Go to <https://developers.google.com/sheets/quickstart/php>

Follow the first step.

Put your `client_secret.json` in to `/app/config/`

Execute command
```
app/console google-spreadsheet:set-credentials
```

Usage
--
```php
$googleSpreadsheet = $this->container->get('dreamlex_google_spreadsheet');
$tabelData = $googleSpreadsheet->getTable('1_ApzX0ZEpN79EL4v_rSpU1PxpgYYOCDKfet7_le0suM', 'IP!A2:B');

```