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
    <script>
        function send_request(request_json, server_url) {
            return $.ajax({
                url: server_url,
                type: "POST",
                dataType: 'json',
                encode: true,
                data: request_json,

                success: function (json) {
                    console.log(json);
                    return json
                },
                error: function (jXHR, textStatus, errorThrown) {
                    console.log(jXHR.responseText);
                    throw Error(errorThrown);
                    // TODO throw this in a little error box
                }
            });
        }
    </script>
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

        const server_url = "o10_db.php"
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
        table.tableData = data // TODO replace this with db ajax request
        table.populateHeader();
        table.populateBody();
        /*
            Binding button handlers
         */
        table.bind_searchHandler((search_json, sort_json) => {
            // Create request: only search
            const request = {"SEARCH": JSON.stringify({
                    "data": search_json,
                    "sort": sort_json
                })
            }
            const result = send_request(request, server_url)
            if (!result) {
                return this.tableData
            }
            else {
                return result
            }
        });
        table.bind_updateHandler((data_json) => {
            // Create request
            const request = {
                "UPDATE": data_json
            }
            const result = send_request(request, server_url)
        })
        table.bind_deleteHandler((data_json) => {
            // Create request: only search
            const request = {
                "DELETE": data_json
            }
            const result = send_request(request, server_url)
        })
    </script>
</body>
</html>
