//deals with displaying the calendar and forms 

if (getCookie('token') != "") {
    $('#login_form').hide();
    $('#register_form').hide();
    $('#logout_btn').show();
    $('#categories').show();
    secondFunction(); //async callback so that categories HTML is loaded before jQuery starts detecting changes
}
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const presentDate = new Date();
const presentMonth = presentDate.getMonth();
let currentYear = presentDate.getFullYear();
// For our purposes, we can keep the current month in a variable in the global scope
let currentMonth = new Month(currentYear, presentMonth); // currentMonth

updateCalendar(currentMonth, currentYear);




// Change the month when the "next" button is pressed
document.getElementById("next_month_btn").addEventListener("click", function (event) {
    currentMonth = currentMonth.nextMonth();
    if (currentMonth.month == 0) {
        currentYear += 1;
    }
    updateCalendar(currentMonth, currentYear);
}, false);

// Change the month when the "previous" button is pressed
document.getElementById("prev_month_btn").addEventListener("click", function (event) {
    currentMonth = currentMonth.prevMonth();
    if (currentMonth.month == 11) {
        currentYear -= 1;
    }
    updateCalendar(currentMonth, currentYear);
}, false);


// This updateCalendar() function only alerts the dates in the currently specified month.  You need to write
// it to modify the DOM (optionally using jQuery) to display the days and weeks in the current month.
function updateCalendar(currMonth, year) {
    document.getElementById('month_display').textContent = months[currMonth.month] + " " + year;
    clearCalendar();
    const weeks = currMonth.getWeeks();
    for (var w in weeks) {
        const days = weeks[w].getDates();
        let row = document.createElement("tr");
        for (var d in days) {
            if (days[d].getMonth() != currMonth.month) {
                let cell = document.createElement("td");
                cell.appendChild(document.createTextNode(""));
                row.appendChild(cell);
            }
            else {
                let cell = document.createElement("td");
                $calendar_date = days[d].toISOString().substring(0, 10);
                let celldiv = document.createElement("div");
                celldiv.appendChild(document.createTextNode(days[d].getDate()));
                celldiv.setAttribute("id", $calendar_date);
                celldiv.setAttribute("class", "open");
                cell.appendChild(celldiv);
                row.appendChild(cell);
                if (getCookie('token') != "") {
                    showEvents($calendar_date);
                }
            }
            document.getElementById('calendar_body').appendChild(row);
        }
    }
}

//adds in event HTML for a particular day 
function showEvents(day) {
    const data = { 'date': day, 'token': getCookie("token") };
    fetch("show_events_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //for events that do fit in one calendar day 
                if (data.events.length <= 3) {
                    for (let i = 0; i < data.events.length; i++) {

                        let checkedCategories = [];
                        $('input[type=checkbox]').each(function () {
                            if (this.checked) {
                                checkedCategories.push(parseInt(this.id.substring(3)));

                            }
                        });
                        if (checkedCategories.includes(data.events[i].category_id)) {
                            let eventdiv = document.createElement("div");
                            eventdiv.appendChild(document.createTextNode(convert(data.events[i].event_time)));
                            eventdiv.appendChild(document.createTextNode(" " + data.events[i].event_title));
                            eventdiv.appendChild(document.createElement("br"));
                            eventdiv.style.color = document.querySelector(`[for="cat${data.events[i].category_id}"]`).style.color
                            eventdiv.setAttribute("class", "events");
                            eventdiv.setAttribute("id", data.events[i].event_id);
                            document.getElementById(day).appendChild(eventdiv);
                        }
                    }
                    //for events that do not all fit in one calendar day 
                } else {
                    for (let i = 0; i < 3; i++) {

                        let checkedCategories = [];
                        $('input[type=checkbox]').each(function () {
                            if (this.checked) {
                                checkedCategories.push(parseInt(this.id.substring(3)));

                            }
                        });
                        if (checkedCategories.includes(data.events[i].category_id)) {
                            let eventdiv = document.createElement("div");
                            eventdiv.appendChild(document.createTextNode(convert(data.events[i].event_time)));
                            eventdiv.appendChild(document.createTextNode(" " + data.events[i].event_title));
                            eventdiv.appendChild(document.createElement("br"));
                            eventdiv.style.color = document.querySelector(`[for="cat${data.events[i].category_id}"]`).style.color
                            eventdiv.setAttribute("class", "events");
                            eventdiv.setAttribute("id", data.events[i].event_id);
                            document.getElementById(day).appendChild(eventdiv);
                        }
                    }
                    let extraEvents = document.createElement("div");
                    if (data.events.length == 4) {
                        extraEvents.appendChild(document.createTextNode("1 more event"));
                    } else {
                        extraEvents.appendChild(document.createTextNode((data.events.length - 3) + " more events"));
                    }

                    extraEvents.setAttribute("class", "extra_events");
                    document.getElementById(day).appendChild(extraEvents);
                }

            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}

//creates the content for the dialog box containing the other events 
function showMoreEvents(day) {
    const data = { 'date': day, 'token': getCookie("token") };
    fetch("show_events_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                for (let i = 3; i < data.events.length; i++) {
                    let eventdiv = document.createElement("div");
                    eventdiv.appendChild(document.createTextNode(convert(data.events[i].event_time)));
                    eventdiv.appendChild(document.createTextNode(" " + data.events[i].event_title));
                    eventdiv.appendChild(document.createElement("br"));
                    eventdiv.setAttribute("class", "events");
                    eventdiv.setAttribute("id", data.events[i].event_id);
                    document.getElementById("show_more").appendChild(eventdiv);
                }
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}

//dynamically adds the categories HTML based on fetch call 
function showCategories(_callback) {
    deleteCategories();
    const data = { 'token': getCookie("token") };
    fetch("show_categories_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("categories").appendChild(document.createElement("br"));
                for (let i = 0; i < data.categories.length; i++) {
                    let checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "cat" + data.categories[i].category_id;
                    checkbox.value = "cat" + data.categories[i].category_id;
                    checkbox.id = "cat" + data.categories[i].category_id;
                    checkbox.checked = true;
                    let label = document.createElement('label');
                    label.setAttribute("for", "cat" + data.categories[i].category_id);
                    label.textContent = data.categories[i].category;
                    label.style.color = data.categories[i].color;
                    document.getElementById("categories").appendChild(checkbox);
                    document.getElementById("categories").appendChild(label);
                    document.getElementById("categories").appendChild(document.createElement("br"));
                }
                _callback();
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}

//fills in information for an event for user to see, edit, or delete 
function openEvent(event_id) {
    const data = { 'event_id': event_id, 'token': getCookie("token") };
    fetch("open_event_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#event_categories').val("cat" + data["events"]["category_id"]);
                $('#title').val(data["events"]["title"]);
                $('#date').val(data["events"]["date"]);
                $('#time').val(data["events"]["time"]);
                $('#description').val(data["events"]["description"]);
                $('#save_changes_btn').show();
                $('#save_btn').hide();
                $('#delete_event_btn').show();
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}

//for the event adding/editing section, this function updates the categories dropdown 
function showPopupCategories() {
    deletePopupCategories();
    const data = { 'token': getCookie("token") };
    fetch("show_categories_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                for (let i = 0; i < data.categories.length; i++) {
                    let childrenCount = document.getElementById("event_categories").getElementsByTagName('option').length;
                    if (childrenCount < data.categories.length) {
                        let option = document.createElement("option");
                        option.value = "cat" + data.categories[i].category_id;
                        option.textContent = data.categories[i].category;
                        document.getElementById("event_categories").appendChild(option);
                    }

                }
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}


//when calendar day is pressed, user can add a new event 
$(document).on("click", ".open", function () {
    if (getCookie('token') != "") {
        if (!$('#show_more').dialog('isOpen')) {
            showPopupCategories();
            $("#event_popup").show();
            $('#date').val(this.id);
            $('#title').val("");
            $('#description').val("");
            $('#time').val("");
            $('#save_btn').show();
            $('#save_changes_btn').hide();
            $('#delete_event_btn').hide();
        }

    }
});

//when cancel is pressed, the event information disappears 
$(document).on("click", "#cancel_btn", function () {
    if (getCookie('token') != "") {
        $("#event_popup").hide();
    }
});

//when an event is pressed, the event information will pop up 
$(document).on("click", ".events", function () {

    if (getCookie('token') != "") {
        $("#event_popup").show();
        showPopupCategories();
        openEvent(this.id);
        $("#event_id").val(this.id);

    }
});

//when the user has saved the event information, the event information disappears
$(document).on("click", "#save_btn", function () {
    if (getCookie('token') != "") {
        $("#event_popup").hide();
    }
});

//when the user has saved the event information, the event information disappears
$(document).on("click", "#save_changes_btn", function () {
    if (getCookie('token') != "") {
        $("#event_popup").hide();
    }
});

//when delete is pressed, the event information disappears 
$(document).on("click", "#delete_event_btn", function () {
    if (getCookie('token') != "") {
        $("#event_popup").hide();
    }
});

//when more events is pressed, the missing events appear in a dialog box 
$(document).on("click", ".extra_events", function () {
    if (getCookie('token') != "") {
        showMoreEvents(this.parentNode.id);
        $("#show_more").dialog();
    }
});

// when the dialog box is closed, its HTML is cleared so there is no duplication 
$("#show_more").dialog({
    close: function () {
        if (getCookie('token') != "") {
            var main = document.getElementById('show_more');
            while (main.childNodes.length > 1) {
                main.removeChild(main.lastChild);
            }
        }
        return false;
    }
});


//brings the user back to the current month 
$(document).on("click", "#today_btn", function () {

    currentYear = presentDate.getFullYear();
    currentMonth = new Month(currentYear, presentMonth);

    updateCalendar(currentMonth, currentYear);

});


//prevents the dialog box from showing up when the page is refreshed
$(document).ready(function () {
    $("#show_more").dialog('close');
});


//async callback function to deal with problem of jQuery change function running when no checkbox inputs were presed 
function secondFunction() {
    showCategories(function () {
        $('input[type=checkbox]').change(
            function () {
                if (this.checked) {
                    updateCalendar(currentMonth, currentYear);
                }
                if (!this.checked) {
                    updateCalendar(currentMonth, currentYear);
                }
            });
    });
}




