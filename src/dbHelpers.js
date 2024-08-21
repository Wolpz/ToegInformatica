function generateTable(data) {
    const keys = Object.keys(data[0]);
    let tableHTML = '<table><tr>';
    for(const key of keys) {
        tableHTML += '<th>'+key+'</th>';
    };
    tableHTML += '</tr>';
    for(const item of data) {
        tableHTML += '<tr>';
        for(const key of keys) {
            tableHTML += '<th>'+item[key]+'</th>';
        };
        tableHTML += '</tr>';
    };
    tableHTML += '</table>';
    return tableHTML;
}

function updateTable(tableElement, sortParams, searchParams) {
    // You can turn this into OOP stuff later to couple site tables and db tables!

    let dataParams = {
        select: {
            table: "cats",
            fields: "*",
            sort: sortParams,
            search: searchParams
        }
    };
    if (searchParams == null)
        delete dataParams.select.search;

    $.ajax({
        url: "db_functions.php",
        type: "POST",
        dataType: 'json',
        encode: true,
        data: dataParams,

        success: function (json) {
            if (json)
                console.log(json);

            if (json.success == false) {
                $(tableElement).html("An error occurred: \n" + json.errorMsg);
                $(tableElement).css("background-color", "red");
            } else {
                $(tableElement).html(generateTable(json.data));
            }
        },
        error: function (jXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function f() {
    
}