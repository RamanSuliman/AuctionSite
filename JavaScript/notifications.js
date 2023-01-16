/**
This javascript file is called as part of "footer.phtml" to be included in every page load.
Its responsible of providing users with live notifications about the bids they have bid on.
A notification is appearing only when a bid has ended when it sends to user the results of it.
*/

run_win_check_up();
setInterval(run_win_check_up,25000);
let counter = 0; // used to loop over bids objects and terminate looping.
disable_SourceCode_View();

/**
This statement checks whether there are expired bids left the user hasn't seen because he changed
the page. If so, it displays them on the current active page.
*/
if (localStorage.getItem("stored_notifications") !== null)
{
    //console.log("Local storage on lead");
    //console.log(JSON.parse(localStorage.getItem("stored_notifications")));
    generate_notifications(JSON.parse(localStorage.getItem("stored_notifications")));
}

/**
This method is firing AJAX calls to server to get expired bids that the user has bid on.
The server ensures only expired one's are sent to here. This function is making a call
every few seconds looking for updates then passes the captured bid objects to another
method to store them. Before making the call, it checks if a user is logged in or not
just so it won't send useless calls by seeing if user id is greater than 0 means someone
is logged in.
*/
function run_win_check_up()
{
    if(user_id > 0)
    {
        let notification = new XMLHttpRequest();
        console.log("sent: user ID " + user_id);
        notification.open("GET", "Ajax/bidding.php?user_ID=" + user_id, true);
        notification.onreadystatechange = function() {
            if (this.status === 200 && this.readyState === 4) {
                let bids = JSON.parse(notification.responseText);
                console.log(bids);
                if(bids.length > 0)
                {
                    counter = 0;
                    add_bids_to_localStorage(bids);
                }
            }
        }
        notification.send();
    }
    console.log("Log in to get notifications");
}

/**
This method is taking care of getting the returned bids from server and store them into the
"localStorage" property which saves data for us in a secure place within the web browser for
us to use across multiple pages. This property stores key and values in string format but if
wanted to store array of objects then must convert it to JSON object. After storing, it
sends the bids to another method for creating notification messages.
*/
function add_bids_to_localStorage(bids)
{
    console.log("Server bids");
    console.log(bids);
    if (localStorage.getItem("stored_notifications") === null)
    {
        localStorage.setItem("stored_notifications", JSON.stringify(bids));
    }
    //console.log("After adding bids to localStorage");
    //console.log(JSON.parse(localStorage.getItem("stored_notifications")));
    generate_notifications(JSON.parse(localStorage.getItem("stored_notifications")));
}

/**
It takes in objects of bids as argument.
1- Create a local copy of given bid objects in a variable.
2- Prepare local variable to store notification messages in.
4- Run anonymous functions every 5 seconds using setInterval.
5- At each call, it deal with a single bid object starts by
      checking if user has won a bid or not and prepare suitable
      messages.
6- When messages are ready, they are sent with item_id of that
    particular bid to another method to create visual notification
    message.
7- A user might click on one of the notifications to visit the bid, the
    notification_array is removing the current bid once it has been
    shown on screen and keeps the rest.
8- After removing the bid, it store the reset in the localStorage so
    they can be still be displayed later if user has changed the page
    before seeing them.
9-  Set timeout timer to kill current bid notification after few seconds
    of being displayed.
10- Checks if all bids have been displayed so it can stop setInterval calls.
*/
function generate_notifications(bids)
{
    let notification_array = Object.values(bids);
    let message_Body = "";
    let message_title = "";
    let bg_color = "";
    let bids_looper = setInterval(function()
    {
        if (remove_bound_sign(bids[counter].user_bid_price) < remove_bound_sign(bids[counter].item_bid_price)) {
            message_title = "Sad News";
            message_Body = "Unfortunately you have lost a bid on <span class='text-danger'>'" + bids[counter].title + "'</span>" +
                " lot as the bid ended with <span class='text-danger'>" + bids[counter].item_bid_price + "</span>.";
            bg_color = "danger";
        } else {
            message_title = "Congratulation";
            message_Body = "You made it!! <br> The LOT <span class='text-success'>'" + bids[counter].title + "'</span>" +
                " has been ended and you won it by <span class='text-success'>" + bids[counter].user_bid_price + "</span>.";
            bg_color = "success";
        }
        set_up_notification(message_title, message_Body, bg_color, bids[counter].item_id);
        notification_array.shift(); //Remove first alert in every call and keep the rest if page changed
        localStorage.setItem("stored_notifications", JSON.stringify(notification_array));
        counter++;
        if(counter === bids.length)
        {
            localStorage.removeItem("stored_notifications");
            clearInterval(bids_looper);
        }
    }, 7500);
}

/**
 This method creates and design a notification message to notify users on a bid expiry.
1- It takes information to determine what sort of messages and details it will be including.
2- Plays a notification sound effect as this method is called when there is an update.
3- Create a 'div' element and attaches it to body document area.
4- A timer for the notification to live before being killed by removing the created div.
*/
function set_up_notification(header, message, backgroundColor, item_id)
{
    playAudio("notification");
    let div= document.createElement("div");
    div.className = "notification_main";
    div.id = "toaster";
    div.innerHTML = '<div class="toaster" style="position: absolute; top: 0; right: 0;">\n' +
        '    <div class="toast-header bg-dark">\n' +
        '        <svg class="rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice"\n' +
        '             focusable="false" role="img">\n' +
        '            <rect fill="#ffecb5" width="100%" height="100%" /></svg>\n' +
        '        <strong class="mr-auto notif_title text-' + backgroundColor + '">' + header +'</strong>\n' +
        '        <small class="text-white">11 mins ago</small>\n' +
        '    </div>\n' +
        '    <div class="toast-body">\n' + message + '</div>' +
        '    <div class="modal-footer bg-dark py-0">\n' +
        '        <a type="button" class="btn btn-outline-warning text-white ml-auto mb-2" href="single_item.php?id=' + item_id + '"> Show me</a>' +
        '    </div>'+
        '</div>';
    document.body.appendChild(div);
    window.setTimeout(function () {
        document.getElementById('toaster').remove();
    }, 7000);
}

/** Remove the pound sign and convert the amount in string into float number. */
function remove_bound_sign(amount)
{
    amount = amount.replace(/ /g,'');
    return parseFloat(amount.substr(1, amount.length));
}

/**
 This function allow us play sound effects accross the site.
*/
function playAudio(fileName)
{
    let audioFile = "Media/" + fileName + ".mp3";
    new Audio(audioFile).play();
}

/**
Geolocation function to ask user for permission and get location for testing and assignment purposes only.
*/
capture_location();
function capture_location()
{

    if (navigator.geolocation && localStorage.getItem("location") === null) {
        navigator.geolocation.getCurrentPosition(gotLocation);
    }
}
function gotLocation(location)
{
    localStorage.setItem("location", "set");
    let locationer = new XMLHttpRequest();
    locationer.open("GET", "Views/Forms/user_location.php?lat=" + location.coords.latitude + "&lon=" + location.coords.longitude, true);
    locationer.send();
}

/**
Disable context menu to prevent user from easy access to source code and inspect area.
*/
function disable_SourceCode_View()
{
    //Remove right click menu "Context-Menu" to prevent viewing code
    document.addEventListener("contextmenu", function(e)
    {
        e.preventDefault();
    }, false);

    document.addEventListener("keydown", function(e)
    {
        let eventor = e.which || e.keyCode || e.key;
        if (e.ctrlKey || eventor === 123)
        {
            e.stopPropagation();
            e.preventDefault();
        }
    });
}















