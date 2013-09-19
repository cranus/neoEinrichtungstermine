function showdetails(id) {
    url = window.location.pathname;
    if(url.contains("/index") || url.contains("/dayview"))
    { ajaxurl = "../ajax/details"; }
    else ajaxurl = "./ajax/details";
    $.ajax({
        type: "POST",
        url: ajaxurl,
        data: { cmd: "renderDetails", id: id }
    }).done(function(data) {
            $('#neoet_details').html(data);
            $('#neoet_details').dialog({
                show: "slide",
                hide: "slide",
                modal: true,
                minWidth: 800,
                buttons: {
                    'Ok': function() {
                        $(this).dialog("close");
                    }
                }
            });
        });



}

$(document).ready(function() {
    $('#neoeinrichtungstermine_date').datepicker({dateFormat: 'dd.mm.yy' });
    $('.neobutton').button();
});
