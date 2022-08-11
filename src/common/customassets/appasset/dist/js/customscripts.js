
const AjaxHttpRequest = async ({url, ...params}) => {

    let searchable = false;
    let formData = false;
    if ((url === 'undefined' || url.trim().length === 0)) {
        throw Error('Attribute Url is required');
    }
    if (!('options' in params)) {
        params.options = {}
    }
    if (('formData' in params)) {
        formData = params.formData
    }
    if (!('mode' in params.options)) {
        params.options.mode = 'cors'
    }
    if (!('cache' in params.options)) {
        params.options.cache = 'no-cache'
    }
    if (!('credentials' in params.options)) {
        params.options.credentials = 'same-origin'
    }
    if (!('headers' in params.options)) {
        params.options.headers = formData ?
        {
            'Content-Type':'application/x-www-form-urlencoded'
        }
        : {
            'Content-Type' : 'application/json'
        }
    }
    if (!('redirect' in params.options)) {
        params.options.redirect = 'follow'
    }
    if (!('referrerPolicy' in params.options)) {
        params.options.referrerPolicy = 'no-referrer-when-downgrade'
    }
    if (('data' in params)) {
        params.options.method = 'POST'
        params.options.body = formData ?  (new URLSearchParams(params.data))
            : JSON.stringify( params.data )
    }
    if (!('form' in params)) {
        params.form = false;
    }
    if (!('method' in params.options)) {
        params.options.method = 'GET'
    }
    if (('search' in params)) {
        searchable = true;
        url = new URL(url)
        let values = params.search
        url.search = new URLSearchParams( values ).toString();
    }
    if (!('datatype' in params)) {
        params.datatype = 'json';
    }
    if (!('success' in params)) {
        params.success =  data => data
    }
    if (!('extra' in params)) {
        params.extra = ( data ) => data
    }
    if (!('error' in params)) {
        params.error = "undefined"
    }
    if (!('finally' in params)) {
        params.finally = ( data ) => data
    }
    try {
        let response = ( data ) => {
            if( !data.ok ){
                return data.text().then( error => {
                    throw JSON.parse(error)
                })
            }
            return params.datatype === 'json' ? data.json() : data;
        }
        const result = await fetch( url, params.options );
        const data = await response( result )
        params.success( data );
        params.extra( data );
        params.finally( data );
    } catch (e) {
        if( typeof params.error !== "undefined" && typeof params.error === 'function'){
            params.error( e )
        } else if (typeof swal == "undefined") {
            alert(e.message)
            console.log( e.message )
        } else {
            swal('Error', e.message, 'error')
            console.log( e.message )
        }
    }
};
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
        success: function ($data){
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
        if(!('SELECTOR' in $params)){
            $selector = '#';
        } else {
            $selector = $params.SELECTOR;
        }
        $frm = $selector+$frm;
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
       var $fielddet = $($frm+" input[type=text], "+$frm+" input[type=number], "+$frm+" select, "+$frm+" textarea, "+$frm+" input[type=hidden], "+$frm+" input[type=radio]");
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

var getValuesBySelector = function($params){
    var $response = {};
    var $detail = {};
    
    if($params.ID){
        //DATABINDING: array, form
        $params.DATABINDING = (!('DATABINDING' in $params)) ? 'array' : $params.DATABINDING;
        $params.FORMNAME = (!('FORMNAME' in $params)) ? 'form-data' : $params.FORMNAME;
        $params.USESELECTOR = (!('USESELECTOR' in $params)) ? true : $params.USESELECTOR;
        $params.SELECTOR = (!('SELECTOR' in $params)) ? '#': $params.SELECTOR;
        $params.PREFIX = !('PREFIX' in $params) ? '': $params.PREFIX;
        $selector = ($params.USESELECTOR ? $params.SELECTOR : '')+$params.ID;
        var $fielddet = $($selector+" input[type=text], "+$selector+" input[type=number], "+$selector+" select, "+$selector+" textarea, "+$selector+" input[type=hidden], "+$selector+" input[type=radio]");
        if($params.DATABINDING === 'array'){
            $response = $detail;
            $.each($fielddet, function(i, field){
                var $type = (typeof ($(field).attr('type')) === 'undefined' ? field.tagName.toLowerCase() : $(field).attr('type'));
                var $key = (typeof ($(field).attr('id')) === 'undefined' ? field.attr('name') : $(field).attr('id'));
                switch($type){
                    case 'radio':
                        if($(field).is(":checked")){
                            var $value = $(field).val();
                            var $values = {};
                            $values[$key] = $value;
                            $.extend($response, $values);
                        }
                        break;
                    case 'checkbox':
                        if($(field).is(":checked")){
                            var $value = $(field).val();
                            var $values = {};
                            $values[$key] = $value;
                            $.extend($response, $values);
                        }
                        break;
                    case 'select':
                        var $value = $(field).find('option:selected').val();
                        var $values = {};
                        $values[$key] = $value;
                        $.extend($response, $values);
                        break;
                    default:
                        var $value = $(field).val();
                        var $values = {};
                        $values[$key] = $value;
                        $.extend($response, $values);
                        break;
                }
            });
        } else {
            var $form = new FormData();
            $.each($fielddet, function(i , field){
                console.log(field.tagName.toLowerCase());
                //var $type = (($(field).attr('type')) === 'undefined' ? field.tagName : $(field).attr('type'));
                //console.log($type);
                //var $value = null; 
                
                //$form.append($(field).attr('name'), $value);
            });
            $response = $form;
        }
    }
    return $response;
};

var setValuesForm = function($params){
    if($params.ID && $params.DATA){
        var $frm = $params.ID;
        var $fielddet = $("#"+$frm+" input, #"+$frm+" number, #"+$frm+" select , #"+$frm+" textarea, #"+$frm+" input[type=checbox], #"+$frm+" input[type=radio]");
        var $selectableInputsNames = [];
        var $selectableInputs = [];
        var $inputsParams = {};
        
        if(!('EXTRA' in $params)){
            $params.EXTRA = function(){};
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
        if(!('INPUTEXTRAS' in $params)){
            $params.INPUTEXTRAS = {};
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
        if(!('EXECUTETRIGGER' in $params)){
            $params.EXECUTETRIGGER = false;
        }
        if(!('TRIGGER' in $params)){
            $params.TRIGGER = 'change';
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
            var $inputName = null;
            var $dataName = null;
            
            if($params.MATCHBYNAME){
                $validate = (jQuery.inArray($name,$params.EXCLUDE) !== -1 ? false:true);
                $inputName = $name;
                $dataName = $name;
                if($params.UNBOUNDNAME){
                    $dataName = $inputName.replace($prefix,"");
                    if($params.UNBOUNDNAME){
                        var $data = {};
                        $data.NAME = $dataName;
                        $data.SEPARATORS = $params.SEPARATORS;
                        $data.REPLACESTRING = $params.REPLACESTRING;
                        $dataName = unboundName($data);
                        if($params.REPLACE){
                            var $par = {};
                            $par.STRING = $dataName;
                            $par.REPLACESTRING = $params.REPLACESTRING;
                            $dataName = replaceString($par);
                        } 
                    }
                } else {
                    $inputName = $prefix+'['+$inputName+']';
                }
                $key = $dataName;
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
                                    dataname: $dataName,
                                    inputname: $inputName,
                                    data: $params.DATA[$key],
                                    extra: ((jQuery.inArray($key,$params.INPUTEXTRAS) === -1 ) ? function(){} : $params.INPUTEXTRAS[$key]),
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
                        $params.EXECUTETRIGGER ? $input.val($dataValue).trigger($params.TRIGGER) : $input.val($dataValue);
                        ($key in $params.INPUTEXTRAS) ?  $params.INPUTEXTRAS[$key]() : null;
                        break;
                }
            }
        });
        $inputsParams.NAMES = $selectableInputsNames.length > 0 ? $selectableInputsNames:null;
        $inputsParams.VALUES = $selectableInputs;
        $inputsParams.ID = $frm;
        $inputsParams.EXECUTETRIGGER = $params.EXECUTETRIGGER;
        $inputsParams.TRIGGER = $params.TRIGGER;
        setSelectableInputValues($inputsParams);
        $params.EXTRA();
    }
};

var setSelectableInputValues = function($params){
    if($params.NAMES && $params.VALUES && $params.ID){
        var $names = $params.NAMES;
        var $values = $params.VALUES;
        var $frm = $params.ID;
        if(!('EXECUTETRIGGER' in $params)){
            $params.EXECUTETRIGGER = false;
        }
        if(!('TRIGGER' in $params)){
            $params.TRIGGER = 'change';
        }
        $.each($names, function(i, value){
            if((value in $values)){
                var $dataset = $values[value];
                var $name = $dataset.name;
                var $data = $dataset.data;
                var $_type = $dataset.type;
                var $_dataType = typeof($data);
                switch($_dataType){
                    case 'object':
                    case 'array':
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
                                        $params.EXECUTETRIGGER ? $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (k) + "]").trigger($params.TRIGGER) : null;
                                    });
                                    break;
                                case 'array':
                                default:
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (val) + "]").attr('checked', $checked);
                                    $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (val) + "]").prop('checked', $checked);
                                    $params.EXECUTETRIGGER ? $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + (val) + "]").trigger($params.TRIGGER) : null;
                                    break;
                            }
                        });
                        break;
                    default :
                        $checked = ($data == '1');
                        if($checked){
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + ($data) + "]").attr('checked', $checked).trigger('change');
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + ($data) + "]").prop('checked', true);
                        } else {
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + ($data) + "]").removeAttr('checked').trigger('change');
                            $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + ($data) + "]").prop( "checked", $checked );
                        }
                        $params.EXECUTETRIGGER ? $("#"+$frm+" input:"+$_type+"[name='"+$name+"'][value=" + ($data) + "]").trigger($params.TRIGGER) : null;
                        break;
                }
                    
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
        $.each($errors, function(key, obj){
            var $id = $prefix+key.toLowerCase();
            if(Array.isArray(obj)){
                var text = obj.join("-");
                var $div = $('#'+$id).parent('div').find('div.invalid-feedback');
                if($div.length !== 0){
                    $('#'+$id).attr('aria-invalid',true)
                            .removeClass('is-valid')
                            .addClass('is-invalid');
                    $div.html(text);
                } else {
                    $("#"+$id).attr('aria-invalid',true)
                            .removeClass('is-valid')
                            .addClass('is-invalid');
                    $("div.field-"+$id)
                        .find('div.invalid-feedback').html(text);
                }
            } else {
                $('#'+$id).attr('aria-invalid',true);
                $('#'+$id).parent('div')
                        .removeClass('has-success')
                        .addClass('has-error');
                $('#'+$id).parent('div')
                    .find('div.invalid-feedback')
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
            $(obj).val($(obj).find('option:first').val()).removeAttr('disabled');
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
    var _dynamicfield = {
        URL: null,
        FIELD: null,
        PARENT: null,
        FORM: null
    };
    
    var getdynamicfield = function(f){
        var $parents = $(f).parents('.extfield');
        var $parent = $parents[0];
        var $field = $($parent).clone();
        var $input = null;
        var $types = ['input','hidden','select','textarea','checkbox','radiobutton'];
        $.each($field.find('.input-group').children(), function(i, child){
            var $tag = (child.tagName);
            if((jQuery.inArray($tag.toLowerCase(), $types) !== -1)){
                $input = child;
                return;
            }
        });
        if($input !== null){
            var $id = $($input).attr('id');
            var $idParts = $id.split('-');
            var $l = $idParts.length;
            var $idValues = $idParts.splice(2, $l - 2);

            $($parent).after($field);
            var $attrVal = $($parent).parents('.row');
            console.log($input.dataset);
        }
        /*
        var $types = ['input','hidden','select','textarea','checkbox','radiobutton'];
        $.each($parents.find('.input-group').children(), function(i, child){
            var $tag = (child.tagName);
            if((jQuery.inArray($tag.toLowerCase(), $types) !== -1)){
                $input = child;
                return;
            }
        });
        var $id = $($input).attr('id');
        var $data = new FormData();
        $data.append($($input).attr('name'), $($input).val());
        $data.append('form',$($input.form).attr('id'));
        var $baseUrl = $input.baseURI;
        var $urlSlice = $baseUrl.split('/');
        $urlSlice[0] += '/';
        $urlSlice[($urlSlice.length - 1)] = 'getdynamicfield';
        var params = {};
        params.URL = $urlSlice.join('/');
        params.DATA = $data;
        params.DATATYPE = 'json';
        params.METHOD = 'POST';
        params.CACHE = false;
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.SUCCESS = function(response){
            $($parent.parentNode).after(response.field);
            $($input.form).yiiActiveForm('add', response.jsValidation);
        };
        params.ERROR = function(){};
        AjaxRequest(params);
         * 
         */
    };

    const getSelectedOption = ( select ) => {
        return select.options[select.selectedIndex].value
    }