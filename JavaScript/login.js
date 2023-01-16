let pass_message = document.getElementById("pass_status");
let sign_up_log_in_btn = document.getElementById("sign_log");
let email = document.getElementById("email");
let passw = document.getElementById("password");
let animation_loader = document.getElementsByClassName("animation_loader")[0];
let attempts = 0;

/**
This method is called by password field to validate user input and its being called at the
release of keyboard key.
1- Ensure sign in/up button are disappeared until given password meets the safety rules.
2- Password must start with capital letter as rest can be mix of letters and numbers only.
3- If rules are meet, button appear for user to trigger.
*/
function validate_password(input)
{
    sign_up_log_in_btn.style.display = "none";
    if(input.length !== 0) {
        if (!(new RegExp('\\W')).test(input)) {
            if (input.length >= 6 && (new RegExp('\^[A-Z]')).test(input) && (new RegExp('[a-zA-Z]')).test(input) && (new RegExp('[0-9]')).test(input))
            {
                pass_message.innerText = "";
                sign_up_log_in_btn.style.display = "block";
                return;
            }
            pass_message.innerHTML = "Password should start by <span class='text-warning'> capital letter </span> with at least <span class='text-warning'> 6 </span> characters in length contianing letters " +
                "and <span class='text-warning'> numbers </span>.";
            return;
        }
        pass_message.innerHTML = "<span class='text-warning'> CAUTION </span>Special characters are invalid input, make sure your password is excludes characters like<span class='text-warning'> ?!.*< </span>etc.";
        return;
    }
    pass_message.innerText = "";
}

/**
This method makes ajax calls to server to pass user details of login and signup.
1- Take two parameters first is arguments to be sent to the server like email etc.
    second is determine the type of details if 1 is login and 2 is sign up call.
2- On respond it checks if server responded with ok meaning the job is done or not otherwise
    to throw an error message on the form.
*/
function login_signup_ajax_call(arguments, type)
{
    console.log(arguments);
    let login = new XMLHttpRequest();
    login.open("GET", "login.php?" + arguments, true);
    login.onreadystatechange = function() {
        if (this.status === 200 && this.readyState === 4) {
            let bid_item = JSON.parse(login.responseText);
                (type === 1 || type === 2)? filter_ajax_respond(bid_item, type) : pass_message.innerText = "Something went wrong, please contact support team.";
        }
    }
    login.send();
}

/**
It is called by signup and login buttons onclick event. The argument determine which
    button was clicked.
1- Starts off by checking if both email and password are provided.
2- Checks if button clicked is login or not.
3- Checks if number of login failure has reached 4 attempts so it prevents the user from
    missing around and redirect to main page with a message displaying the security concern.
4- If attempts are fine, it prepare the arguments which will be sent by ajax call and pass them
    to the method of making calls.
*/
function login_signup(operation)
{
    if(check_empty())return;
    let argument;
    if(operation === "login")
    {
        if(attempts >= 3)
        {
            animation_loader.style.display = "block";
            pass_message.innerHTML = "<span class='text-warning'>You have made four incorrect attempts on this account, please try again later.</span>"
            setTimeout(function (){window.location.href = "auctions.php";}, 4500);
            return;
        }
        argument = "log_in=1&password=" + passw.value + "&email=" + email.value;
        login_signup_ajax_call(argument, 1);
    }
    else
    {
        let f_name = document.getElementById("first_name").value;
        let l_name = document.getElementById("last_name").value;
        argument = "sign_up=1&first_name=" + f_name + "&last_name=" + l_name + "&password=" + passw.value + "&email=" + email.value;
        console.log(argument);
        login_signup_ajax_call(argument, 2);
    }
}

/**
This is being called when ajax has responded with true values.
1- It take server respond and the type of request like login or signup.
2- Double checking if respond was true then displays the loading animation
    with success messages indicating that the page is about to be directed.
3- On success of login/signup the user si given 3 seconds to read the message
    displayed before the page auto leaves.
4- However, if the respond wasn't true then error messages are displayed
    according to the type of clicked button.
*/
function filter_ajax_respond(respond, type)
{
    passw.value = "";
    console.log("Respond is:   " + respond);
    if(respond === true)
    {
        animation_loader.style.display = "block";
        (type === 1)?pass_message.innerHTML = "<span class='text-success'>Login has success.</span>":
            pass_message.innerHTML = "<span class='text-success'>Sign up completed, you will be redirect in a second.</span>";
        setTimeout(function (){window.location.href = "auctions.php";}, 3000);
        return;
    }
    animation_loader.style.display = "none";
    if(type === 2) {
           pass_message.innerHTML = "<span class='text-warning'> Email address already exist. </span>";
    }
    else {
        pass_message.innerHTML = "<span class='text-warning'> Invalid details were provided, please check email and password.</span>";
        attempts++;
    }
}

/**
This runs a check to see if user has provided any input in required fields
*/
function check_empty()
{
    if (!(new RegExp("^[\\w][a-zA-Z._0-9]+@[a-z]+.[a-z]+$").test(email.value)))
    {
        pass_message.innerHTML = "Please provide valid email address.";
        return true;
    }
    if(passw.value.length === 0 || email.value.length === 0)
    {
        pass_message.innerHTML = "You are missing some information, please provide in both email and password";
        return true;
    }
    return false;
}