class CustomTable {
    constructor(
        tableElement,
        columnNames,
        genSearchBars = false,
        genSortButtons = false,
        genUpdateButtons = false,
        genAddEntryFields = false,
        genDeleteButtons = false
    ) {
        this.element = tableElement;
        this.columns = columnNames;
        this.tableData = [];
        this.genSearchBars = genSearchBars;
        this.genUpdateButtons = genUpdateButtons;
        this.genAddEntryFields = genAddEntryFields;
        this.genSortButtons = genSortButtons;
        this.genDeleteButtons = genDeleteButtons;

        this.searchContents = {}; // To query in change handler
        this.sortButtons = []; // To later reset all to indeterminate

        this.header = null;
        this.body = null;
        this.rows = [];

        this.prevEditRow = null;
    }

    populateHeader() {
        if (this.header == null)
            this.header = this.element.createTHead();
        const row = this.header.insertRow();
        for (const col of this.columns) {
            const colHeader = document.createElement("th");
            colHeader.id = this.element.id+"_"+col+"_header";
            colHeader.classList.add(this.element.id+"_header");
            const colTitle = document.createElement("div");
            colTitle.id = colHeader.id+"_title";
            colTitle.innerHTML = col;
            colHeader.appendChild(colTitle);

            // Search bar
            if (this.genSearchBars) {
                const searchBar = document.createElement("input");
                searchBar.type = "search";
                // Action data
                searchBar.dataset.col = col
                this._searchButtonHandler = this._searchButtonHandler.bind(this);
                searchBar.addEventListener("change", this._searchButtonHandler);
                colHeader.appendChild(searchBar);
            }
            // Sort button
            if (this.genSortButtons) {
                const sortButton = document.createElement("input");
                sortButton.type = "checkbox";
                sortButton.classList.add("sortButton");
                sortButton.classList.add("sort-none");
                // Action data
                sortButton.dataset.col = col;
                sortButton.indeterminate = true;
                this._sortButtonHandler = this._sortButtonHandler.bind(this);
                sortButton.addEventListener("change", this._sortButtonHandler);
                this.sortButtons[col] = sortButton;
                colHeader.appendChild(sortButton);
            }
            row.appendChild(colHeader);
        }
        // Utility buttons column header
        const colHeader = document.createElement("th");
        colHeader.innerText = "";
        row.appendChild(colHeader);
    }

    populateBody(data = this.tableData) {
        if (this.body != null)
            this.body.remove()
        this.body = this.element.createTBody();
        this.rows = [];
        for (let rowIndex = 0; rowIndex < data.length; rowIndex++) {
            const row = this.generateRow(data[rowIndex], rowIndex);
            this.rows.push(row);
            this.body.appendChild(row);
        }
        this.prevEditRow = null;
        this.prevDeleteButton = null;
        /*
        // TODO make this a separate form but linkable to the table
        if (this.genAddEntryFields) {
            const row = this.body.insertRow();
            row.id = this.element.id+"_row_addEntry";
            for (const col in this.columns) {
                const cell = row.insertCell();
                const field = document.createElement("input");
                field.data_column = col
                field.type = "text";
                cell.appendChild(field);
            }
            const utilityCell = row.insertCell()
            const addEntryButton = document.createElement("input");
            addEntryButton.type = "button";
            addEntryButton.value = "Add entry";
            addEntryButton.classList.add("addEntryButton");
            // Action data
            addEntryButton.row_num =
            this.addEntryHandler = this.addEntryHandler.bind(this);
            addEntryButton.addEventListener("click", this.addEntryHandler);
            utilityCell.appendChild(addEntryButton);
        }
        */
    }
    
    generateRow(data, rowIndex, options = {}) {
        const row = document.createElement("tr");
        row.dataset.rowIndex = rowIndex;
        row.dataset.id = data['id']
        for (const col of this.columns) {
            let value = "";
            if (col in data) {
                value = data[col];
            }
            if (options['inputFields']) {
                const inputField = document.createElement("input");
                inputField.type = "text";
                inputField.value = value;
                inputField.dataset.column = col;
                row.insertCell().appendChild(inputField);
            }
            else {
                row.insertCell().innerText = value;
            }
        }

        const utilityCell = row.insertCell();
        if (this.genUpdateButtons) {
            if ( !('id' in data)){
                this.genDeleteButtons = false;
                throw Error("Field 'id' not provided in data. Aborting edit button generation.");
            }
            const updateButton = document.createElement("input");
            updateButton.type = "button";
            updateButton.value = "EDIT";
            updateButton.classList.add("updateButton");
            // Action handling
            updateButton.dataset.id = data.id;
            updateButton.dataset.rowIndex = rowIndex;
            this._updateButtonHandler = this._updateButtonHandler.bind(this);
            updateButton.addEventListener("click", this._updateButtonHandler);
            utilityCell.appendChild(updateButton);
            if (options['inputFields']) {
                updateButton.value = "CONFIRM";
                const cancelButton = document.createElement("input");
                cancelButton.type = "button";
                cancelButton.classList.add("cancelButton");
                cancelButton.value = "CANCEL";
                // Action handling
                cancelButton.dataset.id = data.id;
                cancelButton.dataset.rowIndex = rowIndex;
                cancelButton.addEventListener("click", this._updateButtonHandler);
                utilityCell.appendChild(cancelButton)
            }
        }
        if (this.genDeleteButtons) {
            if ( !('id' in data)){
                this.genDeleteButtons = false;
                throw Error("Field 'id' not provided in data. Aborting delete button generation.");
            }
            const deleteButton = document.createElement("input");
            deleteButton.type = "button";
            deleteButton.classList.add('deleteButton');
            deleteButton.value = "DELETE"
            // Action handling
            deleteButton.dataset.id = data.id;
            this._deleteButtonHandler = this._deleteButtonHandler.bind(this);
            deleteButton.addEventListener("click", this._deleteButtonHandler);

            utilityCell.appendChild(deleteButton);
        }
        return row;
    }

    /*
        RE-BINDABLES
    */
    _searchHandler(search_json) {
        console.log("Search handler unimplemented. Args: "+JSON.stringify(search_json));
        return this.tableData
    }
    _sortHandler(sort_json) {
        console.log(`Sort handler unimplemented. Args: ${JSON.stringify(sort_json)}`);
        return this.tableData;
    }
    _updateHandler(update_json) {
        console.log("Update handler unimplemented. Args: "+JSON.stringify(update_json));
        return this.tableData;
    }
    _deleteHandler(delete_json) {
        console.log("Delete handler unimplemented. Args: "+JSON.stringify(delete_json));
        return this.tableData;
    }
    /*
        HANDLERS
    */
    _searchButtonHandler(event) {
        const column = event.currentTarget.dataset.col;
        const search = event.currentTarget.value;
        if (search !== "")
            this.searchContents[column] = search;
        else
            delete this.searchContents[column];
        this.tableData = this._searchHandler(this.searchContents);
        this.populateBody(this.tableData);
    }

    _sortButtonHandler(event) {
        // TODO update other checkboxes to greyed out state on click. Use input.indeterminate method to set all other sort checkboxes to inactive and greyed out
        const currentButton = event.currentTarget
        const column = currentButton.dataset.col;
        let state;

        if (currentButton.checked === true) {
            state = "ASC";
        }
        else {
            state = "DESC";
        }
        for (const btn in this.sortButtons) {
            this.sortButtons[btn].indeterminate = true;
            this.sortButtons[btn].classList.remove("sort-asc", "sort-desc");
            this.sortButtons[btn].classList.add("sort-none");
        }
        currentButton.classList.remove("sort-asc", "sort-desc", "sort-none");
        currentButton.classList.add(`sort-${state.toLowerCase()}`);
        currentButton.indeterminate = false;
        currentButton.checked = (state === "ASC");
        const sort = {"direction": state, "column": column};
        this.tableData = this._sortHandler(sort);
        this.populateBody(this.tableData);
    }
    _addEntryButtonHandler(event) {
        // TODO make this a separate form
        console.log("add entry button unimplemented")
    }
    _updateButtonHandler(event) {
        const button = event.currentTarget
        const rowIndex = button.dataset.rowIndex
        const currentRow = this._getRow(rowIndex);
        if (button.value === "EDIT") {
            if (this.prevEditRow) {
                this._setRowFormatting(this.prevEditRow, 'normal');
            }
            this._setRowFormatting(currentRow, 'edit');
        }
        else if (button.value === "CONFIRM") {
            // TODO get data from table row
            const update = {}
            const inputs = currentRow.querySelectorAll("input[data-column]");
            inputs.forEach(input => {
                const col = input.dataset.column;
                update[col] = input.value;
            })
            update["id"] = button.dataset.id;
            this._setRowFormatting(this.prevEditRow, 'normal');
            this.prevEditRow = null;
            this.tableData = this._updateHandler(update);
            this.populateBody(this.tableData);
        }
        else {
            if (this.prevEditRow) {
                this._setRowFormatting(this.prevEditRow, 'normal');
                this.prevEditRow = null;
            }
        }
    }
    _deleteButtonHandler(event) {
        const button = event.currentTarget;
        const id = button.dataset.id;
        if (button.value === "DELETE") {
            if (this.prevDeleteButton) {
                this.prevDeleteButton.value = "DELETE";
                this.prevDeleteButton.classList.remove("confirmButton")
            }
            button.value = "CONFIRM";
            button.classList.add("confirmButton");
            this.prevDeleteButton = button;
            window.setTimeout(() => {
                button.classList.remove("confirmButton");
                button.value = "DELETE";
            }, 2000);
        }
        else if (button.value === "CONFIRM") {
            button.value = "DELETE";
            button.classList.remove("confirmButton");
            // TODO do server delete stuff
            this.tableData = this._deleteHandler({"id": id});
            this.populateBody(this.tableData);
        }
        else {
            button.value = "DELETE";
            button.classList.remove("confirmButton");
        }
    }

    _getRow(rowIndex) {
        const row = this.rows[rowIndex];
        if (!row) {
            throw (`Failed to fetch row ${rowIndex}`)
        }
        return row
    }

    _setRowFormatting(row, formatting) {
        const rowIndex = row.dataset.rowIndex;
        const data = this.tableData[rowIndex];
        let newRow;
        try {
            switch(formatting) {
                case 'edit':
                    newRow = this.generateRow(data, rowIndex, {'inputFields': true});
                    this.body.replaceChild(newRow, row);
                    this.rows[rowIndex] = newRow;
                    this.prevEditRow = newRow;
                    break;
                default:
                    newRow = this.generateRow(data, rowIndex);
                    this.body.replaceChild(newRow, row);
                    this.rows[rowIndex] = newRow;
                    this.prevEditRow = newRow;
            }
        }
        catch (e) {
            console.error(`Error updating row ${rowIndex}: ${e}`)
        }
    }
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