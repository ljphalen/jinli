var PortletDraggable = function () {
    return {
        //main function to initiate the module
        init: function () {

            if (!jQuery().sortable) {
                return;
            }

            $("#sortable_portlets").sortable({
                connectWith: ".widget",
                items: ".widget",
                opacity: 0.8,
                coneHelperSize: true,
                placeholder: 'sortable-box-placeholder round-all',
                forcePlaceholderSize: true,
                tolerance: "pointer"
            });


            $(".column").disableSelection();
            $('#sortable_portlets').bind('sortstop', function(event, ui) {
                console.log(ui.item.index());
            console.log($("#sortable_portlets").sortable('toArray'));
                
            });

        }

    };

}();