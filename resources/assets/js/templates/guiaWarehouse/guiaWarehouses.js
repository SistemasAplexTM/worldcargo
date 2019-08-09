$(document).ready(function () {
    llenarSelectPersonalizado('guia', 'localizacion', 'localizacion_id', 2);// module, tableName, id_campo
    llenarSelectPersonalizado('guia', 'localizacion', 'localizacion_id_c', 2);// module, tableName, id_campo
    llenarSelectPersonalizado('guia', 'agencia', 'agencia_id', 0);// module, tableName, id_campo
    // llenarSelect2('guia', 'servicios', 'servicios_id');// module, tableName, id_campo
    llenarSelect2('guia', 'tipo_empaque', 'tipo_empaque_id');// module, tableName, id_campo

    $('#saveForm').on('click', function(){
        sendForm();
    });
    $('#btn_add').click(function () {
      validationFields();
    });
});

function sendForm(){
  if ($('#whrTable >tbody >tr').length === 0) {
      $('#noEnviar').css('background', 'rgba(248,80,50,0.20)');
      $('#noEnviar').css('display', 'block');
      return false;
  } else {
    if(validationFields(true)){
      $('#formGuia').submit();
    }
  }
}

function llenarSelect2(module, tableName, idSelect){
    var url = '../selectInput/' + tableName ;
    $.ajax({
        url: url,
        dataType: 'json',
        type: 'GET',
        success: function (data) {
            if (data.code === 200) {
                /* llenar select */
                $(data.items).each(function (index, value) {
                    $("#" + idSelect).append('<option value="' + value.id +'">' + value.text + '</option>');
                });
            } else {
                alert('error');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            data = {
                error: jqXHR + ' - ' + textStatus + ' - ' + errorThrown
            }
            $('#modal' + 1).modal('toggle');
            $('body').append('<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel">ERROR EN TRANSACCIÓN</h4></div><div class="modal-body">' + data.error + '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button></div></div></div></div>');
            $('#modalError').modal({show: true});
        }
    });
}

/*-- Función para llenar select PERSONALIZADO --*/
function llenarSelectPersonalizado(module, tableName, idSelect, length){
    var url = '../selectInput/' + tableName ;

    $('#' + idSelect).select2({
      placeholder: "Seleccionar",
      tokenSeparators: [','],
      ajax: {
          url: url,
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  term: params.term, // search term
                  page: params.page,
                  idSelect: idSelect,
              };
          },
          processResults: function (data, params) {
              params.page = params.page || 1;
              return {
                  results: data.items,
                  pagination: {
                      more: (params.page * 30) < data.total_count
                  }
              };
          },
          cache: true
      },
      escapeMarkup: function (markup) {
          return markup;
      }, // let our custom formatter work
      minimumInputLength: length,
      templateResult: formatRepo,
      templateSelection: formatRepoSelection,
    }).on("change", function (e) {
      $('.select2-selection').css('border-color', '');
      $('#'+idSelect).siblings('small').css('display', 'none');
    });
}

function formatRepo(repo) {
    if (repo.loading) {
        return repo.text;
    }

    if(repo.deptos && repo.pais){
        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'><strong><i class='fa fa-map-marker'></i> " + repo.text + " / " + repo.deptos + " / " + repo.pais + "</strong></div>";
    }else{
        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'><strong><i class='fa fa-map-marker'></i> " + repo.text + "</strong></div>";
    }

    return markup;
}

function formatRepoSelection(repo) {
    if(repo.deptos && repo.pais){
        $('#prefijoD').val(repo.prefijoD);
        $('#prefijoR').val(repo.prefijoR);
        return  repo.text + " / " + repo.deptos + " / " + repo.pais  || repo.text;
    }else{
        return  repo.text  || repo.text;
    }
}

function addRow(){
    var cont = $('#whrTable tbody tr').length + 1;
    /*tomo los valores de cada campo*/
    var peso = $('#peso').val();
    var largo = $('#largo').val();
    var alto = $('#alto').val();
    var ancho = $('#ancho').val();
    var contiene = $('#contiene').val();
    var tracking = $('#tracking').val();
    var tipoEmpaqueText = $('#tipo_empaque_id option:selected').text();
    var tipoEmpaque = $('#tipo_empaque_id').val();

    /*funcion para tomar la fecha del pc y no del servidor*/
    Number.prototype.padLeft = function (base, chr) {
        var len = (String(base || 10).length - String(this).length) + 1;
        return len > 0 ? new Array(len).join(chr || '0') + this : this;
    }
    var d = new Date,
            dformat = [d.getFullYear(),
                (d.getMonth() + 1).padLeft(),
                d.getDate().padLeft()].join('-') + 'T' +
            [d.getHours().padLeft(),
                d.getMinutes().padLeft(),
                d.getSeconds().padLeft()].join(':');

    /*calcula el volumen total .toFixed(5)*/
    var volumen = (parseInt(largo) * parseInt(alto) * parseInt(ancho) / 166);
    var vol = parseFloat(volumen.toFixed(2)) + parseFloat($('#volumen').val());
    $('#volumen').val(vol.toFixed(2));

    /*creo la fila de la tabla con los campos asignados y colocando la variable cont en cada id*/
    var fila = '<tr id="fila' + cont + '" class="rowDetail">';
    var td1 = '<td><input type="hidden" id="volumen' + cont + '" name="volumenD[]" class="form-control" value="' + volumen.toFixed(2) + '">'+
    '<input type="hidden" id="pesoUnit' + cont + '" name="pesoUnit[]" class="form-control" value="' + peso + '">'+
    '<input type="text" id="dimensiones' + cont + '" name="dimensiones[]" class="form-control" value="' + peso + ' Vol= ' + largo + 'x' + ancho + 'x' + alto + '" readonly></td>';
    var td2 = '<td><input type="text" id="contiene' + cont + '" name="contiene[]" class="form-control" value="' + contiene + '" readonly></td>';
    var td3 = '<td><input type="hidden" id="tempaque' + cont + '" name="tempaque[]" class="form-control" value="' + tipoEmpaque + '" readonly>'+
    '<input type="text" readonly id="tempaqueD' + cont + '" name="tempaqueD[]" class="form-control" value="' + tipoEmpaqueText + '"></td>';
    var td4 = '<td><input type="text" id="tracking' + cont + '" name="tracking[]" class="form-control" value="' + tracking + '" readonly></td>';
    var td5 = '<td><input type="datetime-local" id="fecha' + cont + '" name="fechaDetalle[]" class="form-control" value="' + dformat + '" readonly></td>';
    var td6 = '<td><button class="btn btn-danger btn-xs btn_remove" id="btn_remove" type="button" onclick="removeRowDetail(' + cont + ')"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="Eliminar"></i></button></td>';
    fila += td1 + td2 + td3 + td4 + td5 + td6 + '</tr>';
    $('#noEnviar').css('display', 'none');
    //coloca una nueva fila en la tabla
    $('#whrTable').append(fila);
    var cant = $('#whrTable >tbody >tr').length;
    //asigna la cantidad que va tomanto de cada campo de  la tabla
    $('#piezas').val(cant);
    //suma los pesos a medida que se agregan
    $('#pesoDim').val(parseInt($('#pesoDim').val()) + parseInt(peso));

    $('#peso').val('');
    $('#contiene').val('');
    $('#tracking').val('');
    $('#largo').val(0);
    $('#ancho').val(0);
    $('#alto').val(0);
}

function validationFields(op) {
  if(op){
    var validar = true;
    var ciudad = $('#nomCiudad').val();
    if (ciudad === '') {
        validar = true;//false
    }

    if (validar === false) {
        $('#nomCiudad').focus();
        swal("Atencion!", "Porfavor de click en el boton 'Buscar' del campo de ciudad del Consignee y seleccione una ciudad de la lista.", "warning");
    } else {
      var msError = false;
      x = $('#formGuia').serializeArray();
      var campo = '';

      $.each(x, function (i, field) {
          campo = field.name;
          if (campo != 'id' && campo != 'prefijoR' && campo != 'prefijoD' &&campo != 'consignee_id' && campo != 'shipper_id' && campo != 'peso' && campo != 'contiene' && campo != 'tipo_empaque_id' && campo != 'tracking'
                  && campo != 'volumenD[]' && campo != 'pesoUnit[]' && campo != 'dimensiones[]' && campo != 'contiene[]' && campo != 'tempaque[]'
                  && campo != 'tempaqueD[]' && campo != 'tracking[]' && campo != 'fechaDetalle[]' && campo != 'guia_hija_id' && campo != 'guia_hija_observaciones' && campo != 'trackingCas'
                  && campo != 'poBoxD' && campo != 'emailR' && campo != 'telR' && campo != 'emailD' && campo != 'telD' && campo != 'observaciones' && campo != 'c_wrh' && campo != '_token') {
            
              if ($('#' + campo).val() == '') {
                  $('#' + campo).parent().addClass('has-error');
                  console.log(campo);
                  if ($('#' + campo).parent().hasClass('input-group')) {
                      $('#' + campo).parent().siblings('small').css('display', 'block');
                      $('#' + campo).parent().siblings('small').css('color', '#a94442');
                  } else {
                      $('#' + campo).siblings('small').css('display', 'block');
                  }
                  msError = true;
              }
          }
      });

      if (msError === true) {
          return false;
      } else {
          return true;
      }
    }
  }else{
    if ($('#peso').val() === '') {
        $('#Valpeso').addClass('has-error');
        $('#Hpeso').css('display', 'block');

    } else {
      if ($('#largo').val() === '' || $('#ancho').val() === '' || $('#alto').val() === '') {
          $('#Valdim').addClass('has-error');
          $('#Hdim').css('display', 'block');
      } else {
        if ($('#contiene').val() === '') {
            $('#Valconti').addClass('has-error');
            $('#Hcontiene').css('display', 'block');
        } else {
          if ($('#tipo_empaque_id').val() === '') {
              $('#Valtipoem').addClass('has-error');
              $('#HtipoE').css('display', 'block');
          } else {
            if ($('#contiene').val().length > 215) {
                $('#Valconti').addClass('has-error');
                $('#Hcontiene').css('display', 'block');
                $('#Hcontiene').text('(' + $('#contiene').val().length + ') caracteres, No puede tener mas de 215 caracteres');
            } else {
                addRow();
            }
          }
        }
      }
    }
  }
}

function removeRowDetail(id_row){
  if ($('#whrTable >tbody >tr').length === 1) {
      $('#noEnviar').css('background', 'rgba(248,80,50,0.20)');
      $('#noEnviar').css('display', 'block');
  } else {
      var re = parseFloat($('#volumen').val()) - parseFloat($('#volumen' + id_row).val());
      $('#volumen').val(re.toFixed(2));
      $('#piezas').val($('#piezas').val() - 1);
      var string = $('#dimensiones' + id_row).val();

      var valor = parseFloat(string.substring(0, 4));
      $('#pesoDim').val($('#pesoDim').val() - valor);
      $('#fila' + id_row).remove();
  }
}