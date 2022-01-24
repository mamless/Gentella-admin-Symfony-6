require("/src/js/datatable/users.datatables.js");
require("/src/js/datatable/roles.datatables.js");
require("/src/js/datatable/permissions.datatables.js");
function require(script) {
    $.ajax({
        url: script,
        dataType: "script",
        async: false,           // <-- This is the key
        success: function () {
            // all good...
        },
        error: function () {
            throw new Error("Could not load script " + script);
        }
    });
}
$(document).on('change', '#select_all', function(e){
    e.preventDefault();
    $( ".checkboxes" ).trigger( "click" );
});
function preload(identifier, table){
    $(identifier+' thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();

        if(!$(this).hasClass('no-search') && !$(this).hasClass('checkAll')){
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        }
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
        $( 'select', this ).on( 'change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    });
    $("#grpaction").submit(function (e) {
        e.preventDefault();
        var ids = [];
        var action = $("#action_select").val();

        $( ".dataminetables .checkboxes" ).each(function() {
            if($(this).is(':checked')){
                ids.push($(this).val());
            }

        });
        if (ids.length === 0){
            alert(App.getPageConfig("no_items_selected"));
        }else if (action === ""){
            alert(App.getPageConfig("choice_action"));
        } else {
            if (confirm(App.getPageConfig("confirmation_request"))){
                var link = $(this).attr("action");

                $.ajax({
                    url: link,
                    method: 'POST',
                    data : {
                        action:action,
                        ids:ids
                    },
                    success: function(data){
                        if(data.message==="success"){
                            notif("success","Reussi",data.nb +" element(s) " +action);
                            setTimeout(table.ajax.reload(), 2000);
                        }else {
                            $(".main_container").prepend(data);
                            notif("error",App.getPageConfig("global_error"), App.getPageConfig("an_unexpected_error"));
                        }
                    },
                    error: function(xhr){
                        notif("error",App.getPageConfig("global_error"), App.getPageConfig("an_unexpected_error"));
                    }
                });
            }
        }
    });
}

