


<!DOCTYPE html>
<!--
Maak en bewerk een relationele database en gebruik hierbij MySql.

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
    <title>GRAPHINATOR</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#id_graphSelection').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    dataType: 'json',
                    encode: true,
                    data: $(this).serialize(),

                    success: function (json) {
                        if (json)
                            console.log(json);

                    },
                    error: function (jXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            });
        });
    </script>

</head>

<body>


</body>
</html>
