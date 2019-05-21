<?php
use Modules\Attributo\Entities\Attributo;
use Modules\Oratorio\Entities\Type;
use Modules\Event\Entities\EventSpec;
use Modules\Event\Entities\Event;
use Modules\Oratorio\Entities\Oratorio;
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" >

  <!-- Scripts -->
  <!-- jQuery -->
  <script src="{{ asset('js/app.js') }}"></script>
  <!-- <script src="//code.jquery.com/jquery-3.3.1.min.js"></script> -->
  <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/bootstrap.datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css"/>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- Styles -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.5/css/rowReorder.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">

  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.4/css/select.bootstrap.min.css">
  <link rel="stylesheet" href="{{asset('plugins/editor/css/editor.dataTables.css') }}">
  <link rel="stylesheet" href="{{asset('plugins/editor/css/editor.bootstrap.css') }}">



  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/rowreorder/1.2.5/js/dataTables.rowReorder.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

  <script src="{{asset('plugins/editor/js/dataTables.editor.js')}}"></script>
  <script src="{{asset('plugins/editor/js/editor.title.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper-utils.min.js"></script>
  <script src="{{asset('/js/bootstrap-confirmation.js')}}"></script>
  <script src="{{asset('/js/jquery.redirect.js')}}"></script>


  <!--  -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
  <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
  <link href="{{ asset('css/scheduler.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/font-awesome-animation.css') }}" rel="stylesheet">
  <link href="{{ asset('plugins/editor/css/editor.title.css') }}" rel="stylesheet">
  <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet">

  <!-- FullCalendar -->

  <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
  <script src="{{ asset('/js/fullcalendar/scheduler.min.js') }}"></script>
  <script src="{{ asset('/js/fullcalendar/locale/it.js') }}"></script>


</head>

<body class="d-flex flex-column h-100">

  @include('header')
  @include('header_event')

  <main class="py-4" role="main">
    @yield('content')
  </main>

  <footer>
    @include('footer')
  </footer>

  <!-- Scripts -->
  <script>
  (function( factory ){
    if ( typeof define === 'function' && define.amd ) {
      // AMD
      define( ['jquery', 'datatables', 'datatables-editor'], factory );
    }
    else if ( typeof exports === 'object' ) {
      // Node / CommonJS
      module.exports = function ($, dt) {
        if ( ! $ ) { $ = require('jquery'); }
        factory( $, dt || $.fn.dataTable || require('datatables') );
      };
    }
    else if ( jQuery ) {
      // Browser standard
      factory( jQuery, jQuery.fn.dataTable );
    }
  }(function( $, DataTable ) {
    'use strict';


    if ( ! DataTable.ext.editorFields ) {
      DataTable.ext.editorFields = {};
    }

    var _fieldTypes = DataTable.Editor ?
    DataTable.Editor.fieldTypes :
    DataTable.ext.editorFields;


    _fieldTypes.tinymce = {
      create: function ( conf ) {
        var that = this;
        conf._safeId = DataTable.Editor.safeId( conf.id );

        conf._input = $('<div><textarea id="'+conf._safeId+'"></textarea></div>');

        // Because tinyMCE uses an editable iframe, we need to destroy and
        // recreate it on every display of the input
        this
        .on( 'open.tinymceInit-'+conf._safeId, function () {
          tinymce.init( $.extend( true, {
            selector: '#'+conf._safeId
          }, conf.opts, {
            init_instance_callback: function ( editor ) {
              if ( conf._initSetVal ) {
                editor.setContent( conf._initSetVal );
                conf._initSetVal = null;
              }
            }
          } ) );

          var editor = tinymce.get( conf._safeId );

          if ( editor && conf._initSetVal ) {
            editor.setContent( conf._initSetVal );
            conf._initSetVal = null;
          }
        } )
        .on( 'close.tinymceInit-'+conf._safeId, function () {
          var editor = tinymce.get( conf._safeId );


          if ( editor ) {
            editor.destroy();
          }

          conf._initSetVal = null;
          conf._input.find('textarea').val('');
        } );

        return conf._input;
      },

      get: function ( conf ) {
        var editor = tinymce.get( conf._safeId );
        if ( ! editor ) {
          return conf._initSetVal;
        }

        return editor.getContent();
      },

      set: function ( conf, val ) {
        var editor = tinymce.get( conf._safeId );

        // If not ready, then store the value to use when the `open` event fires
        conf._initSetVal = val;
        if ( ! editor ) {
          return;
        }
        editor.setContent( val );
      },

      enable: function ( conf ) {}, // not supported in TinyMCE

      disable: function ( conf ) {}, // not supported in TinyMCE

      destroy: function (conf) {
        var id = DataTable.Editor.safeId(conf.id);

        this.off( 'open.tinymceInit-'+id );
        this.off( 'close.tinymceInit-'+id );
      },

      // Get the TinyMCE instance - note that this is only available after the
      // first onOpen event occurs
      tinymce: function ( conf ) {
        return tinymce.get( conf._safeId );
      }
    };


  }));
  </script>

  <script>


  tinymce.init({
    selector: 'textarea',
    height: 180,
    width : '100%',
    theme: 'modern',
    plugins: [
      'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern imagetools'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    toolbar2: 'print preview media | forecolor backcolor emoticons',
    image_advtab: true,
    templates: [
      { title: 'Test template 1', content: 'Test 1' },
      { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
      '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
      '//www.tinymce.com/css/codepen.min.css'
    ]
  });

  $(function() {
    $.datepicker.setDefaults(
      $.extend(
        {'dateFormat':'dd/mm/yy'},
        $.datepicker.regional['it']
      )
    );
    $("#datepicker").datepicker();
    $("#datepicker2").datepicker();
    $("#nato_il").datepicker();
    $(".data").datepicker();
  });

  function add_eventspec(id_sub, id_event, admin){
    var valid_for = $('#valid_for').val(); //è l'id_week
    var event_spec = $('#event_spec').val();
    var event_spec_text = $('#event_spec option:selected').text();
    var id_type = $('#event_spec').find(':selected').data('type');
    var price = $('#event_spec').find(':selected').data('price');

    var t = parseInt($('#contatore_e').val());
    var row = "<tr style='background-color: #dff0d8;'>";
    row += "<td>";
    row += "<input name='id_eventspecvalue["+t+"]' type='hidden' value='0'/>";
    row += "<input name='id_eventspec["+t+"]' type='hidden' value='"+event_spec+"'/>";
    row += "<input name='id_subscription["+t+"]' type='hidden' value='"+id_sub+"'/>";
    row += "<input name='id_week["+t+"]' type='hidden' value='"+valid_for+"'/>";
    row += event_spec_text+"</td>";
    row += "<td>";

    if(id_type>0){
      row += "<select id='valore"+t+"' name='valore["+t+"]' class='form-control'></select>";
      $.get("{{ url('types/options')}}",
      {id_type: id_type },
      function(data2) {
        var model = $("#valore"+t);
        model.empty();
        $.each(data2, function(index_2, element_2) {
          model.append("<option value='"+ element_2.id +"'>" + element_2.option + "</option>");
        });
      });
    }else{
      switch(id_type){
        case -1:
        row += "<input name='valore["+t+"]' type='text' value='' class='form-control'/>";
        break;
        case -2:
        row += "<input name='valore["+t+"]' type='hidden' value='0'/>";
        row += "<input name='valore["+t+"]' type='checkbox' value='1' class='form-control'/>";
        break;
        case -3:
        row += "<input name='valore["+t+"]' type='number' value='' class='form-control'/>";
        break;
        case -4:
        row += "<select id='valore"+t+"' name='valore["+t+"]' class='form-control'></select>";
        $.get("{{ url('admin/groups/dropdown')}}",
        {},
        function(data2) {
          var model = $("#valore"+t);
          model.empty();
          $.each(data2, function(index_2, element_2) {
            model.append("<option value='"+ element_2.id +"'>" + element_2.nome + "</option>");
          });
        });
        break;
      }
    }



    row += "</td>";
    if(admin){
      row += "<td><input name='costo["+t+"]' type='number' value='"+price+"' class='form-control' style='width: 70px;' step='0.1' id='costo_"+t+"' onchange='check_importo(this, "+t+")'/></td>";
      row += "<td>";
      row += "<input name='pagato["+t+"]' type='hidden' value='0'/>";
      row += "<input name='pagato["+t+"]' type='checkbox' value='1' class='form-control'  id='pagato_"+t+"' onchange='check_pagato(this, "+t+")' style='display:none'/>";
      row += "</td>";
      row += "<td><input name='acconto["+t+"]' type='number' value='"+price+"' class='form-control' style='width: 70px;' step='0.1' id='acconto_"+t+"'/></td>";
    }else{
      row += "<td><input name='costo["+t+"]' type='hidden' value='0' class='form-control' style='width: 70px;' />"+price+"€</td>";
      row += "<td>";
      row += "<input name='pagato["+t+"]' type='hidden' value='0'/>";
      row += "</td>";
    }
    row += "<td></td>"; //cestino
    row += "</tr>";



    if(valid_for==0){//inserisco una riga nella tabella delle specifiche generali
      $('#showeventspecvalue tr:last').after(row);
    }else{ //riga nelle tabelle settimanali
      $('#weektable_'+valid_for+' tr:last').after(row);
    }
    $('#contatore_e').val((t+1));




    $('#eventspecsOp').modal('hide');
  }

  

    /**
    Funzione che viene richiamata quando un select cambia valore; viene popolato lo span (#span_type)
    con un input del tipo corretto (seelct, testo, checkbox).

    sel: il select
    multiple: se viene generato un select, indica se sono possibili scelte multiple
    name: il name da dare all'input generato
    id: l'id da dare all'input generato
    **/
    function change_type(sel, multiple='', name='valore', id='valore', show_checkbox_hidden=true, span_id="span_type"){
      $.get("{{ url('types/type')}}",
      {id_eventspec: sel.value},
      function(data) {
        $.each(data, function(index, element) {
          var row = "";
          if(element.id>0){
            row = "<select id='"+id+"' name='"+name+"' "+multiple+" class='form-control'></select>";
            $.get("{{ url('types/options')}}",
            {id_type: element.id },
            function(data2) {
              var model = $("#"+id+"");
              model.empty();
              $.each(data2, function(index_2, element_2) {
                model.append("<option value='"+ element_2.id +"'>" + element_2.option + "</option>");
              });
            });
          }else{
            switch(element.id){
              case -1:
              row = "<input name='"+name+"' type='text' value='' class='form-control' style='width: 300px'/>";
              break;
              case -2:
              if(show_checkbox_hidden){
                row = "<input name='"+name+"' type='hidden' value='0'/>";
              }
              row += "<input name='"+name+"' type='checkbox' value='1' />";
              break;
              case -3:
              row = "<input name='"+name+"' type='number' value='' class='form-control' style='width: 300px'/>";
              break;
              case -4:
              row = "<select id='"+id+"' name='"+name+"'></select>";
              $.get("{{ url('admin/groups/dropdown')}}",
              {},
              function(data2) {
                var model = $("#"+id+"");
                model.empty();
                $.each(data2, function(index_2, element_2) {
                  model.append("<option value='"+ element_2.id +"'>" + element_2.nome + "</option>");
                });
              });
              break;
            }
          }
          $("#"+span_id).html(row);
        });
      });
    }

    function change_attrib(sel, t){
      $.get("{{ url('types/type_attrib')}}",
      {id_attrib: sel.value },
      function(data) {
        $.each(data, function(index, element) {
          var row = "";
          if(element.label=="text"){
            row = "<input name='valore["+t+"]' type='text' value='' class='form-control' style='width: 300px'/>";
          }else if(element.label=="checkbox"){
            row = "<input name='valore["+t+"]' type='hidden' value='0'/>";
            row += "<input name='valore["+t+"]' type='checkbox' value='1'/>";
          }else{
            row = "<select id='valore"+t+"' name='valore["+t+"]'></select>";
            $.get("{{ url('types/options')}}",
            {id_type: element.id },
            function(data2) {
              var model = $("#valore"+t);
              model.empty();
              $.each(data2, function(index_2, element_2) {
                model.append("<option value='"+ element_2.id +"'>" + element_2.option + "</option>");
              });
            });
          }
          $("#span_type"+t).html(row);
        });
      });
    }




    function typeselect_add(id_type){
      var t = parseInt($('#contatore_e').val());
      var row = "<tr>";
      var form = ('{{ Form::text("option[]", "", ["style" => "width: 100%"]) }}').replace(/"/g, '\'');
      form = form.replace("option[]", "option["+t+"]");
      row += "<input name='id_option["+t+"]' type='hidden' value='0'/>";
      row += "<input name='id_type["+t+"]' type='hidden' value='"+id_type+"'/>";
      row += "<td>"+form+"</td>";
      row += "<td><input type='number' min='0' name='ordine["+t+"]' value='0'</td>";
      row += "<td>E</td>";
      row += "</tr>";

      $('#showoptions tr:last').after(row);
      $('#contatore_e').val((t+1));

    }

    function add_cassa(){
      var t = parseInt($('#contatore_c').val());
      var row = "<tr>";
      row += "<td>#<input type='hidden' value='0' name='id["+t+"]'></td>";
      row += "<td><input type='text' name='label["+t+"]' class='form-control'/></td>";
      // row += "<td></td>";
      row += "<td></td>";
      row += "</tr>";

      $('#table_casse tr:last').after(row);
      $('#contatore_c').val((t+1));

    }

    function add_modo_pagamento(){
      var t = parseInt($('#contatore_m').val());
      var row = "<tr>";
      row += "<td>#<input type='hidden' value='0' name='id["+t+"]'></td>";
      row += "<td><input type='text' name='label["+t+"]' class='form-control'/></td>";
      // row += "<td></td>";
      row += "<td></td>";
      row += "</tr>";

      $('#table_modo tr:last').after(row);
      $('#contatore_m').val((t+1));

    }

    function add_tipo_pagamento(){
      var t = parseInt($('#contatore_t').val());
      var row = "<tr>";
      row += "<td>#<input type='hidden' value='0' name='id["+t+"]'></td>";
      row += "<td><input type='text' name='label["+t+"]' class='form-control'/></td>";
      // row += "<td></td>";
      row += "<td></td>";
      row += "</tr>";

      $('#table_tipo tr:last').after(row);
      $('#contatore_t').val((t+1));

    }


    //A seconda dell'attributo selezionato, cambio la casella dove inserire il valore (testo, checkbox, ...)
    function change_attributo_type(sel){
      if(sel.value<0) return;
      $.get("{{ url('attributos/type')}}",
      {id_attributo: sel.value },
      function(data){
        if(data.length>0){
          var row = "";
          $.each(data, function(index, element) {

            if(element.id_type>0){
              row = "<select id='valore' name='valore' class='form-control'></select>";
              $.get("{{ url('types/options')}}",
              {id_type: element.id_type },
              function(data2) {
                var model = $("#valore");
                model.empty();
                $.each(data2, function(index_2, element_2) {
                  model.append("<option value='"+ element_2.id +"'>" + element_2.option + "</option>");
                });
              });
            }else{
              switch(element.id_type){
                case -1:
                row = "<input name='valore' type='text' value='' class='form-control'/>";
                break;
                case -2:
                row = "<input name='valore' type='hidden' value='0'/>";
                row += "<input name='valore' type='checkbox' value='1' class='form-control'/>";
                break;
                case -3:
                row = "<input name='valore' type='number' value='' class='form-control' />";
                break;
                case -4:
                row = "<select id='valore' name='valore' class='form-control'></select>";
                $.get("{{ url('admin/groups/dropdown')}}",
                {},
                function(data2) {
                  var model = $("#valore");
                  model.empty();
                  $.each(data2, function(index_2, element_2) {
                    model.append("<option value='"+ element_2.id +"'>" + element_2.nome + "</option>");
                  });
                });
                break;
              }
            }

          });
          $("#attrib_value").html(row);
        }
      });


    }

    function load_attrib_registration(sel){
      var body = "";
      $.get("{{ url('attributos/dropdown')}}",
      {id_oratorio: sel.value },
      function(data) {
        if(data.length>0){
          var t = 0;
          body+= "INFORMAZIONI AGGIUNTIVE";
          $.each(data, function(index, element) {
            body += "<div class='form-group'>";
            body += "<label for='attrib_"+element.id+"' class='col-md-4 control-label'>"+element.nome+"</label>";
            body += "<div class='col-md-6'>";
            body += "<input type='hidden' name='id_attributo["+t+"]' value='"+element.id+"'>";

            var row = "";

            if(element.id_type>0){
              body += "<select class='form-control' id='valore"+t+"' name='attributo["+t+"]'>";
              $.ajax({
                async: false,
                data: {id_type: element.id_type},
                type: "GET",
                url: "{{ url('types/options')}}",
                success: function(data2) {
                  $.each(data2, function(index_2, element_2) {
                    body += "<option value='"+ element_2.id +"'>" + element_2.option + "</option>";
                  });
                  body += "</select>";
                }
              });
            }else{
              switch(element.id_type){
                case -1:
                body += "<input name='attributo["+t+"]' type='text' value='' class='form-control' required autofocus style='width: 300px'/>";
                break;
                case -2:
                body += "<input name='attributo["+t+"]' type='hidden' value='0'/>";
                body += "<input class='form-control' name='attributo["+t+"]' type='checkbox' value='1' required />";
                break;
                case -3:
                body += "<input name='attributo["+t+"]' type='number' value='' class='form-control' required style='width: 300px'/>";
                break;
                case -4:
                body += "<select class='form-control' id='valore"+t+"' name='attributo["+t+"]'>";
                $.ajax({
                  async: false,
                  type: "GET",
                  data: {id_oratorio: sel.value},
                  url: "{{ url('groups/dropdown')}}",
                  success: function(data2) {
                    $.each(data2, function(index_2, element_2) {
                      body += "<option value='"+ element_2.id +"'>" + element_2.nome + "</option>";
                    });
                    body += "</select>";
                  }
                });
                break;
              }
            }
            body += "</div>";
            body += "</div>";
            t++;
          });
        }
        $("#attributes").html(body);
      });
      //t++;



    }


      function load_spec_usersubscription(id_subscription, id_event){
        $('#spec1').load("usereventspecvalues?id_sub="+id_subscription+"&id_event="+id_event);
        $('#spec2').load("userspecsubscriptions?id_sub="+id_subscription+"&id_event="+id_event);
        $('#id_event').val(id_event);
      }

      function eventspec_destroy(id_eventspec, index){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
          type: 'POST',
          dataType: 'html',
          data: {id_spec: id_eventspec,
            _token: CSRF_TOKEN},
            url: "{{route('eventspecs.destroy')}}",
            success: function(response) {
              //alert(response);
              $('#row_'+index).remove();
            },
            error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest.responseText + "\n" + exception); },
            async: true
          });
        }

        function elencovalue_destroy(id_v){
          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          $.ajax({
            type: 'POST',
            dataType: 'html',
            data: {id_value: id_v,
              _token: CSRF_TOKEN},
              url: "{{route('elenco.destroy_value')}}",
              success: function(response) {
                //alert(response);
                $('#row_'+id_v).remove();
              },
              error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + XMLHttpRequest + "\n" + exception); },
              async: true
            });
          }

          function colonneelenco_add(){
            var t = parseInt($('#contatore').val());
            t = t+1;
            var row = "<tr>";
            row += "<td>";
            row += "<input type='text' name='colonna["+t+"]' class='form-control'/></td>";
            row += "</tr>";

            $('#colonne_elenco tr:last').after(row);
            $('#contatore').val(t);
          }

          function elencovalues_add(num_colonne, keys){
            var key = jQuery.parseJSON(keys);
            var t = parseInt($('#contatore').val());
            t = t+1;
            var row = "<tr>";
            row += "<input name='id_values["+t+"]' type='hidden' value='0'/>";
            row += "<td>#</td>";
            var select = "<select class='form-control' id='id_user["+t+"]' name='id_user["+t+"]'>";
            $.ajax({
              async: false,
              type: "GET",
              data: {},
              url: "{{ url('user/dropdown')}}",
              success: function(data) {
                $.each(data, function(index, element) {
                  select += "<option value='"+ element.id +"'>" + element.cognome + " "+element.name+"</option>";
                });
                select += "</select>";
              }
            });

            row += "<td>"+select+"</td>";
            for(var i=0; i<num_colonne; i++){
              row += "<td>";
              row += "<input name='colonna["+t+"]["+key[i]+"]' type='hidden' value='0'/>";
              row += "<input class='form-control' name='colonna["+t+"]["+key[i]+"]' type='checkbox' value='1' />";
            }
            row += "</tr>";

            $('#elenco_values tr:last').after(row);
          }

          // function redirect_check(route, method='POST', send_param=true){
          //   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          //   var selected = [];
          //   $('input[type=checkbox]').each(function() {
          //     if ($(this).is(":checked")){
          //       selected.push($(this).attr('value'));
          //     }
          //   });
          //   if(send_param){
          //     $.redirect(route, {check: JSON.stringify(selected), _token: CSRF_TOKEN}, method);
          //   }else{
          //     $.redirect(route, {}, method);
          //   }
          //
          // }

          function disable_select(checkbox, id_select, inverse=false){
            if(inverse){
              $('#'+id_select).prop('disabled', !checkbox.checked);
            }else{
              $('#'+id_select).prop('disabled', checkbox.checked);
            }

          }

          </script>
          @stack('scripts')
        </body>
        </html>
