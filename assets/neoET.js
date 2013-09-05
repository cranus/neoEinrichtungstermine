function showdetails(id,level) {
    $.ajax({
        type: "POST",
        url:level+"/ajax/details", //ToDo: Geht nicht wenn man eine unterseite offen hat
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
