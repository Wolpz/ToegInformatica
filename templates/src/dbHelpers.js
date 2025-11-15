class CustomTable {
    // TODO create rebindable table field generation functions to support images and other code
    // TODO set required fields as such in html and table metadata
    constructor(
        tableElement,
        columnNames,
        {
            fetchData = null,
            updateHandler = null,
            insertHandler = null,
            deleteHandler = null
        }
    ) {
        this.element = tableElement;
        this.columns = columnNames;
        this.tableData = [];
        this.entryForm = null;

        this.genSearchBars = (typeof fetchData === "function");
        this.genSortButtons = (typeof fetchData === "function");
        this.genUpdateButtons = (typeof updateHandler === "function");
        this.genAddEntryFields = (typeof insertHandler === "function");
        this.genDeleteButtons = (typeof deleteHandler === "function");

        this.searchContents = {"id": ""};
        this.columns.forEach(column => (this.searchContents[column] = ""));
        this.sort = {
            column: "id",
            direction: "ASC"
        };
        this.sortButtons = [];

        this.header = null;
        this.body = null;
        this.rows = [];

        this.prevEditRow = null;

        if (fetchData) {
            this.bind_searchHandler(fetchData);
            this.fetchData(this.searchContents, this.sort);
        }
        if (updateHandler) this.bind_updateHandler(updateHandler);
        if (insertHandler) this.bind_insertHandler(insertHandler);
        if (deleteHandler) this.bind_deleteHandler(deleteHandler);

        this.populateHeader()
    }


    populateHeader(
        options = {
            'searchBars': this.genSearchBars,
            'sortButtons': this.genSortButtons,
        }
    ) {
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
            const row = this.generateRow(data[rowIndex], rowIndex, {
                'updateButtons': this.genUpdateButtons,
                'deleteButtons': this.genDeleteButtons
            });
            this.rows.push(row);
            this.body.appendChild(row);
        }
        this.prevEditRow = null;
        this.prevDeleteButton = null;
    }
    
    generateRow(
        data,
        rowIndex,
        options = {}
    ) {
        const row = document.createElement("tr");
        row.dataset.rowIndex = rowIndex;
        row.dataset.id = data['id']
        for (const col of this.columns) {
            let value = "";
            if (col in data) {
                value = data[col];
            }
            if (options['inputFields'] && col !== 'id') {
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
        if (options['updateButtons']) {
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
            // Cancel and confirm buttons
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
        if (options['deleteButtons']) {
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

    generateAddEntryForm(container_element) {
        if (!this.genAddEntryFields) {
            return;
        }
        if (this.entryForm) {
            // Destruct old entry form if it already exists
            this.entryForm.remove();
            this.entryForm = null;
        }
        const form = document.createElement("form");
        form.classList.add("add-entry-form");

        for (const col of this.columns) {
            if (col === 'id') continue;
            const label = document.createElement("label");
            label.textContent = col;
            const inputField = document.createElement('input');
            inputField.type = 'text';
            inputField.name = col;
            // TODO set input field to required if required
            // inputField.required = True;
            label.appendChild(inputField);
            form.appendChild(label);
        }
        const submitButton = document.createElement("button");
        submitButton.type = "submit";
        submitButton.classList.add("addEntryButton");
        submitButton.textContent = "Add Entry";
        form.appendChild(submitButton);
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            const data = {};
            for (const col of this.columns) {
                if (col === 'id') continue;
                data[col] = form.elements[col].value;
            }
            this._insertHandler(data)
            form.reset()
        })

        this.entryForm = form;
        container_element.appendChild(this.entryForm);
    }

    /*
        RE-BINDABLES
    */
    fetchData(search_json, sort_json) {
        alert("Search handler not bound. See the heading RE-BINDING FUNCTIONS in this class on how to do this.\nArgs:\n"+JSON.stringify(search_json)+"\n"+JSON.stringify(sort_json));
        return this.tableData
    }
    _updateHandler(update_json) {
        alert("Update handler not bound. See the heading RE-BINDING FUNCTIONS in this class on how to do this.\nArgs: "+JSON.stringify(update_json));
    }
    _insertHandler(insert_json) {
        alert("Insert entry handler not bound. See the heading RE-BINDING FUNCTIONS in this class on how to do this.\nArgs: "+JSON.stringify(update_json));
    }
    _deleteHandler(delete_json) {
        alert("Delete handler not bound. See the heading RE-BINDING FUNCTIONS in this class on how to do this.\nArgs: "+JSON.stringify(delete_json));
    }

    /*
        RE-BINDING FUNCTIONS
        Passing a function pointer to these functions binds the function to be used to update the table.
        The function pointed to MUST do the following:
        - Accept as input: one or more JSON objects in the format specified at the relevant binder function.
        - Respect the 'this' context as the CustomTable class context, as this will also be rebound.
            This will let you use 'this' to refer to internal class variables and functions such as TableData if needed.
    */
    bind_searchHandler(func_ptr) {
        /*
            SearchHandler input format:
                search_json: dictionary with table column name as key and search field contents as value.
                    All column names will be present.
                sort_json: JSON object with fields:
                {
                    "direction":"ASC or "DESC" (Ascending, descending),
                    "column": column name
                }
            The rebound SearchHandler must return:
                A JSON object in the same format as this.tableData, with at least the same column headers as keys.

         */
        this.fetchData = func_ptr.bind(this);
    }

    bind_insertHandler(func_ptr) {
        /*
            Insert entry handler input format:
            insert_json: JSON object with the data to insert into the table, keyed by column name.
        */
        this._insertHandler = func_ptr.bind(this);
    }

    bind_updateHandler(func_ptr) {
        /*
            Update entry handler input format:
                update_json: key-value pairs of the row when CONFIRM button is clicked. Also includes entry id.
                Ex.
                {
                    "name":"Luke",
                    "age":"5",
                    "id":"0"
                }
         */
        this._updateHandler = func_ptr.bind(this);
    }
    bind_deleteHandler(func_ptr) {
        /*
            Delete entry handler input format:
                delete_json: JSON object that only includes the id of the entry to be deleted.
         */
        this._deleteHandler = func_ptr.bind(this);
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
            this.searchContents[column] = "";
        this.fetchData(this.searchContents, this.sort);
    }

    _sortButtonHandler(event) {
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
        this.sort = {"direction": state, "column": column};
        this.fetchData(this.searchContents, this.sort);
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
            const update = {}
            const inputs = currentRow.querySelectorAll("input[data-column]");
            inputs.forEach(input => {
                const col = input.dataset.column;
                update[col] = input.value;
            })
            update["id"] = button.dataset.id;
            console.log(update)
            this._setRowFormatting(this.prevEditRow, 'normal');
            this.prevEditRow = null;
            this._updateHandler(update);
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
            this._deleteHandler({"id": id});
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
                    newRow = this.generateRow(data, rowIndex, {
                        'inputFields': true,
                        'updateButtons': this.genUpdateButtons,
                        'deleteButtons': this.genDeleteButtons
                    });
                    this.body.replaceChild(newRow, row);
                    this.rows[rowIndex] = newRow;
                    this.prevEditRow = newRow;
                    break;
                default:
                    newRow = this.generateRow(data, rowIndex, {
                        'updateButtons': this.genUpdateButtons,
                        'deleteButtons': this.genDeleteButtons
                    });
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