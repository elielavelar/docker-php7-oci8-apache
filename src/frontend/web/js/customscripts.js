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
	
    if(!('LOADER' in $params)){
            $params.LOADER = false;
    }
    
    if (!('BEFORESEND' in $params)) {
        $params.BEFORESEND = function (x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/json;charset=UTF-8");
            }
			if($params.LOADER === true){
				//showLoader();
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
			//var $p = [];
			//$p.LOAD = false;
			//showLoader($p);
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
            if(!('GETBYNAME' in $params)){
                 $params.GETBYNAME = false;
            }
            
            if(!('READONLY' in $params)){
                 $params.READONLY = true;
            }

            if(!('EMPTY' in $params)){
                 $params.EMPTY = false;
            }

            if(!('UPPERCASE' in $params)){
                $params.UPPERCASE = true;
            }

            if(!('LOWERCASE' in $params)){
                $params.LOWERCASE = false;
            }

           if(!('UNBOUNDNAME' in $params)){
               $params.UNBOUNDNAME = false;
           }
           if(!('SEPARATORS' in $params)){
               $params.SEPARATORS = {};
           }
           if(!('REPLACESTRING' in $params)){
               $params.REPLACESTRING = {};
               $params.REPLACE = false;
           } else {
               $params.REPLACE = true;
           }

           var $prefix = "";
           if(('PREFIX' in $params)){
               $prefix = $params.PREFIX;
               $prefix = $params.UPPERCASE ? $prefix.toUpperCase():($params.LOWERCASE ? $prefix.toLowerCase():$prefix);
           } 
           var $fielddet = $("#"+$frm+" input[type=text], #"+$frm+" input[type=number], #"+$frm+" select, #"+$frm+" textarea, #"+$frm+" input[type=hidden], #"+$frm+" input[type=radio]");
           var $empty = ($params.EMPTY ? true:false);
           $.each($fielddet,function(i, value){
                var $id = $(value).attr("id");
                var $undefinedId = (typeof  $id === 'undefined');
                var $name = $(value).attr('name');
                var $undefinedName = (typeof  $name === 'undefined' || jQuery.trim($name) === "");
                var $type = $(value).attr('type');
                var $readonly = $(value).is('[readonly="readonly"]');
                if( !$undefinedId || ($params.GETBYNAME && !$undefinedName)){
                    if(!$undefinedId && !$params.GETBYNAME){
                        var $key = $params.UPPERCASE ? $id.toUpperCase():($params.LOWERCASE ? $id.toLowerCase():$id);
                        $key = $key.replace($prefix,"");
                        var $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                    } else if($params.GETBYNAME || !$undefinedName){
                        var $key = $name;
                        var $validate = (jQuery.inArray($name,$params.EXCLUDE) !== -1 ? false:true);
                    } else {return [];}
                    var $value = $(value).val();
                    if(($value !== '' || $empty) && ($params.READONLY || (!$params.READONLY && $readonly !== true) ) && $validate){
                        if($params.UNBOUNDNAME){
                            var $data = {};
                            $data.NAME = $key;
                            $data.SEPARATORS = $params.SEPARATORS;
                            $data.REPLACE = $params.REPLACE;
                            $data.REPLACESTRING = $params.REPLACESTRING;
                            $key = unboundName($data);
                        }
                        /*FINAL ASIGNATION OF VALUES*/
                        switch($type){
                            case 'radio':
                                if($(value).is(":checked")){
                                    var $valores = {};
                                    $valores[$key] = $value;
                                    $.extend($detalle, $valores);
                                }
                                break;
                            case 'checkbox':
                                if($(value).is(":checked")){
                                    var $valores = {};
                                    $valores[$key] = $value;
                                    $.extend($detalle, $valores);
                                }
                                break;
                            default:
                                var $valores = {};
                                $valores[$key] = $value;
                                $.extend($detalle, $valores);
                                break;
                        }
                      }
                }
           });
        }
        return $detalle;
    };

var xxgetValuesForm = function($params){
    var $detalle = {};
    if($params.ID){
        var $frm = $params.ID;
        if(!('EXCLUDE' in $params)){
            $params.EXCLUDE = {};
        }
        if(!('GETBYNAME' in $params)){
             $params.GETBYNAME = false;
        }
        
        if(!('EMPTY' in $params)){
             $params.EMPTY = false;
        }

        if(!('UPPERCASE' in $params)){
            $params.UPPERCASE = true;
        }

        if(!('LOWERCASE' in $params)){
            $params.LOWERCASE = false;
        }

       if(!('UNBOUNDNAME' in $params)){
           $params.UNBOUNDNAME = false;
       }
       if(!('SEPARATORS' in $params)){
           $params.SEPARATORS = {};
       }
       if(!('REPLACESTRING' in $params)){
           $params.REPLACESTRING = {};
           $params.REPLACE = false;
       } else {
           $params.REPLACE = true;
       }

       var $prefix = "";
       if(('PREFIX' in $params)){
           $prefix = $params.PREFIX;
           $prefix = $params.UPPERCASE ? $prefix.toUpperCase():($params.LOWERCASE ? $prefix.toLowerCase():$prefix);
       } 
       var $fielddet = $("#"+$frm+" input[type=text], #"+$frm+" select, #"+$frm+" textarea, #"+$frm+" input[type=hidden]");
       var $empty = ($params.EMPTY ? true:false);
       $.each($fielddet,function(i, value){
            var $id = $(value).attr("id");
            if(typeof  $id !== 'undefined'){
                  var $key = $params.UPPERCASE ? $id.toUpperCase():($params.LOWERCASE ? $id.toLowerCase():$id);
                  $key = $key.replace($prefix,"");
                  var $value = $(value).val();
                  var $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                  if(($value !== '' || $empty) && $validate){
                    if($params.GETBYNAME){
                        var $key = $(value).attr('name');
                    } 
                    if($params.UNBOUNDNAME){
                        var $data = {};
                        $data.NAME = $key;
                        $data.SEPARATORS = $params.SEPARATORS;
                        $data.REPLACE = $params.REPLACE;
                        $data.REPLACESTRING = $params.REPLACESTRING;
                        $key = unboundName($data);
                    }
                    /*FINAL ASIGNATION OF VALUES*/
                    var $valores = {};
                    $valores[$key] = $value;
                    $.extend($detalle, $valores);
                  }
            }
       });
    }
    return $detalle;
};


var setValuesForm = function($params){
    if($params.ID && $params.DATA){
        var $frm = $params.ID;
        var $fielddet = $("#"+$frm+" input, #"+$frm+" number, #"+$frm+" select , #"+$frm+" textarea, #"+$frm+" input[type=checbox], #"+$frm+" input[type=radio]");
        var $selectableInputsNames = [];
        var $selectableInputs = [];
        var $inputsParams = {};
        
        if(!('UPPERCASE' in $params)){
            $params.UPPERCASE = true;
        }
        if(!('LOWERCASE' in $params)){
            $params.LOWERCASE = false;
        }
        if(!('UNBOUNDNAME' in $params)){
            $params.UNBOUNDNAME = false;
        }
        if(!('SEPARATORS' in $params)){
            $params.SEPARATORS = {};
        }
        if(!('REPLACESTRING' in $params)){
            $params.REPLACESTRING = {};
            $params.REPLACE = false;
        } else {
            $params.REPLACE = true;
        }
        if(!('MATCHBYNAME' in $params)){
            $params.MATCHBYNAME = false;
        }
        if(!('SETBYID' in $params)){
            $params.SETBYID = false;
        }
        var $prefix = "";
        if(('PREFIX' in $params)){
            $prefix = $params.PREFIX;
        } 
        
        $.each($fielddet,function(i, value){
            var $type = $(value).attr('type');
            var $id = $(value).attr('id');
            var $name = $(value).attr('name');
            var $input = null;
            var $validate = false;
            var $dataValue = null;
            var $key = null;
            
            if($params.MATCHBYNAME){
                $validate = (jQuery.inArray($name,$params.EXCLUDE) !== -1 ? false:true);
                var $inputName = $name;
                $inputName = $inputName.replace($prefix,"");
                if($params.UNBOUNDNAME){
                    if($params.UNBOUNDNAME){
                        var $data = {};
                        $data.NAME = $inputName;
                        $data.SEPARATORS = $params.SEPARATORS;
                        $data.REPLACESTRING = $params.REPLACESTRING;
                        $inputName = unboundName($data);
                        if($params.REPLACE){
                            var $par = {};
                            $par.STRING = $inputName;
                            $par.REPLACESTRING = $params.REPLACESTRING;
                            $inputName = replaceString($par);
                        } 
                    }
                }
                $key = $inputName;
            } else {
                $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                $id = $id.replace($prefix,"");
                $id = $params.UPPERCASE ? $id.toUpperCase(): $params.LOWERCASE ? $id.toLowerCase():$id;
                $key = $id;
            }
            if($key !== null && $key !== "undefined" && $validate){
                switch ($type){
                    case 'checkbox':
                    case 'radio':
                        if(jQuery.inArray($key,$selectableInputsNames) === -1 ){
                            var $values = {};
                            var $data = $params.DATA[$key];
                            if($data !== "undefined"){
                                    var $value = {
                                    key: $key,
                                    name: $name,
                                    data: $params.DATA[$key],
                                    type: $type
                                };
                                $values[$key] = $value;
                                $selectableInputsNames.push($key);
                                $.extend($selectableInputs, $values);
                            }
                        }
                        break;
                    default: 
                        $input = $(value);
                        $dataValue = $params.DATA[$key];
                        $input.val($dataValue);
                        break;
                }
            }
        });
        $inputsParams.NAMES = $selectableInputsNames.length > 0 ? $selectableInputsNames:null;
        $inputsParams.VALUES = $selectableInputs;
        $inputsParams.ID = $frm;
        
        setSelectableInputValues($inputsParams);
    }
};

var setSelectableInputValues = function($params){
    if($params.NAMES && $params.VALUES && $params.ID){
        var $names = $params.NAMES;
        var $values = $params.VALUES;
        var $frm = $params.ID;
        $.each($names, function(i, value){
            if((value in $values)){
                var $dataset = $values[value];
                var $name = $dataset.name;
                var $data = $dataset.data;
                var $_type = $dataset.type;
                $.each($data, function(j, val){
                    var $type = typeof(val);
                    var $checked = true;
                    switch ($type){
                        case 'object':
                            $.each(val, function(k, v){
                                $checked = (v == '1');
                                if($checked){
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (k) + "]").attr('checked', $checked);
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (k) + "]").prop('checked', true);
                                } else {
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (k) + "]").removeAttr('checked');
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (k) + "]").prop( "checked", $checked );
                                }
                            });
                            break;
                        case 'array':
                        default:
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (val) + "]").attr('checked', $checked);
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (val) + "]").prop('checked', $checked);
                            break;
                    }
                });
            }
        });
    }
    
};

/*
* @param array mixed
* @returns void
* @description Function that set the values from the DATA param of input array to
* fields from form defined in ID param
* */
var _setValuesForm = function($params){
    if($params.ID && $params.DATA){
        var $frm = $params.ID;
        if(!('UPPERCASE' in $params)){
            $params.UPPERCASE = true;
        }
        if(!('LOWERCASE' in $params)){
            $params.LOWERCASE = false;
        }
        if(!('UNBOUNDNAME' in $params)){
            $params.UNBOUNDNAME = false;
        }
        if(!('SEPARATORS' in $params)){
            $params.SEPARATORS = {};
        }
        if(!('REPLACESTRING' in $params)){
            $params.REPLACESTRING = {};
            $params.REPLACE = false;
        } else {
            $params.REPLACE = true;
        }
        if(!('MATCHBYNAME' in $params)){
            $params.MATCHBYNAME = false;
        }
        if(!('SETBYID' in $params)){
            $params.SETBYID = false;
        }
        var $prefix = "";
        if(('PREFIX' in $params)){
            $prefix = $params.PREFIX;
        } 
        var $fielddet = $("#"+$frm+" input, #"+$frm+" select , #"+$frm+" textarea");
        $.each($fielddet,function(i, value){
            var $type = $(value).attr('type');
            var $id = $(value).attr('id');
            var $_name = $(value).attr('name');
            if(typeof $id !== "undefined"){
                var $validate = (jQuery.inArray($id,$params.EXCLUDE) !== -1 ? false:true);
                var $name = $(value).attr('name');
                var $inputName = $name;
                var $_name = $name;
                var $key;
                if(!$params.MATCHBYNAME){
                    $id = $id.replace($prefix,"");
                    $id = $params.UPPERCASE ? $id.toUpperCase(): $params.LOWERCASE ? $id.toLowerCase():$id;
                    $key = $id;
                } else {
                    if($params.UNBOUNDNAME){
                        var $data = {};
                        $data.NAME = $name;
                        $data.SEPARATORS = $params.SEPARATORS;
                        $data.REPLACESTRING = $params.REPLACESTRING;
                        $name = unboundName($data);
                        if($params.REPLACE){
                            var $par = {};
                            $par.STRING = $name;
                            $par.REPLACESTRING = $params.REPLACESTRING;
                            $name = replaceString($par);
                        } 
                    }
                    if($params.SETBYID){
                        $inputName = $prefix+$id;
                    }
                    $key = $name;
                }
                if($validate){
                    switch($type){
                        case 'checkbox':
                            //$("input:checkbox[name="+$inputName+"][value=" + ($params.DATA[$key]) + "]").attr('checked', 'checked');
                            break;
                        case 'radio':
                            $("input:radio[name="+$inputName+"][value=" + ($params.DATA[$key]) + "]").attr('checked', 'checked');
                            break;
                        default :
                            if($params.SETBYID){
                                $('#'+$id).val($params.DATA[$key]);
                            } else {
                                $(value).val($params.DATA[$key]);
                            }
                            
                            
                            break;
                    }
                }
            } else if($params.MATCHBYNAME && $_name !== "undefined"){
                switch($type){
                        case 'checkbox':
                           // $("input:checkbox[name="+$_name+"][value=" + ($params.DATA[$_name]) + "]").attr('checked', 'checked');
                            break;
                        case 'radio':
                            $("input:radio[name="+$_name+"][value=" + ($params.DATA[$_name]) + "]").attr('checked', 'checked');
                            break;
                        default :
                            break;
                }
            }
        });
    }
};

var unboundName = function($params){
    if($params.NAME && $params.SEPARATORS){
        var $sep = $params.SEPARATORS;
        var $data = $params.NAME;
        var $replace = $params.REPLACESTRING;
        if(!('REPLACESTRING' in $params)){
            $params.REPLACE = false;
        } else {
            $params.REPLACE = true;
        }
        var $string = $data;
        if($params.REPLACE){
            var $str = {};
            $str.STRING = $string;
            $str.REPLACESTRING = $replace;
            $string = replaceString($str);
        }
        $.each($sep, function(i, val){
            var $values =  $string.split(val);
            var $l = $values.length;
            if($values[$l-1] === ''){
                $string = $values[$l-2];
            } else {
                $string = $values[$l-1];
            }
        });
        return $string;
    } else {
        return $params;
    }
};

var replaceString = function($params){
    if($params.STRING && $params.REPLACESTRING){
        var $string = $params.STRING;
        var $str = $string;
        var $replace = $params.REPLACESTRING;
        $.each($replace, function(i, val){
            var $old = i;
            var $new = val;
            $str = $string.replace($old,$new);
            $string = $str;
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
        if(!('EXTRA' in $params)){
            $params.EXTRA = function(){};
        }
        var $errors = $params.ERRORS;
        $.each($errors, function (key, obj) {
            var $id = $prefix+key.toLowerCase();
            if(Array.isArray(obj)){
                var text = obj.join("-");
                var $div = $('#'+$id).parent('div')
                    .find('div.help-block');
                if($div.length !== 0){
                     $('#'+$id).attr('aria-invalid',true);
                    $('#'+$id).parent('div')
                        .removeClass('has-success')
                        .addClass('has-error');
                    $div.html(text);
                } else {
                    $("div.field-"+$id)
                        .removeClass('has-success')
                        .addClass('has-error');
                    $("div.field-"+$id)
                        .find('div.help-block').html(text);
                    $('#'+$id).attr('aria-invalid',true);
                }
            } else {
                $('#'+$id).attr('aria-invalid',true);
                $('#'+$id).parent('div')
                        .removeClass('has-success')
                        .addClass('has-error');
                $('#'+$id).parent('div')
                    .find('div.help-block')
                    .html(obj);
            }
                    
        });
        $params.EXTRA();
    }
};

var clearForm = function($params){
    if($params.ID){
        if(!('DEFAULTS' in $params)){
            $params.DEFAULTS = {};
        }
        if(!('EXTRA' in $params)){
            $params.EXTRA = function(){};
        }
        if(!('BEFORE' in $params)){
            $params.BEFORE = function(){};
        }
        if(!('BEFOREDEFAULTS' in $params)){
            $params.BEFOREDEFAULTS = function(){};
        }
        var id = $params.ID;
        $params.BEFORE();
        $('#'+id+' input[type=text],#'+id+' input[type=hidden], #'+id+' textarea, #'+id+' input[type=number], #'+id+' input[type=password], #'+id+' input[type=email]')
            .val('')
            .removeAttr('aria-invalid')
            .removeAttr('disabled');
			
        var $select = $('#'+id+' select');
		$.each($select, function(key, obj){
			var $_id = $(obj).attr('id');
			$(obj).val($("#"+$_id+' option:first').val())
				.removeAttr('disabled');
		});
		
        $('#'+id).find('div')
            .removeClass('has-success')
            .removeClass('has-error');
    
        $("#"+id+" input[type=checkbox]")
                .removeAttr('checked')
                .prop('checked', false)
                .removeAttr('disabled');
        
        $('#'+id).find('div.help-block').html("");
        $('#'+id+' div.help-block').empty();
        $params.BEFOREDEFAULTS();
        var $default = $params.DEFAULTS;
        $.each($default, function (key, obj) {
            var $input = $('#'+key);
            var $type = $input.attr('type');
            switch($type){
                case 'checkbox':
                case 'radio':
                    $("#"+key+"[value=" + obj + "]").attr('checked', 'checked');
                    $("#"+key+"[value=" + obj + "]").prop('checked', true);
                    break;
                default :
                    $input.val(obj);
                    break;
            }
        });
        $params.EXTRA();
    }   
};

    var getDataBind = function($params){
        if(('SELECTOR' in $params)){
            var $selector = $params.SELECTOR;
            var $type = ('TYPE' in $params) ? $params.TYPE:'RAW';
            var $schema = ('SCHEMA' in $params) ? $params.SCHEMA:'DATA';
            if($type === 'RAW'){
                return $selector.data().bind;
            } else {
                if($schema == 'DATA'){
                    var $data = $selector.data();
                } else if($schema == 'BIND'){
                    var $raw = $bind.split(",");
                    var $data = {};
                    $.each($raw, function(key, value){
                        var $values = value.split(':');
                        var $_id = jQuery.trim($values[0]);
                        var $_value = jQuery.trim($values[1]);
                        $data[$_id] = $_value;
                    });
                } else {
                    var $data = $selector.data().bind;
                }
                
                return $data;
            }
        } else {
            return null;
        }
    };

