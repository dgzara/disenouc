/*
 * 
 * pedroare
 * submit task : rutina de envio de formularios y urls con modal
 * logica LIFO
 * .pushForm(formid,mensaje) : agrega formulario a la cola
 * .pushUrl(url,mensaje) : agrega url a la cola
 * .redirectUrl : url de redireccion al finalizar
 * .status : boolean estado de la tarea
 * .statusMsj : mensaje estado de la tarea
 * .onComplete : define function(){} para ejecutar al finalizar
 * .begin() : comienza la subrutina
 *
 */
 
 var p_submitTask = (function() {
        
        // constructor
        function p_submitTask(){
            this._queue = new Array();
            this.status = false;
            this.statusMsj = "";
            this.onComplete = false;
            this.redirectUrl = false;
            this.redirectMsj = false;
            this._queueLength = 0;
            this._queuePosition = 0;
            this._paginaActual = 1;
        };

        p_submitTask.prototype._continue = function() {
            if(this.status == true)
            {
                this._queueLength = $(this._queue).size();
                
                this._submit();
            }
            else
            {
                p_alertOnModal("Ha ocurrido un error: "+this.statusMsj);
            }
        };
        
        p_submitTask.prototype.begin = function() {
            this._queueLength = $(this._queue).size();
            this._submit();
        };
        
        p_submitTask.prototype._percent = function(instance) {
            var subval = instance._paginaActual / 10;
            var count = 2; 
            while(subval>1)
            {
                subval = instance._paginaActual / (10*count++);
            }
            var val = parseInt("" + 100 * ((instance._queuePosition + subval) / (instance._queueLength+instance._queuePosition)));
            if(val>99)
                val=99;
            //alert(val);
            return val;
        };
        
        p_submitTask.prototype.pushForm = function(formid,mensaje) {
            var temp = { form: '#'+formid, msj: mensaje };
            this._queue.push(temp);
        };
        
        p_submitTask.prototype.pushUrl = function(newurl,mensaje) {
            var temp = { url: newurl, msj: mensaje };
            this._queue.push(temp);
        };
        
        p_submitTask.prototype.pushUrlPagina = function(newurl,mensaje) {
            var temp = { urlPagina: newurl, msj: mensaje };
            this._queue.push(temp);
        };
        
        p_submitTask.prototype._submit = function() {
            
            var item = this._queue.pop();
            var instance = this;
            
            if(item != undefined)
            {
                if(item.msj != undefined)
                {
                    var percent = instance._percent(instance);
                    p_progressBar(percent,item.msj+" ("+percent+"%)");
                }
                
                if(item.form != undefined)
                {
                    $.ajax({                        
                        complete: function() { 
                            setTimeout(function(){instance._continue()},1000);
                        },
                        url: $(item.form).attr("action"),
                        type: "POST",
                        data: $(item.form).serialize(),
                        success: function(data){
                            instance.status = false;
                            if(data.status == "ok")
                            {
                                instance.status = true;
                            }
                            else
                            {
                                instance.statusMsj = data;
                            }
                        }
                    });
                }
                else if(item.url != undefined)
                {
                    $.ajax({
                        complete: function() { 
                            setTimeout(function(){instance._continue()},1000);
                        },
                        url: item.url,
                        type: "POST",
                        success: function(data){
                            instance.status = false;
                            if(data.status == "ok")
                            {
                                instance.status = true;                            
                            }
                            else
                            {
                                instance.statusMsj = data;
                            }
                        }
                    });
                }
                else if(item.urlPagina != undefined)
                {
                    $.ajax({
                        complete: function() { 
                            setTimeout(function(){instance._continue()},1000);
                        },
                        url: item.urlPagina.replace('pagina',instance._paginaActual),
                        type: "POST",
                        error: function(jqXHR, textStatus, errorThrown ){
                            instance.status = true;
                            instance.statusMsj = textStatus+" : "+errorThrown;
                            instance.pushUrlPagina(item.urlPagina,item.msj+" (Reintentando)");
                            setTimeout(function(){instance.begin()},10000);
                            
                            },
                        success: function(data){
                            //alert(data.status+"::"+data.msg);
                            instance.status = false;
                            if(data.status == "ok")
                            {
                                instance.status = true;
                                instance._queuePosition++;
                            }
                            if(data.status == "ok:pagina")
                            {
                                instance._paginaActual++;
                                //var newurl = item.urlPagina.replace('pagina',instance._paginaActual);
                                instance.pushUrlPagina(item.urlPagina,item.msj);
                                instance.status = true;
                            }
                            if(data.status == "ok:finalizado")
                            {
                                instance._paginaActual = 1;
                                instance._queuePosition++;
                                instance.status = true;
                            }
                            else if(data.status == "error")
                            {
                                instance.statusMsj = data.msg;
                                instance.pushUrlPagina(item.urlPagina,item.msj+" (reintento)");
                            }
                            else
                            {
                                instance.statusMsj = data;
                            }
                        }
                    });
                }
            }
            else
            {
                p_progressBar(100,'Sincronización Finalizada.');
                
                if(this.onComplete != false)
                    this.onComplete();
                    
                if(this.redirectUrl != false)
                {
                    $(location).attr('href',this.redirectUrl);
                    
                    if(this.redirectMsj != false)
                        p_alertOnModal(this.redirectMsj);
                }                
            }
            
        };

        return p_submitTask;
    })();
