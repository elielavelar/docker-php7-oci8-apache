/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(function ($) {
Highcharts.setOptions([]); new Highcharts.Chart({"chart":{"renderTo":"signup-citizen", "type":"column"}, "title":{"text":"Reporte de Registro de Ciudadanos"}, "xAxis":{"categories":["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]}, "yAxis":{"title":{"text":"Ciudadanos Registrados"}, "allowDecimals":false}, "legend":{"reserved":true}, "plotOptions":{"series":{"stacking":"normal"}}, "series":[{"name":"Aplicación en Línea", "data":[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]}, {"name":"Call Center", "data":[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]}, {"name":"No Definida", "data":[0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]}], "credits":{"text":"Muehlbauer ID Services"}});
        Highcharts.setOptions([]); new Highcharts.Chart({"chart":{"renderTo":"appointment-service", "type":"column", "height":600}, "title":{"text":"Citas de Ciudadanos por Duicentro"}, "xAxis":{"categories":["SOYAPANGO", "APOPA", "SAN MARCOS", "SENSUNTEPEQUE", "SAN VICENTE", "CHALATENANGO", "COJUTEPEQUE", "SANTA ANA", "AHUACHAPAN", "SANTA TECLA", "LOURDES COLON", "SAN MIGUEL", "SAN FRANCISCO GOTERA", "USULUTAN", "LA UNION", "SANTIAGO DE MARIA", "GALERIAS", "SAN SALVADOR", "ZACATECOLUCA", "SONSONATE"], "labels":{"rotation": - 45, "style":{"fontSize":"10px"}}}, "yAxis":{"title":{"text":"Citas Ciudadanos"}, "allowDecimals":false}, "legend":{"enabled":false}, "tooltip":{"headerFormat":"<span style=\"font-size:11px\">{series.name}</span><br>", "pointFormat":"<span style=\"color:{point.color}\">{point.name}</span>: <b>{point.y:.0f}</b><br/>"}, "plotOptions":{"series":{"borderWidth":0, "dataLabels":{"enabled":true, "format":"{point.y:.0f}"}}}, "series":[{"name":"Citas", "colorByPoint":true, "data":[{"name":"SOYAPANGO", "y":0, "drilldown":"SOYAPANGO"}, {"name":"APOPA", "y":0, "drilldown":"APOPA"}, {"name":"SAN MARCOS", "y":0, "drilldown":"SAN MARCOS"}, {"name":"SENSUNTEPEQUE", "y":0, "drilldown":"SENSUNTEPEQUE"}, {"name":"SAN VICENTE", "y":0, "drilldown":"SAN VICENTE"}, {"name":"CHALATENANGO", "y":0, "drilldown":"CHALATENANGO"}, {"name":"COJUTEPEQUE", "y":0, "drilldown":"COJUTEPEQUE"}, {"name":"SANTA ANA", "y":0, "drilldown":"SANTA ANA"}, {"name":"AHUACHAPAN", "y":0, "drilldown":"AHUACHAPAN"}, {"name":"SANTA TECLA", "y":0, "drilldown":"SANTA TECLA"}, {"name":"LOURDES COLON", "y":0, "drilldown":"LOURDES COLON"}, {"name":"SAN MIGUEL", "y":0, "drilldown":"SAN MIGUEL"}, {"name":"SAN FRANCISCO GOTERA", "y":0, "drilldown":"SAN FRANCISCO GOTERA"}, {"name":"USULUTAN", "y":0, "drilldown":"USULUTAN"}, {"name":"LA UNION", "y":0, "drilldown":"LA UNION"}, {"name":"SANTIAGO DE MARIA", "y":0, "drilldown":"SANTIAGO DE MARIA"}, {"name":"GALERIAS", "y":0, "drilldown":"GALERIAS"}, {"name":"SAN SALVADOR", "y":0, "drilldown":"SAN SALVADOR"}, {"name":"ZACATECOLUCA", "y":0, "drilldown":"ZACATECOLUCA"}, {"name":"SONSONATE", "y":0, "drilldown":"SONSONATE"}]}]
            , "drilldown":{
                "series":[
                    {"name":"SOYAPANGO", "id":"SOYAPANGO", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"APOPA", "id":"APOPA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"SAN MARCOS", "id":"SAN MARCOS", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"SENSUNTEPEQUE", "id":"SENSUNTEPEQUE", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SAN VICENTE", "id":"SAN VICENTE", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"CHALATENANGO", "id":"CHALATENANGO", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"COJUTEPEQUE", "id":"COJUTEPEQUE", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SANTA ANA", "id":"SANTA ANA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"AHUACHAPAN", "id":"AHUACHAPAN", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SANTA TECLA", "id":"SANTA TECLA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"LOURDES COLON", "id":"LOURDES COLON", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SAN MIGUEL", "id":"SAN MIGUEL", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SAN FRANCISCO GOTERA", "id":"SAN FRANCISCO GOTERA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"USULUTAN", "id":"USULUTAN", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"LA UNION", "id":"LA UNION", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}, {"name":"SANTIAGO DE MARIA", "id":"SANTIAGO DE MARIA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"GALERIAS", "id":"GALERIAS", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"SAN SALVADOR", "id":"SAN SALVADOR", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"ZACATECOLUCA", "id":"ZACATECOLUCA", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}
                    , {"name":"SONSONATE", "id":"SONSONATE", "data":[{"Primera Vez":0}, {"Modificación":0}, {"Reposición":0}, {"Renovación":0}]}]}
            , "credits":{"text":"Muehlbauer ID Services"}});
        var getData = function(){
        getDataByMonth();
                getDataByCentre();
        };
        var getDataByMonth = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
                var data = {'AppointmentDate': AppointmentDate};
                var params = {};
                params.URL = "/muhlbauer_new/backend/web/reports/getdatabymonth";
                params.DATA = {'data':JSON.stringify(data)};
                params.DATATYPE = 'json';
                params.METHOD = 'POST';
                params.SUCCESS = function(data){
                var dataValues = data.data;
                        var chart = $("#signup-citizen").highcharts();
                        chart.update({
                        series: dataValues
                        });
                };
                params.ERROR = function(data){
                console.log(data);
                };
                AjaxRequest(params);
        };
        var getDataByCentre = function(){
        var AppointmentDate = $("#AppointmentDate option:selected").val();
                var data = {'AppointmentDate': AppointmentDate};
                var params = {};
                params.URL = "/muhlbauer_new/backend/web/reports/getdatabycentre";
                params.DATA = {'data':JSON.stringify(data)};
                params.DATATYPE = 'json';
                params.METHOD = 'POST';
                params.SUCCESS = function(data){
                var values = data.data;
                        var dataValues = values.dataset;
                        var drillDown = values.drilldown;
                        var chart = $("#appointment-service").highcharts();
                        chart.update({
                        series: {data: dataValues}
                        , drilldown: drillDown
                        });
                };
                params.ERROR = function(data){
                console.log(data);
                };
                AjaxRequest(params);
        };
        $(document).ready(function(){
getData();
        $("#AppointmentDate").on('change', function(){
getData();
});
});
        });