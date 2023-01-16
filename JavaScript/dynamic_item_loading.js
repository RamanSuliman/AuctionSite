let ajaxCall = new XMLHttpRequest();
let limiter = 8;
let starter = 1;
let priceer = 'none';
let lastData = '';
let decrease = false;
let action = 'inactive';
let loadButton = document.getElementById("load_data_message");
let data = document.getElementById("load_data");

/**
Add event listener to capture document loading event and began making a call with server
to get data from.
*/
document.addEventListener("DOMContentLoaded", function(event) {
    load_country_data(auction_id, starter, limiter, priceer);
});

/**
This method takes in auction_id to retrieve lots based on, the starter meaning from returned query
    rests from where it should start grabbing data as in first load it starts by first row, the limiter
    determine how many lots can be retrieved per call and priceer used for filtering item based on their
    prices which can be max-low or -low-max.
1- On data respond, it checks if data returned so it show up loading label on the bottom meaning there is more.
2- Stores the last item ID at every call so it can be used to stop retrieving more data as if previous call last id
    was similar to current call captured id then there are no more data to load so stop loading and show a message.
3- It sends each captured item into "buildItems" method to start presenting data on the page.
4- The last method "scrollIp_Down()" is making auto scrolling based on last user scroll as auto loading data works
    by scrolling down the page to get new items then this method moves the scroll back into middle so user can
    have chance to view the new lots. Similarly when scrolled all the way up but in this case it retrieve the
    previous items.
*/
function load_country_data(auction_id, start, limit, price)
{
    ajaxCall.open("GET", "Ajax/dynamicDataLoading.php?id=" + auction_id + "&start=" + start + "&limit=" + limit + "&pr=" + price);
    ajaxCall.send();
    ajaxCall.onreadystatechange = function() {
        if (this.status === 200 && this.readyState === 4)
        {
            let items = JSON.parse(ajaxCall.responseText);
            let lastItem = items[items.length - 1].item_id;
            if(items.length !== 0 && lastItem !== lastData || decrease === 'stop')
            {
                loadButton.innerHTML = "<button type='button' style='color: green;'> Please wait... </button>";
                data.innerHTML = '';
                items.forEach(function (item){
                    lastData = item.item_id;
                    buildItems(item);
                });
                action = 'active';
                scrollUp_Down();
            }
            else
            {
                loadButton.innerHTML = "<button type='button' style='color: red;'> No data found... </button>";
                action = 'inactive';
            }
        }
    };
}

/**
    This method is listening to user scrolling, if it reached close to the bottom or close to up to
    load more data and refresh the items accordingly.
*/
document.addEventListener('scroll', function(event) {
    let innerHighera = window.innerHeight + window.scrollY + 3;
    if(document.body.offsetHeight < innerHighera && action === 'active')
    {
        decrease = false;
        action = 'inactive'
        starter += 2;
            setTimeout(function () {load_country_data(auction_id,starter, limiter, priceer);}, 500);
        return;
    }
    if(window.scrollY < 1 && starter >= 1)
    {
        decrease = true;
        starter -= 2;
        if(starter < 1){starter = 1;decrease = 'stop';}
        setTimeout(function () {load_country_data(auction_id, starter, limiter, priceer);}, 500);
    }
});

/**
    Based on the scroll results, this method is making auto scrolling to bring the view into middle of page so
    user can be ready to make another scroll action up or down.
*/
function scrollUp_Down()
{
    if(decrease === true)
    {
        window.scrollTo(0, (window.scrollY + 60));
        return;
    }
    (starter > 1) ? window.scrollTo(0, (window.scrollY - 200)) : window.scrollTo(0, 0);
}

/**
This method is used for the preparation and creation of LOT's on the items page. This function takes
 an object of type item then extract fields data and display its properties in HTML format.
*/
function buildItems(item)
{
    data.innerHTML += '<div class="col-xl-3 col-lg-4 col-md-6 my-3">' +
        '<a class="text-decoration-none" href="single_item.php?id=' + item.item_id + '">' +
        '<div class="card anim_box3"><img class="card-img-top" src="Images/'+ item.image +'.jpg" alt="Card image cap">' +
        '<h5 class="card-title mt-2"> <small class="text-danger">' + item.item_id + '</small> ' + item.title + '</h5>' +
        '<h6 class="card-subtitle text-muted"> <span class="item-price"> Estimated  </span>' + item.Max_price + '</h6>' +
        '<p class="card-text py-2"> location: ' + item.country + '</p>' +
        '<div class="card-footer bg-transparent text-center">Ends: ' + item.end_date + ' at ' + item.end_time +
        '</div></div> </a> <br>';
}

function filterItems()
{
    let min_price = document.getElementById("min_price").checked;
    let max_price = document.getElementById("max_price").checked;
    let numberOfItems = document.querySelectorAll('input[name="lot_number"]:checked');
    numberOfItems.forEach(function (box){
        if(box.checked) limiter = box.value;
    });
    (min_price) ? priceer = "min" : priceer = "max";
    starter = 1;
    console.log("Priceer " + priceer + " items " + limiter + " strater " + starter);
    load_country_data(auction_id, starter, limiter, priceer);
}

function resetFilters()
{
    starter = 1 ;
    limiter = 8;
    priceer = ' ';
    document.getElementById("defaultLotNum").checked = true;
    load_country_data(auction_id, starter, limiter, priceer);
}
