$(document).ready(function () {
    $('#tbl-tipoEmbarque').DataTable({
        ajax: 'tipoEmbarque/all',
        columns: [
            {data: 'nombre', name: 'nombre'},
            {data: 'descripcion', name: 'descripcion'},
            {
                sortable: false,
                "render": function (data, type, full, meta) {
                    var params = [
                        full.id, 
                        "'" + full.descripcion + "'", 
                        "'" + full.nombre + "'"
                    ];
                    var btn_edit =  "<a onclick=\"edit(" + params + ")\" class='btn btn-outline btn-success btn-xs' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fa fa-edit'></i></a> ";
                    var btn_delete = " <a onclick=\"eliminar(" + full.id + ","+true+")\" class='btn btn-outline btn-danger btn-xs' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fa fa-trash'></i></a> ";
                    return btn_edit + btn_delete;
                }
            }
        ]
    });
});

function edit(id,descripcion,nombre){
    var data ={
        id:id,
        descripcion: descripcion,
        nombre: nombre,
    };
    objVue.edit(data);
}

var objVue = new Vue({
    el: '#tipoEmbarque',
    mounted: function(){
        //
    },
    data:{
        descripcion: '',
        nombre: '',
        editar: 0,
        formErrors: {},
        listErrors: {},
    },
    methods:{
        resetForm: function(){
            this.id = '';
            this.descripcion = '';
            this.nombre = '';
            this.editar = 0;
            this.formErrors = {};
            this.listErrors = {};
        },
        /* metodo para eliminar el error de los campos del formulario cuando dan clic sobre el */
        deleteError: function(element){
            let me = this;
            $.each(me.listErrors, function (key, value) {
                if(key !== element){
                   me.listErrors[key] = value; 
               }else{
                me.listErrors[key] = false; 
               }
            });
        },
        rollBackDelete: function(data){
            var urlRestaurar = 'tipoEmbarque/restaurar/' + data.id;
            axios.get(urlRestaurar).then(response => {
                toastr.success('Registro restaurado.');
                this.updateTable();
            });
        },
        updateTable: function(){
            refreshTable('tbl-tipoEmbarque');
        },
        delete: function(data){
            this.formErrors = {};
            this.listErrors = {};
            if(data.logical === true){
                axios.get('tipoEmbarque/delete/' + data.id + '/' + data.logical).then(response => {
                    this.updateTable();
                    toastr.success("<div><p>Registro eliminado exitosamente.</p><button type='button' onclick='deshacerEliminar(" + data.id + ")' id='okBtn' class='btn btn-xs btn-danger pull-right'><i class='fa fa-reply'></i> Restaurar</button></div>");
                    toastr.options.closeButton = true;
                });
            }else{
                axios.delete('tipoEmbarque/' + data.id).then(response => {
                    this.updateTable();
                    toastr.success('Registro eliminado correctamente.');
                    toastr.options.closeButton = true;
                });
            }
        },
        create: function(){
            let me = this;
            axios.post('tipoEmbarque',{
                'created_at' : new Date(),
                'descripcion' : this.descripcion,
                'nombre' : this.nombre,
            }).then(function(response){
                if(response.data['code'] == 200){
                    toastr.success('Registro creado correctamente.');
                    toastr.options.closeButton = true;
                }else{
                    toastr.warning(response.data['error']);
                    toastr.options.closeButton = true;
                }
                me.resetForm();
                me.updateTable();
            }).catch(function(error){
                console.log(error);
                if (error.response.status === 422) {
                    me.formErrors = error.response.data;//guardo los errores
                    me.listErrors = me.formErrors.errors;//genero lista de errores
                }

                $.each(me.formErrors.errors, function (key, value) {
                    $('.result-' + key).html(value);
                });
                toastr.error("Porfavor completa los campos obligatorios.", {timeOut: 50000});
            });
        },
        update: function(){
            var me = this;
            axios.put('tipoEmbarque/' + this.id,{
                'descripcion' : this.descripcion,
                'nombre' : this.nombre,
            }).then(function (response) {
                if (response.data['code'] == 200) {
                    toastr.success('Registro Actualizado correctamente');
                    toastr.options.closeButton = true;
                    me.editar = 0;
                } else {
                    toastr.warning(response.data['error']);
                    toastr.options.closeButton = true;
                    console.log(response.data);
                }
                me.resetForm();
                me.updateTable();
            }).catch(function (error) {
                if (error.response.status === 422) {
                    me.formErrors = error.response.data;
                    me.listErrors = me.formErrors.errors;//genero lista de errores
                }
                $.each(me.formErrors.errors, function (key, value) {
                    $('.result-' + key).html(value);
                });
                toastr.error("Porfavor completa los campos obligatorios.", {timeOut: 50000});
            });
        },
        edit: function(data){
            this.id = data['id'];
            this.descripcion = data['descripcion'];
            this.nombre = data['nombre'];
            this.editar = 1;
            this.formErrors = {};
            this.listErrors = {};
        },
        cancel: function(){
            var me = this;
            me.resetForm();
        },
    },
});