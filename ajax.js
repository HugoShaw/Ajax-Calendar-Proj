//many fetch calls for adding, editing, deleting events as well as login, logout, and registration

$(document).ready(function () {
    $("#show_more").dialog('close');
});

//logging in 
function loginAjax() {
    const username = document.getElementById("username").value; // Get the username from the form
    const password = document.getElementById("password").value; // Get the password from the form

    // Make a URL-encoded string for passing POST data:
    const data = { 'username': username, 'password': password };

    fetch("login_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.cookie = `token=${data["token"]}`;
                $('#login_form').hide();
                $('#register_form').hide();
                $('#logout_btn').show();
                $('#categories').show();
                updateCalendar(currentMonth, currentYear);
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
                alert("You've been logged in!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}
document.getElementById("login_btn").addEventListener("click", loginAjax, false);

//registering new user
function registerAjax() {
    const new_username = document.getElementById("new_username").value; // Get the new_username from the form
    const new_password = document.getElementById("new_password").value; // Get the new_password from the form

    // Make a URL-encoded string for passing POST data:
    const data = { 'username': new_username, 'password': new_password };

    fetch("register_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#logout_btn').hide();
                $('#username').val("");
                $('#password').val("");
                $('#new_username').val("");
                $('#new_password').val("");
                alert("You've been registered!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));

}
document.getElementById("register_btn").addEventListener("click", registerAjax, false);

//logging out
function logoutAjax() {
    const data = { 'token': getCookie("token") };

    fetch("logout_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.cookie = "token= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
                $('#login_form').show();
                $('#register_form').show();
                $('#event_popup').hide();
                $('#categories').hide();
                $('#logout_btn').hide();
                $('#username').val("");
                $('#password').val("");
                $('#new_username').val("");
                $('#new_password').val("");
                updateCalendar(currentMonth, currentYear);
                alert("You've been logged out!");
            }
        })
        .catch(err => console.error(err));
}
document.getElementById("logout_btn").addEventListener("click", logoutAjax, false);

//adding new event
function addEventAjax() {
    const data = {
        'title': $('#title').val(),
        'date': $('#date').val(),
        'time': $('#time').val(),
        'category': $('#event_categories').val(),
        'description': $('#description').val(),
        'token': getCookie("token")
    };

    fetch("add_event_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCalendar(currentMonth, currentYear);
                alert("Event has been successfully added!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}
document.getElementById("save_btn").addEventListener("click", addEventAjax, false);

//editing existing event
function editEventAjax() {
    const data = {
        'event_id': $('#event_id').val(),
        'category_id': $('#event_categories').val(),
        'title': $('#title').val(),
        'date': $('#date').val(),
        'time': $('#time').val(),
        'description': $('#description').val(),
        'token': getCookie("token")
    };

    fetch("edit_event_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCalendar(currentMonth, currentYear);
                alert("Event has been successfully updated!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));


}
document.getElementById("save_changes_btn").addEventListener("click", editEventAjax, false);

//deleting event
function deleteEventAjax() {
    const data = { 'event_id': $('#event_id').val(), 'token': getCookie("token") };
    fetch("delete_event_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCalendar(currentMonth, currentYear);
                alert("Event has been successfully deleted!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}
document.getElementById("delete_event_btn").addEventListener("click", deleteEventAjax, false);

//adding a category 
function addCategoryAjax() {
    const data = { 'name': $('#new_cat').val(), 'token': getCookie("token") };
    fetch("add_category_ajax.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
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
                showPopupCategories();
                alert("Category has been successfully added!");
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}
document.getElementById("add_cat_btn").addEventListener("click", addCategoryAjax, false);

// function addShareAjax() {
//     const data = { 'friend': $('#new_share').val(), 'token': getCookie("token") };
//     fetch("add_share_ajax.php", {
//         method: 'POST',
//         body: JSON.stringify(data),
//         headers: { 'content-type': 'application/json' }
//     })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 alert("Calendar has successfully been shared!");
//             } else {
//                 alert(data.message);
//             }
//         })
//         .catch(err => console.error(err));
// }

// document.getElementById("add_share_btn").addEventListener("click", addShareAjax, false);
