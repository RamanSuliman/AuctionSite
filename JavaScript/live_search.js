/**
 * This file is holding all necessary functions needed for live searching
 * feature.
 */

var ajaxCall = new XMLHttpRequest();
var searchViewer = document.getElementById('searchViewArea');
var searchBar = document.getElementById('searchBar');
var showImage = false;
document.getElementById("keySearch").value = tokenKey;
console.log("The key at loading is: " + tokenKey);
//Generate new token every 10 seconds, used for live searching.
setInterval(updateToken,12000);

/**
* This method get the value typed in search box starts off validating the given argument
 * then trigger AJAX call to server's "liveSearch.php" file to get data from
 * in JSON format. On a respond, it checks the returned state if is successful
 * it decode JSON object into JavaScript object and pass it to another function.
*
* @param {value} value The current search box value.
*/
function getData(value)
{
    if(!validDateSearch(value)) {return;}
    ajaxCall.open("GET", "Ajax/liveSearch.php?liveSearch=" + value + "&key=" + tokenKey, true);
    ajaxCall.send();
    //Listens and handle returned data from server.
    ajaxCall.onreadystatechange = function()
    {
        //Checks if ajax call was successfully done.
        if(this.status === 200 && this.readyState === 4)
        {
            //Ensure there are no current search records.
            searchViewer.innerHTML = "";
            //Store json data into object.
            let items = JSON.parse(ajaxCall.responseText);
            display_search_results(items);
        }
    }
}

/**
 * This method takes responsibility to validate the typed value
 * sent by search box to ensure its empty of special characters and
 * has specific number of characters.
 *
 * @param {value} value The current search box value
 * @return {boolean} validated
 */
function validDateSearch(value)
{
    let accepted_Chara = /[^a-z0-9 .]/gi.test(value);
    if(!accepted_Chara && value.length >= 2) {return true;}
    searchViewer.innerHTML = "";
    return false;
}

/**
 * This method has the duty of checking over the size of given object and
 * generating an appropriate search results based on the data which this
 * object holds in.
 */
function display_search_results(items)
{
    if(items.length !== 0)
    {
        if(!JSON.stringify(items).startsWith("Invalid"))
        {
            items.forEach(function (item)
            {
                searchViewer.innerHTML += "<li class='list-group-item'><a class='text-decoration-none' href='single_item.php?id=" + item.itemID + "'>" +
                    ((showImage) ? "<img class='img-thumbnail ml-0 mr-1' src='Images/thumbnails/" + item.itemImage + ".jpg' alt='Italian Trulli'>" : "")+ item.itemName + "</a></li>";
            });
        }
        else
        {
            searchViewer.innerHTML = "<li class='list-group-item no_result'><strong>Ni</strong></li>";
        }
    }
    else
    {
        searchViewer.innerHTML = "<li class='list-group-item no_result'><strong>No results found...</strong></li>";
    }
}

/**
 * On the call of this function, the search box and search results
 * are getting discarded. This method is used mainly when user
 * exists the search box meaning removing the focus from.
 */
function clearViewArea()
{
    searchBar.value = "";
    searchViewer.innerHTML = '';
}

/**
 * This block is helping us determine if images should be showing
 * also in the search results. It takes boolean argument and
 * pass it to showImage variable.
 */
function changeImageStatue(imageVisiblity)
{
    showImage = imageVisiblity;
}

/**
 * This function is responsible of making AJAX calls to the server
 * to update token key that is attached to search request URL.
 */
function updateToken()
{
    let keyGetter = new XMLHttpRequest();
    keyGetter.open("GET", "Ajax/liveSearch.php?&changeKey=none", true);
    keyGetter.send();
    //Listens and handle returned data from server.
    keyGetter.onreadystatechange = function() {
        //Checks if ajax call was successfully done.
        if (this.status === 200 && this.readyState === 4) {
            tokenKey = JSON.parse(keyGetter.responseText);
            console.log("New key is: " + tokenKey);
            document.getElementById("keySearch").value = tokenKey;
        }
    }
}