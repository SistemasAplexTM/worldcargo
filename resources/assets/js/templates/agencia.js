$(document).ready(function() {
    $('#tbl-agencia').DataTable({
        ajax: 'agencia/all',
        columns: [{
            data: 'descripcion',
            name: 'descripcion'
        }, {
            data: 'responsable',
            name: 'responsable'
        }, {
            data: 'direccion',
            name: 'direccion'
        }, {
            data: 'ciudad',
            name: 'localizacion.nombre'
        }, {
            data: 'estado',
            name: 'deptos.descripcion'
        }, {
            data: 'pais',
            name: 'pais.descripcion'
        }, {
            data: 'telefono',
            name: 'telefono'
        }, {
            data: 'logo',
            name: 'logo'
        }, {
            sortable: false,
            "render": function(data, type, full, meta) {
                var btn_edit = '';
                var btn_delete = '';
                if (permission_edit) {
                    var params = [
                        full.id, "'" + full.descripcion + "'", "'" + full.ciudad_id + "'", "'" + full.ciudad + "'", "'" + full.estado_id + "'", "'" + full.estado + "'", "'" + full.pais_id + "'", "'" + full.pais + "'",
                    ];
                    var btn_edit = "<a onclick=\"edit(" + params + ")\" class='btn btn-outline btn-success btn-xs' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fa fa-edit'></i></a> ";
                }
                if (permission_delete) {
                    if(full.tipo_agencia != 1){
                        var btn_delete = " <a onclick=\"eliminar(" + full.id + "," + true + ")\" class='btn btn-outline btn-danger btn-xs' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fa fa-trash'></i></a> ";
                    }
                }
                return btn_edit + btn_delete;
            }
        }]
    });
});

function edit(id) {
    var data = {
        id: id
    };
    objVue.edit(data);
}
/* objetos VUE index */
var objVue = new Vue({
    el: '#agencia',
    data: {
        //
    },
    methods: {
        rollBackDelete: function(data) {
            var urlRestaurar = 'agencia/restaurar/' + data.id;
            axios.get(urlRestaurar).then(response => {
                toastr.success('Registro restaurado.');
                this.updateTable();
            });
        },
        updateTable: function() {
            refreshTable('tbl-agencia');
        },
        delete: function(data) {
            this.formErrors = {};
            this.listErrors = {};
            if (data.logical === true) {
                axios.get('agencia/delete/' + data.id + '/' + data.logical).then(response => {
                    this.updateTable();
                    toastr.success("<div><p>Registro eliminado exitosamente.</p><button type='button' onclick='deshacerEliminar(" + data.id + ")' id='okBtn' class='btn btn-xs btn-danger pull-right'><i class='fa fa-reply'></i> Restaurar</button></div>");
                    toastr.options.closeButton = true;
                });
            } else {
                axios.delete('agencia/' + data.id).then(response => {
                    this.updateTable();
                    toastr.success('Registro eliminado correctamente.');
                    toastr.options.closeButton = true;
                });
            }
        },
        edit: function(data) {
            $(location).attr('href', 'agencia/' + data.id + '/edit');
        }
    },
});