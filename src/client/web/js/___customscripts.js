/*
 * FUNCION MEJORADA DE JsonAjaxRequest
 * @param mixed $params arreglo con los parametros para ejecucion de peticion http
 * - string $params.URL ruta a la que se envían los datos
 * - string $params.METHOD método de envío ajax [POST/GET]. Si no se define por defecto será GET
 * - string $params.DATATYPE Tipo de dato que se espera recibir del servidor [json/xml/html/script]
 * - Array $params.DATA arreglo con datos a enviar
 * - mixed $params.SUCCESS función que maneja los valores de retorno de controlador
 * - mixed $params.FINALLY arreglo con acciones a ejecutar después de procesamiento
 * - mixed $params.ERROR acciones a ejecutar en caso de que procesamiento devuelva error
 */
var AjaxRequest = function ($params) {
    if (!('METHOD' in $params)) {
        $params.METHOD = "GET";
    }

    if (!('DATATYPE' in $params)) {
        $params.DATATYPE = "json";
    }
    if (!('CONTENTTYPE' in $params)) {
        $params.CONTENTTYPE = 'application/x-www-form-urlencoded; charset=UTF-8';
    }

    if (!('PROCESSDATA' in $params)) {
        $params.PROCESSDATA = true;
    }
    if (!('CACHE' in $params)) {
        $params.CACHE = true;
    }
    
    if (!('BEFORESEND' in $params)) {
        $params.BEFORESEND = function (x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/json;charset=UTF-8");
            }
        };
    }
    
    $.ajax({
        type: $params.METHOD,
        dataType: $params.DATATYPE,
        data: $params.DATA,
        cache: $params.CACHE,
        processData: $params.PROCESSDATA,
        contentType: $params.CONTENTTYPE,
        beforeSend: $params.BEFORESEND,
        url: $params.URL,
        success: function ($data)
        {
            if ($params.DATATYPE === "json") {
                if ($data.success) {
                //$success($data);
                    $params.SUCCESS($data);
                } else {
                //Permite ceder el control a la vista que lo invoca para manejar el error
                    if ($params.ERROR) {
                        $params.ERROR($data);
                    } else {
                        if ($data.message) {
                            alert($data.message);
                        } else {
                            console.log("Error Desconocido");
                        }
                    }
                }
            } else {
                $params.SUCCESS($data);
            }
            if ($params.FINALLY) {
                $params.FINALLY($data);
            }
        }

    });
};

var clearField = function($params){
    $.each($params, function(i, key){
        $("#"+key.id).val("");

    });
};


/*
* @param array mixed
* @returns array mixed         
* @description Función que recupera valores de un formulario en formato de array
* */
var getValuesForm = function($params){
   var $detalle = {};
   if($params.ID){
        var $frm = $params.ID;
        if(!('EXCLUDE' in $params)){
            $params.EXCLUDE = {};
        }
        if(!('GETFORM' in $params)){
            $params.GETFORM = false;
        } else {
            $detalle = new FormData($("#"+$params.ID)[0]);
            return $detalle;
        }
        if(!('GETBYNAME' in $params)){
             $params.GETBYNAME = false;
         }

         if(!('UPPERCASE' in $params)){
             $params.UPPERCASE = true;
         }
         var $prefix = "";
         if(('PREFIX' in $params)){
             $prefix = $params.PREFIX;
             $prefix = $params.UPPERCASE ? $prefix.toUpperCase():$prefix.toLowerCase();
         } 
         var $fielddet = $("#"+$frm+" input[type=text], #"+$frm+" select, #"+$frm+" textarea, #"+$frm+" input[type=hidden]");
         var $empty = ($params.EMPTY ? true:false);
         $.each($fielddet,function(i, value){
             var $id = $(value).attr("id");
             if(typeof  $id !== 'undefined'){
                  var $key = $params.UPPERCASE ? $id.toUpperCase():$id.toLowerCase();
                  $key = $key.replace($prefix,"");
                  var $value = $(value).val();
                  var $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                  if(($value !== '' || $empty) && $validate){
                      var $valores = {};
                      $valores[$key]=$value;
                      if($params.GETFORM){
                            var $name = $(value).attr('name');
                            $detalle.append($name, $value);
                      } else {
                          $.extend($detalle,$valores);
                      }
                  }
             }
         });
   }
   return $detalle;
};


/*
* @param array mixed
* @returns void
* @description Function that set the values from the DATA param of input array to
* fields from form defined in ID param
* */
var setValuesForm = function($params){
    if($params.ID && $params.DATA){
        var $frm = $params.ID;
        if(!('UPPERCASE' in $params)){
            $params.UPPERCASE = true;
        }
        if(!('LOWERCASE' in $params)){
            $params.LOWERCASE = false;
        }
        if(!('UNBOUNDNAME' in $params)){
            $params.LOWERCASE = false;
        }
        if(!('SEPARATORS' in $params)){
            $params.SEPARATORS = {};
        }
        if(!('MATCHBYNAME' in $params)){
            $params.MATCHBYNAME = false;
        }
        var $prefix = "";
        if(('PREFIX' in $params)){
            $prefix = $params.PREFIX;
        } 
        var $fielddet = $("#"+$frm+" input, #"+$frm+" select , #"+$frm+" textarea");
        $.each($fielddet,function(i, value){
            var $type = $(value).attr('type');
            var $id = $(value).attr('id');
            if(typeof $id !== 'undefined'){
                var $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                var $name = $(value).attr('name');
                var $inputName = $name;
                
                var $key;
                var $keyName;
                if(!$params.MATCHBYNAME){
                    $id = $id.replace($prefix,"");
                    $id = $params.UPPERCASE ? $id.toUpperCase(): $params.LOWERCASE ? $id.toLowerCase():$id;
                    $key = $id;
                } else {
                    if($params.UNBOUNDNAME){
                        var $data = {};
                        $data.NAME = $name;
                        $data.SEPARATORS = $params.SEPARATORS;
                        $name = unboundName($data);
                    }
                    $key = $name;
                }
                if($validate){
                    switch($type){
                        case 'checkbox':
                            $("input:checkbox[name="+$inputName+"][value=" + ($params.DATA[$key]) + "]").attr('checked', 'checked');
                            break;
                        case 'radio':
                            $("input:radio[name="+$inputName+"][value=" + ($params.DATA[$key]) + "]").attr('checked', 'checked');
                            break;
                        default :
                            $(value).val($params.DATA[$key]);
                            break;
                    }
                }
            }
        });
    }
};

var unboundName = function($params){
    if($params.NAME && $params.SEPARATORS){
        var $sep = $params.SEPARATORS;
        var $data = $params.NAME;
        var $string = $data;
        $.each($sep, function(i, val){
            var $values =  $string.split(val);
            if($values[$values.length-1] === ''){
                $string = $values[$values.length-2];
            } else {
                $string = $values[$values.length-1];
            }
        });
        return $string;
    } else {
        return $params;
    }
};

var setErrorsModel = function($params){
    if($params.ID && $params.ERRORS){
        var $prefix = "";
        if(('PREFIX' in $params)){
            $prefix = $params.PREFIX;
        } 
        var $errors = $params.ERRORS;
        $.each($errors, function (key, obj) {
            var $id = $prefix+key.toLowerCase();
            $('#'+$id).attr('aria-invalid',true);
            $('#'+$id).parent('div')
                    .removeClass('has-success')
                    .addClass('has-error');
            $('#'+$id).parent('div')
                    .find('div.help-block')
                    .html(obj);
        });
    }
};

var clearForm = function($params){
    if($params.ID){
        var id = $params.ID;
        $('#'+id+' input[type=text],#'+id+' input[type=hidden]')
            .val('')
            .removeAttr('aria-invalid');
        $('#'+id+' select').val($('#'+id+' select option:first').val());
        $('#'+id).find('div')
            .removeClass('has-success')
            .removeClass('has-error');
        $('#'+id).find('div.help-block').html("");
        $('#'+id+' div.help-block').empty();
        var $default = $params.DEFAULTS;
        $.each($default, function (key, obj) {
            var $input = $('#'+key);
            var $type = $input.attr('type');
            switch($type){
                case 'checkbox':
                    $("#"+key+"[value=" + obj + "]").attr('checked', 'checked');
                    break;
                case 'radio':
                    $("#"+key+"[value=" + obj + "]").attr('checked', 'checked');
                    break;
                default :
                    $input.val(obj);
                    break;
            }
        });
    }
};
