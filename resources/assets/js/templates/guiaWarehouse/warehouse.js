$(document).ready(function () {
    $('#tbl-warehouses').DataTable({
        ajax: 'guia/all/guia_hija_detalle',
        columns: [
            {data: 'num_warehouse', name: 'num_warehouse'},
            {data: 'cons_nomfull', name: 'cons_nomfull'},
            {data: 'peso', name: 'peso'},
            {data: 'nom_agencia', name: 'nom_agencia'},
            {
                sortable: false,
                "render": function (data, type, full, meta) {
                    var params = [
                        /*full.id, 
                        "'" + full.descripcion + "'", 
                        "'" + full.ciudad_id + "'",
                        "'" + full.ciudad + "'",
                        "'" + full.estado_id + "'",
                        "'" + full.estado + "'",
                        "'" + full.pais_id + "'",
                        "'" + full.pais + "'",*/
                    ];
                    var btn_edit =  '<li><a href="guia/'+ full.id +'/edit"><spam class="fa fa-edit"></spam> Editar</a></li>';
                    var btn_delete = "<li><a onclick=\"modalEliminar()\"><spam class='fa fa-times'></spam> Eliminar</a></li>";
                    var btns = "<div class='btn-group'>"+
                "<button type='button' class='btn btn-info dropdown-toggle btn-xs' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+
"Actiones <span class='caret'></span>"+
"</button>"+
"<ul class='dropdown-menu'>"+ btn_edit +
"<li><a href='' target='_blank'> <spam class='fa fa-print'></spam> Imprimir Warehouse</a></li>"+
"<li><a href='' target='_blank'> <spam class='fa fa-print'></spam> Imprimir Labels</a></li>"+
"<li><a href=''> <spam class='fa fa-eye'></spam> Visualizar</a></li>" +
"<li><a href='#' onclick=\"enviarMail()\"> <spam class='fa fa-envelope'></spam> Enviar Mail</a></li> "+ btn_delete +
"</ul>"+
"</div> <a class='btn btn-xs btn-success' href='' data-toggle='tooltip' data-placement='top' title='Liquidar'> <spam class='fa fa-money'></spam></a>";
                    return btns;
                }
            }
        ]
    });
});