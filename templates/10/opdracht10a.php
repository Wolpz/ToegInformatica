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
        function send_request(request_json, server_url, onSuccess, onError) {
            console.log("Request sent:", request_json);
            $.ajax({
                url: server_url,
                type: "POST",
                dataType: 'json',
                encode: true,
                data: request_json,

                success: function(response) {
                    console.log("Response:", response);
                    document.getElementById("table_error_box").innerHTML=""
                    if (onSuccess)
                        onSuccess(response)
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("AJAX Request Error: ", xhr)
                    let message = "Unknown error.";
                    try {
                        const errObj = JSON.parse(xhr.responseText.slice(3));
                        message = errObj.message || xhr.responseText;
                    } catch (_) {
                        message = xhr.responseText
                    }
                    console.error("Server error: ", message)
                    document.getElementById("table_error_box").innerHTML=message
                }
            });
        }
    </script>
</head>

<body>
    <div id="datatable_container">
        <div id="table_error_box"></div>
        <table id="datatable" class="o10table">
        </table>
    </div>

    <script>
        const server_url = "o10_db.php"
        const tableElem = document.getElementById('datatable');
        const table = new CustomTable(
            tableElem,
            ["id", "Series_Title", "Released_Year", "Director"], {
            fetchData: function (search_json, sort_json) {
                send_request(
                    { SEARCH: JSON.stringify({ data: search_json, sort: sort_json }) },
                    server_url,
                    function onSuccess(result) {
                        table.tableData = result.data;
                        table.populateBody();
                    },
                    function onError(xhr, status, err, msg) {
                        console.error("Search failed:", xhr)
                    }
                );
            },
            updateHandler: function (data_json) {
                send_request(
                    {
                        UPDATE: data_json,
                        SEARCH: JSON.stringify(
                            {
                                data: table.searchContents,
                                sort: table.sort
                            })
                    },
                    server_url,
                    function onSuccess(result) {
                        table.tableData = result.data;
                        table.populateBody();
                    },
                    function onError(xhr, status, err, msg) {
                        console.error("Search failed:", xhr)
                    }
                );
            },
            deleteHandler: function (data_json) {
                send_request(
                    {
                        DELETE: data_json,
                        SEARCH: JSON.stringify(
                            {
                                data: table.searchContents,
                                sort: table.sort
                            })
                    },
                    server_url,
                    function onSuccess(result) {
                        table.tableData = result.data;
                        table.populateBody();
                    },
                    function onError(xhr, status, err, msg) {
                        console.error("Search failed:", xhr)
                    }
                );
            }
        });
    </script>
</body>
</html>
