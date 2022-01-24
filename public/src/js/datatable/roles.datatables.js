$(document).ready(function () {

    var roles_table = $('#roles-table').DataTable({
        // Server-side parameters
        "processing": true,
        "serverSide": true,
        orderCellsTop: true,
        fixedHeader: true,
        // Ajax call
        ajax: {
            url: App.getPageConfig('index_roles'),
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
            {"data": "roleName", "targets": 2},
            {"data": "permissions", "targets": 3},
            {"data": "actions", "targets": 4, searchable: false, orderable: false}
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
                            dt.column( -2 ).visible( ! dt.column( -2 ).visible() );
                            var lastDraw = roles_table.ajax.params();
                            var allVisiblecolumns_excel = get_visible_columns(roles_table);
                            exportList(App.getPageConfig('index_roles'),'xls',  allVisiblecolumns_excel, lastDraw);
                        }
                    },
                    {
                        text: 'Csv',
                        className: 'btn default',
                        action: function ( e, dt, node, config ) {

                            var lastDraw = roles_table.ajax.params();
                            var allVisiblecolumns = get_visible_columns(roles_table);
                            exportList(App.getPageConfig('index_roles'),'csv',  allVisiblecolumns, lastDraw);


                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                className: 'btn default',
                columns: ':eq(2),:eq(3)',
                postfixButtons: ['colvisRestore'],
            }
        ],
    });
    preload('#roles-table', roles_table);

});