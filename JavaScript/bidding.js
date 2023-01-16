let bid_item = undefined;
let price_box = document.getElementById('input_price');
let dateCountDown = document.getElementById('dateCountDown');
let errorMessage = document.getElementById('eror_msg');
let num_of_biders = document.getElementById('biders');
let user_price = document.getElementById('user_price');
let current_bid_price = document.getElementById('bid_price');
let price_sugguestion = document.getElementById('price_sugguestion');
let place_bid_btn = document.getElementById('place_bid_button');
opening_Price = parseFloat(opening_Price.substr(1, opening_Price.length));
live_bid_update(); //If not called, we won't be having current highest amount ready to use for input validation.
setInterval(live_bid_update,8000);
itemDateCountDown(); //If not called countdown is invisible until 1 second passes when setInterval work.
setInterval(itemDateCountDown, 1000);
(user_id === "0") ? show_hide_notification(false) : show_hide_notification(true) ;

function live_bid_update()
{
    let key = new XMLHttpRequest();
    key.open("GET", "Ajax/bidding.php?item_id=" + item_id, true);
    key.onreadystatechange = function() {
        if (this.status === 200 && this.readyState === 4) {
            bid_item = new Bid_item(JSON.parse(key.responseText));
            num_of_biders.innerText = bid_item.number_Of_Bids;
            user_price.innerText = bid_item.user_pricer;
            current_bid_price.innerText = bid_item.current_price;
            price_sugguestion.innerText = bid_item.current_price;
        }
    }
    key.send();
}

/**
 * This is called only when user amount on a bid is meeting the rules. It takes in
    user input amount then run AJAX call to pass the amount as new price for a bid
    the web server. It validate server respond to check if process is success then
    it updates the "Bidding Section" area on UI otherwise an error message appears.
 */
function place_bid_AJAX(amount)
{
    let fam = new XMLHttpRequest();
    fam.open("GET", "Ajax/bidding.php?bid_amount=" + amount +"&item_ID=" + item_id, true);
    fam.onreadystatechange = function() {
        if (this.status === 200 && this.readyState === 4) {
            if(fam.responseText.toString().startsWith("t"))
            {
                live_bid_info_update(amount);
                return;
            }
            errorMessage.innerText = "Seems like a technical problem occurred, please try again later or contact support team.";
        }
    }
    fam.send();
}

/**
 *  This method is called by "Place Bid" button on click and amount text box with onkeyup event.
    It takes care of enabling and disabling place bid button based on input validation results.
    If called by onkeyup event then it only run input validation check up but if with the button
    click of new bid then it passes user given price into place_bid_ajax to complete the process.
*/
function checkUserBidPrice(button_clicked)
{
    let price = price_box.value;
    place_bid_btn.disabled = true;
    errorMessage.style.color = "red";
    if(validateInput(price))
    {
        if(button_clicked)place_bid_AJAX(parseFloat(price).toFixed(2));
        place_bid_btn.disabled = false;
    }
}

class Bid_item
{
    constructor(bid_item) {
        this.user_price = bid_item.user_price;
        this.current_bid_price = bid_item.currentMaxPrice;
        this.numberOfBids = bid_item.num_of_bids;
    }
    get number_Of_Bids() { return this.numberOfBids; }
    get user_pricer(){ return this.user_price; }
    get current_price() { return this.current_bid_price; }
}

/** This function is called on successful bid placement, it updates the prices and display message. */
function live_bid_info_update(amount)
{
    errorMessage.style.color = "green";
    errorMessage.innerText = "Bid has been placed!!";
    user_price.innerText = "£" + amount;
    current_bid_price.innerText = "£" + amount;
    price_box.value = "";
    price_sugguestion.innerText = amount;
}

/**
*This method does the following to given bid amount as check up step:
*   1- Checks if no amount is given.
*   2- Removes any white spaces from amount.
*   3- Ensure amount is given is right format with regular expressions.
*   4- Get the current amount on bid
*   5- Checks if price meets item minimum price with some rules.
*   6- Update message label and return boolean on success and failure.
 */
function validateInput(price)
{
    if(price !== "")
    {
        price = price.replace(/ /g,'');
        if((new RegExp('^[\\d]*\\.{0,1}[\\d]{0,6}$')).test(price) && (new RegExp('^[^\.]')).test(price))
        {
            let currentPrice = remove_bound_sign(bid_item.current_price);
            if(price >=  (currentPrice + 1.0) && price <= 6000 && price >= opening_Price)
            {
                errorMessage.innerText = '';
                return true;
            }
            errorMessage.innerText = "Bidding amount must meet opening price " + opening_Price + " & be at least £1 higher than current amount & is less than 6k.";
            return false;
        }
        errorMessage.innerText = "Ops, please ensure only numbers used and in correct format!";
        return false;
    }
    errorMessage.innerText = '';
    return false;
}

/** Calculate date difference between item date/time and current date/time then setup
 days, hours, minutes and seconds. It updates the countdown timer labels as results
 of calculation. If date are meet or passed, an expiry message appears. This
 method is called by setInterval() function every second.
 */
function itemDateCountDown()
{
    //Prepare the date we are counting down to
    let countDownDate = new Date(item_date).getTime();
    //Get Today's time and date
    let currentDate = new Date().getTime();
    //Get the distance between given dates
    let dateDistance = countDownDate - currentDate;
    // Time calculations for days, hours, minutes and seconds
    let days = Math.floor(dateDistance / (1000 * 60 * 60 * 24));
    let hours = Math.floor((dateDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    let minutes = Math.floor((dateDistance % (1000 * 60 * 60)) / (1000 * 60));
    let seconds = Math.floor((dateDistance % (1000 * 60)) / 1000);
    if(seconds < 10)seconds = "0" + seconds;
    if(hours < 10)hours = "0" + hours;
    if(days < 10)days = "0" + days;
    if(minutes < 10)minutes = "0" + minutes;
    dateCountDown.innerText = days + " : " + hours + " : " + minutes + " : " + seconds;
    //Checks if date is expired, if so makes input elements readonly, hide date timer and labels.
    if (dateDistance < 0) {
        dateCountDown.innerText = "AUCTION EXPIRED";
        set_timeLabels_display(false);
        place_bid_btn.disabled = true;
        price_box.disabled = true;
    }
}

/** Takes in boolean value to determine if dates labels under the countdown will be visible or not. */
function set_timeLabels_display(isVisible)
{
    let time_labels = document.getElementsByClassName("dateLabels");
    let tem = '';
    (isVisible) ? tem = "block" : tem = "none";
    for (x = 0; x < time_labels.length; x++) {
        time_labels[x].style.display = tem;
    }
}

/** Remove the pound sign and convert the amount in string into float number. */
function remove_bound_sign(amount)
{
    amount = amount.replace(/ /g,'');
    return parseFloat(amount.substr(1, amount.length));
}


/*   ------------------------------------   Code related to Notification Message Area  ------------------------------------   */

function show_hide_notification(setVisible)
{
    let notWindow = document.getElementById("exampleModalCenter");
    if(setVisible)
    {
        disableScreen(false);
        get_Notification();
        notWindow.style.visibility = "block";
        return;
    }
    notWindow.style.visibility = "hidden";
    disableScreen(true);
}

function disableScreen(isEnabled)
{
    if(isEnabled)
    {
        let blue_layer = document.getElementById("disablePage");
        if(blue_layer !== null) blue_layer.remove();
        return;
    }
    let div= document.createElement("div");
    div.className += "overlay";
    div.id = "disablePage";
    document.body.appendChild(div);
}

function get_Notification()
{
    let usr_price = remove_bound_sign(user_price.innerText);
    let bid_price = remove_bound_sign(current_bid_price.innerText);
    let body_title;
    let body_message;
    if(dateCountDown.innerText.startsWith("A"))
    {
        let win_loss = "and others haven't bid on";
        if(usr_price < bid_price) win_loss = "LOST";
        if(usr_price === bid_price) win_loss = "WON";
        if(bid_price !== 0.0 && usr_price === 0.0) win_loss = "have not bid with";
        body_title = "Item Expired";
        body_message = "This item is outdated now, you " + win_loss + " this lot.<span class='text-info'> why don't you Seek alternative lots?!</span>";
    }
    else if(bid_price === 0.0)
    {
        body_title = "Fresh Item";
        body_message = "The current price is £0.0 <span class='text-warning'><strong>Hurry UP</strong></span> and be the first bidder.";
    }
    else if(bid_price !== 0.0 && usr_price < bid_price)
    {
        body_title = "Losing the Lead";
        body_message = "Someone has bid with higher amount, " + "<span class='text-danger'>can you believe this??</span> Take the lead again by paying £"
            + (usr_price + (bid_price - usr_price) + 1.0) + ".";
    }
    else if(usr_price === 0.0 && bid_price > 0.0)
    {
        body_title = "Challenge Time";
        body_message = "The item already been bid on by others, <span class='text-danger'>accept the challenge</span> and offer higher price £" +
            (usr_price + (bid_price - usr_price) + 1.0) + ".";
    }
    else
    {
        body_title = "You are Winning";
        body_message = "You are still on the lead of winning the bid <span class='text-success'>OUTSTANDING!!</span> Please keep an eye on this item as others may pay higher.";
    }
    document.getElementById("notificationTitle").innerHTML = body_title;
    document.getElementById("notificationMessage").innerHTML = body_message;
}