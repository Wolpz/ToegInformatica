<!DOCTYPE html>
<!--
Maak en bewerk een relationele database en gebruik hierbij MySql.

https://www.jetbrains.com/help/phpstorm/export-data.html
https://www.jetbrains.com/help/phpstorm/import-data.html#import-data-to-a-database

Bij het inleveren ook de de structuur van je database inleveren (mag ook als screenshot).
Gebruik de interface vanuit plesk voor inzicht in de databasestructuur.
Eisen:
- Gegevens toevoegen
- Gegevens verwijderen
- Gegevens wijzigen (adv een lijst aanwezige data)
- Sorteren
- Selecties uitvoeren
-->
<html lang="en">
<head>
    <title>10</title>
    <link rel="stylesheet" href="../../styles/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../../src/dbHelpers.js"></script>
</head>

<body>
    <div id="datatable_container">
        <table id="datatable" class="o10table">
        </table>
    </div>

    <script>
        const data = [
            { "id": 0, "name": "abc", "age": 1 },
            { "id": 1, "age": "2", "name": "def" },
            { "id": 2, "name": "xyz", "age": 3.2 }
        ];
        const tableElem = document.getElementById('datatable');
        const table = new CustomTable(
            tableElement = tableElem,
            columnNames = ["name", "age"],
            genSearchBars = true,
            genSortButtons = true,
            genUpdateButtons = true,
            genAddEntryFields = true,
            genDeleteButtons = true
        );
        table.tableData = data
        table.populateHeader();
        table.populateBody();
    </script>
</body>
</html>
