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