function showdetails(id) {
    $.ajax({
        type: "POST",
        url:"/neo/public/plugins.php/neoeinrichtungstermine/ajax/details", //ToDo auf Livesystem anpassen
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
