


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

<html>
<head>
    <title>10</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../../src/dbHelpers.js"></script>
    <script>
        // DEFAULT VALUES
        let sortState = {
            field:      'id',
            direction:  'asc'
        };
        let searchState = null;

        $(document).ready(function () {
            tableElement = '#datatable';
            const data = [
                { name: "abc", age: 50 },
                { age: "25", name: "swimming" },
                { name: "xyz", age: "1" }
            ];


            updateTable('#datatable', sortState, searchState);
        });


            // Put new data in table

        }
    </script>
</head>

<body>
<p id="datatable">
Table goes here.
</p>


</body>
</html>
