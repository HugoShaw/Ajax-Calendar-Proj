// Author: Wenxin (Hugo) Xue &　Anamika Basu
// Email: hugo@wustl.edu

//citation: https://www.w3schools.com/js/js_cookies.asp
//returns the value of a specified cookie
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

//calendar helper functions from wiki
(function () {
    "use strict";
    Date.prototype.deltaDays = function (n) {
        // relies on the Date object to automatically wrap between months for us
        return new Date(this.getFullYear(), this.getMonth(), this.getDate() + n);
    };

    Date.prototype.getSunday = function () {
        return this.deltaDays(-1 * this.getDay());
    };
}());

function Week(initial_d) {
    "use strict";

    this.sunday = initial_d.getSunday();


    this.nextWeek = function () {
        return new Week(this.sunday.deltaDays(7));
    };

    this.prevWeek = function () {
        return new Week(this.sunday.deltaDays(-7));
    };

    this.contains = function (d) {
        return (this.sunday.valueOf() === d.getSunday().valueOf());
    };

    this.getDates = function () {
        var dates = [];
        for (var i = 0; i < 7; i++) {
            dates.push(this.sunday.deltaDays(i));
        }
        return dates;
    };
}

function Month(year, month) {
    "use strict";

    this.year = year;
    this.month = month;

    this.nextMonth = function () {
        return new Month(year + Math.floor((month + 1) / 12), (month + 1) % 12);
    };

    this.prevMonth = function () {
        return new Month(year + Math.floor((month - 1) / 12), (month + 11) % 12);
    };

    this.getDateObject = function (d) {
        return new Date(this.year, this.month, d);
    };

    this.getWeeks = function () {
        var firstDay = this.getDateObject(1);
        var lastDay = this.nextMonth().getDateObject(0);

        var weeks = [];
        var currweek = new Week(firstDay);
        weeks.push(currweek);
        while (!currweek.contains(lastDay)) {
            currweek = currweek.nextWeek();
            weeks.push(currweek);
        }

        return weeks;
    };
}

function clearCalendar() {
    const calendar_body = document.getElementById('calendar_body');
    while (calendar_body.childNodes.length > 1) {
        calendar_body.removeChild(calendar_body.lastChild);
    }
}

function deletePopupCategories() {
    const options = document.getElementById('event_categories');
    while (options.childNodes.length > 0) {
        options.removeChild(options.lastChild);
    }
}

function deleteCategories() {
    const categories = document.getElementById('categories');
    while (categories.childNodes.length > 5) {
        categories.removeChild(categories.lastChild);
    }
}

function getCheckedCategories() {
    let checkedCategories = [];
    //for checkbox elements in parent 

    if (document.getElementById('remember').checked) {
        alert("checked");
    }
}


// Converts time to string so it can be displayed
// code source http://stackoverflow.com/questions/29206453/best-way-to-convert-military-time-to-standard-time-in-javascript
function convert(timeinput) {
    time = timeinput.split(':'); // convert to array

    var hour = Number(time[0]);
    var minutes = Number(time[1]);
    var seconds = Number(time[2]);

    // calculate
    var timeValue;

    if (hour > 0 && hour <= 12) {
        timeValue = "" + hour;
    } else if (hour > 12) {
        timeValue = "" + (hour - 12);
    } else if (hour == 0) {
        timeValue = "12";
    }
    if (minutes == 0) {
        timeValue += (hour >= 12) ? "pm" : "am";
    }
    else {
        timeValue += (minutes < 10) ? ":0" + minutes : ":" + minutes;  // get minutes
        timeValue += (hour >= 12) ? "pm" : "am";  // get AM/PM
    }
    return timeValue;
}

