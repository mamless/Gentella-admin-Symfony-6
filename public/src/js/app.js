/**
 Core script to handle the entire theme and core functions
 **/
var App = function () {
    var configData =  {};
    var currentPage =  '';
    return {
        /**
         * Humanize a string
         * etc product_show =>  productShow
         * @param str
         * @returns {string}
         */
        humanize: function (str) {
            var frags = str.split('_');
            for (i = 0; i < frags.length; i++) {
                frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
            }
            return frags.join('');
        },
        /**
         * Setting current page config
         * @param pageKey
         */
        setCurrentPage: function(pageKey) {
            currentPage =  this.humanize(pageKey);
            configData[currentPage] = {};
        },
        /**
         * Get current page config
         * @returns {string}
         */
        getCurrentPage: function () {
            return currentPage;
        },
        /**
         * Get current page config
         * @param configName
         * @returns {*}
         */
        getPageConfig: function (configName = null) {
            if (null === configName) {
                return configData[this.getCurrentPage()]
            }
            return configData[this.getCurrentPage()][configName];
        },
        /**
         * Set current page config
         * @param configName
         * @param configValue
         */
        setPageConfig:  function (configName, configValue) {
            configData[this.getCurrentPage()][configName] = configValue;
        },
        // showSuccess: function (message, type = 'success', position = 'center', width = 600, timeout = 2000) {
        //     return Swal.fire({
        //         position: 'center',
        //         showCloseButton: true,
        //         type: type,
        //         width: width,
        //         title: message
        //     }, timeout);
        // }

    };

}();
function get_visible_columns(table) {

    var all_columns = table.settings().init().columnDefs;
    var allVisible = table.columns().visible();
    var visible_columns = [];
    for (var i in all_columns) {
        for (var j in allVisible) {
            if ((i==j) && (allVisible[j] === true)) {
                visible_columns.push(all_columns[i].data);
            }
        }
    }

    return visible_columns;

}
function exportList(url, format , allVisiblecolumns, lastDraw) {
    $.fileDownload(url, {
        httpMethod: "POST",
        data: {
            format: format,
            allVisiblecolumns: allVisiblecolumns,
            columns: lastDraw.columns,
            order: lastDraw.order
        }, successCallback: function (tes) {
            <!-- alert('File download a success!'); -->
        }
    });
}

$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});
$(document).on("click",".activate-link",function (e) {
    /*$(".activate-link").click(function(e) {*/
    e.preventDefault();
    var btn = $(this);
    var link = $(this).attr("href");
    $.ajax({
        url: link,
        method: 'POST',
        success: function(data){
            if(data.message=="success"){
                if(data.value===true){
                    btn.removeClass("btn-success");
                    btn.removeClass("btn-warning");
                    btn.addClass("btn-success");
                    btn.html("<i class=\"fa fa-check\"></i>");
                    notif("success","Reussi","Activée");
                }else if(data.value===false) {
                    btn.removeClass("btn-success");
                    btn.removeClass("btn-warning");
                    btn.addClass("btn-warning");
                    btn.html("<i class=\"fa fa-times\"></i>");
                    notif("warning","Reussi","Desactivée");
                }


            }else {
                notif("error","Erreur","Une erreur innatendue est survenue.");
            }
        },
        error: function(xhr){
            notif("error","Erreur","Une erreur innatendue est survenue");
        }
    });
});

$(document).on("click",".del-link",function (e) {
    /*$(".activate-link").click(function(e) {*/
    e.preventDefault();
    if(confirm("Voulez vous vraiment supprimer ?")){
        var btn = $(this);
        var link = $(this).attr("href");
        $.ajax({
            url: link,
            method: 'POST',
            success: function(data){
                if(data.message=="success"){
                    if(data.value===true){
                        notif("success","Reussi","Suprimmé");
                        // Get the position of the current data from the node
                        var aPos = dataminetables.fnGetPosition( btn.closest('tr').get(0) );
                        // Delete the row
                        dataminetables.fnDeleteRow(aPos);
                    }else if(data.value===false) {
                        notif("warning",App.getPageConfig("global_error"), App.getPageConfig("an_unexpected_error"));
                    }


                }else {
                    notif("error",App.getPageConfig("global_error"), App.getPageConfig("an_unexpected_error"));
                }
            },
            error: function(xhr){
                notif("error",App.getPageConfig("global_error"), App.getPageConfig("an_unexpected_error"));
            }
        });
    }
});


function notif(type,titre,text) {
    new PNotify({
        title: titre,
        text: text,
        type: type,
        styling: 'bootstrap3'
    });
}
// transform all select
$('select').selectpicker();

$('#select_all').click(function() {
    if ($(this).is(':checked')) {
        $('.chkgrp').prop('checked', true);
    } else {
        $('.chkgrp').prop('checked', false);
    }
});


$("body").on('DOMSubtreeModified', ".dataTables_info", function () {

    if ($("#select_all").is(':checked')) {
        $('#select_all').prop('checked', false);
    }
});
