$(document).ready(function () {

    var permissions_table = $('#permissions-table').DataTable({
        // Server-side parameters
        "processing": true,
        "serverSide": true,
        orderCellsTop: true,
        fixedHeader: true,
        // Ajax call
        ajax: {
            url: App.getPageConfig('index_permissions'),
            type: 'POST'
        },
        "language": {
            "url":"//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
        },
        "sAjaxDataProp": "data",
        "deferRender": true,
        "paging" : true,
        "pageLength": 10,
        "columnDefs":[
            {"data": "select_item", "targets": 0, searchable: false, orderable: false},
            {"data": "id", "visible": false, "targets": 1},
            {"data": "name", "targets": 2},
            {"data": "actions", "targets": 3, searchable: false, orderable: false}
        ],
        "order": [[1, 'desc']],
        dom: 'Bfrtip',
        "dom": 'Blfrtip',
        "buttons": [
            {
                extend: 'collection',
                text: 'Télécharger les éléments visibles',
                className: 'btn default',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn default',
                        exportOptions: {
                            columns: ':visible',
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        className: 'btn default',
                        charset: 'UTF-8',
                        fieldSeparator: ';',
                        bom: true,
                        exportOptions: {
                            columns: ':visible',
                        }
                    },
                ]
            },
            {
                extend: 'collection',
                text: 'Exporter tout',
                className: 'btn default',
                buttons: [
                    {
                        text: 'Excel',
                        className: 'btn default',
                        action: function ( e, dt, node, config ) {
                            var lastDraw = permissions_table.ajax.params();
                            var allVisiblecolumns_excel = get_visible_columns(permissions_table);
                            exportList(App.getPageConfig('index_permissions'),'xls',  allVisiblecolumns_excel, lastDraw);
                        }
                    },
                    {
                        text: 'Csv',
                        className: 'btn default',
                        action: function ( e, dt, node, config ) {

                            var lastDraw = permissions_table.ajax.params();
                            var allVisiblecolumns = get_visible_columns(permissions_table);
                            exportList(App.getPageConfig('index_permissions'),'csv',  allVisiblecolumns, lastDraw);


                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                className: 'btn default',
                columns: ':eq(2)',
                postfixButtons: ['colvisRestore'],
            }
        ],
    });
    preload('#permissions-table', permissions_table);

});