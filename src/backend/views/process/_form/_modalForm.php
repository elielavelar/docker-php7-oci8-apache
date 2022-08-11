<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="modal fade in" id="modal-detail" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-md" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Proceso <div class="inline" id="OrderQuestion"></div></strong></h3>
            </div>
            <div class="modal-body">
                <?=$this->render('_formDetail', ['model'=>$model])?>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12">
                        <span class="float-right">
                            <button type="button" id="btnSaveDetail" name="btnSaveDetail" class="btn btn-success">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <button type="button" id="btnCancelDetail" name="btnCancelDetail" class="btn btn-danger">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>