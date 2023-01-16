function remove_bid_from_basket(bid_id, btn_clicked_id)
{
    let basket = new XMLHttpRequest();
    basket.open("GET", "Ajax/bidding.php?rm_bid=" + bid_id, true);
    basket.onreadystatechange = function() {
        if (this.status === 200 && this.readyState === 4)
        {
            let i = btn_clicked_id.parentNode.parentNode.rowIndex;
            document.getElementById("bid_table").deleteRow(i);
        }
    }
    basket.send();
}
