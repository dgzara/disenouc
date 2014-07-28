$(document).ready(function() {

    p_initialize();
    
    var header = $('.navbar');
    
    $(window).scroll(function () {
        if ($(this).scrollTop() > 0) {
            header.addClass("navbar-offset");
        } else {
            header.removeClass("navbar-offset");
        }
    });
    
});

function p_initialize(parentag)
{
    if(parentag == undefined)
        parentag = 'body';
    
    parentag += ' ';
    
    $( parentag+"a.close" ).each(function( index ) {
        
        $(this).click(function(){
            
            $(this).parent().remove();
            return false;
        });
    });
    
    $( parentag+"a.onbackground" ).each(function( index ) {
        
        $(this).click(function(){
            var url = $(this).attr('href');
            if(url!=undefined)
                p_getOnBackground(url);            
            return false;
        });
    });
    
    $( parentag+"a.loadmore" ).each(function( index ) {
        
        $(this).click(function(){
            var url = $(this).attr('href');
            if(url!=undefined)
                p_getMore(url,this);            
            return false;
        });
    });
    
    $( parentag+"a.onmodal" ).each(function( index ) {
        $(this).click(function(){
            var link = $(this).attr('href');
            p_getOnModal(link);
            return false;
        });
    });
    
    $( parentag+"a.submitform" ).each(function( index ) {
        $(this).click(function(){
            $($(this).attr('href')+' #_submit').click();
            
            return false;
        });
        
        var submitbtn = $(this);
        
        $('form'+$(this).attr('href')).submit(function(){
            var btntxt = submitbtn.text();
            submitbtn.text('Espere...');
            submitbtn.addClass('btn-warning');
            $(window).unload(function() {
                submitbtn.text(btntxt);
                submitbtn.removeClass('btn-warning');
            });
            //$(parentag+"a.submitform" ).replaceWith('<a class="btn btn-warning">Enviando...</a>');
            return true;
        });  
        
        
        
    });
    
    $( parentag+".date" ).each(function( index ) {
        $(this).datetimepicker({
          pickTime: false
        });
        
    });
    
    $( parentag+".time" ).each(function( index ) {
        $(this).datetimepicker({
          pickDate: false
        });
    });

    p_modal.Obj = $('#defaultModal');
    
    $(parentag+'.tooltip-append').each(function( index ) {
            $(this).tooltip();
    });
}

function p_checkBrowserAlert()
{
    var nVer = navigator.appVersion;
    var nAgt = navigator.userAgent;
    var browserName  = navigator.appName;
    var fullVersion  = ''+parseFloat(navigator.appVersion); 
    var majorVersion = parseInt(navigator.appVersion,10);
    var nameOffset,verOffset,ix;
    var requireUpdate = false;
    
    // In Opera, the true version is after "Opera" or after "Version"
    if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
        browserName = "Opera";
        fullVersion = nAgt.substring(verOffset+6);
        if ((verOffset=nAgt.indexOf("Version"))!=-1) 
            fullVersion = nAgt.substring(verOffset+8);
    }
    // In MSIE, the true version is after "MSIE" in userAgent
    else if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
        browserName = "Microsoft Internet Explorer";
        fullVersion = nAgt.substring(verOffset+5);
    }
    // In Chrome, the true version is after "Chrome" 
    else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
        browserName = "Chrome";
        fullVersion = nAgt.substring(verOffset+7);
    }
    // In Safari, the true version is after "Safari" or after "Version" 
    else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
        browserName = "Safari";
        fullVersion = nAgt.substring(verOffset+7);
        if ((verOffset=nAgt.indexOf("Version"))!=-1) 
            fullVersion = nAgt.substring(verOffset+8);
    }
    // In Firefox, the true version is after "Firefox" 
    else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
        browserName = "Firefox";
        fullVersion = nAgt.substring(verOffset+8);
    }
    // In most other browsers, "name/version" is at the end of userAgent 
    else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) < 
              (verOffset=nAgt.lastIndexOf('/')) ) 
    {
        browserName = nAgt.substring(nameOffset,verOffset);
        fullVersion = nAgt.substring(verOffset+1);
        if (browserName.toLowerCase()==browserName.toUpperCase()) {
            browserName = navigator.appName;
        }
    }
    // trim the fullVersion string at semicolon/space if present
    if ((ix=fullVersion.indexOf(";"))!=-1)
        fullVersion=fullVersion.substring(0,ix);
    if ((ix=fullVersion.indexOf(" "))!=-1)
        fullVersion=fullVersion.substring(0,ix);

    majorVersion = parseInt(''+fullVersion,10);
    if (isNaN(majorVersion)) {
        fullVersion  = ''+parseFloat(navigator.appVersion); 
        majorVersion = parseInt(navigator.appVersion,10);
    }
    
    // verificamos versiones minimas
    if(browserName=="Opera" && majorVersion<15)
        requireUpdate = true;
    else if(browserName=="Microsoft Internet Explorer" && majorVersion<10)
        requireUpdate = true;
    else if(browserName=="Chrome" && majorVersion<28)
        requireUpdate = true;
    else if(browserName=="Safari" && majorVersion<5)
        requireUpdate = true;
    else if(browserName=="Firefox" && majorVersion<22)
        requireUpdate = true;

    if(requireUpdate)
    {
        document.write("<div class='alert alert-default'><a class='close'>&times;</a><span class='glyphicon glyphicon-margin-right glyphicon-warning-sign' style='float:left;'></span><div style='margin-left: 25px;width: 560px;'><strong>Por favor actualice su navegador, puede experimentar problemas en la navegación.</strong></div></div>")
    }
}

var p_modal = new function() {
    this.Obj = $('#defaultModal');
    this.Try = function (args) {
        try
        {
            if(args!=undefined)
                this.Obj.modal(args);
            else
                this.Obj.modal();
        }
        catch(err)
        {
            return false;
        }
        
        return true;
    };
}

// oculta lo preexistente al interior del modal y le agrega contenido de Cargando
function p_loadingModal(mensaje)
{
    height = 120;
    if(mensaje==undefined)
    {
        mensaje = "";
        height = 100;
    }
    var kids = p_modal.Obj.children();
    var flag = false;
    jQuery.each(kids, function() {
        if(jQuery(this).attr('id')!="ModalLoadingDiv")
            jQuery(this).hide();
        else
        {
            flag = true;
            return true;
        }
        return true;
        });
    
    if(flag)
        $('#ModalLoadingDiv .modal-body h5').html(mensaje);
    else
    {
        var content = "<div id=\"ModalLoadingDiv\" class=\"modal-dialog\">";
        content += "<div class=\"modal-content\">";
        content += "<div class=\"modal-header\">";
        content += "</div>";
        content += "<div  id=\"ModalLoadingBody\" class=\"modal-body\" style=\"height:"+height+"px;text-align:center;\">";
        content += "<h5 style=\"position: relative;top: 100px;\">"+mensaje+"</h5>";
        content += "</div>";
        content += "<div class=\"modal-footer\"></div></div></div>";
        
        
        p_modal.Obj.append(content);
        
        var opts = {
          lines: 13, // The number of lines to draw
          length: 11, // The length of each line
          width: 4, // The line thickness
          radius: 25, // The radius of the inner circle
          corners: 0.7, // Corner roundness (0..1)
          rotate: 9, // The rotation offset
          color: '#FEBE10', // #rgb or #rrggbb
          speed: 1, // Rounds per second
          trail: 60, // Afterglow percentage
          shadow: false, // Whether to render a shadow
          hwaccel: false, // Whether to use hardware acceleration
          className: 'spinner', // The CSS class to assign to the spinner
          zIndex: 2e9, // The z-index (defaults to 2000000000)
          top: '10px', // Top position relative to parent in px
          left: '226px' // Left position relative to parent in px
        };
        var target = document.getElementById('ModalLoadingBody');
        var spinner = new Spinner(opts).spin(target);
        
    }
    
    if(!p_modal.Try('show'))
        alert(mensaje);

    
    return true;
}

function p_progressBar(valor,msj)
{
    var progressBar = $('#progressBar');
    if(progressBar != undefined)
    {
        var content = "<div class=\"progress progress-danger progress-striped active\">";
        content += "<div class=\"bar\" style=\"width: "+valor+"%\"></div></div>";
        content += "<div><h4>"+msj+"</h4></div>";
        
        progressBar.empty();
        progressBar.append(content);
        return true;
    }
    return false;
}


// quita lo agregado Cargando y hace visible todo el resto de los elementos
function p_destroyModal()
{
    p_modal.Obj.empty();
    p_modal.Try('hide');
}

function p_alertOnModal(msj,title)
{
    if(msj === undefined)
        msj = "";
    if(title === undefined)
        title = "Información";    

    var content = "<div id=\"ModalAlertDiv\" class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header\">";
    content += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>";
    content += "<h3 id=\"myModalLabel\">"+title+"</h3>";
    content += "</div>";
    content += "<div class=\"modal-body\">";
    content += "<p>"+msj+"</p>";
    content += "</div>";
    content += "<div class=\"modal-footer\">";
    content += "<button id=\"buttonaceptar\" class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">Aceptar</button>";
    content += "</div></div></div>";
    
    p_modal.Obj.empty();
    p_modal.Obj.append(content);
    
    if(!p_modal.Try('show'))
    {    
        alert(msj);
    }
    
    return true; 
}

function p_confirmOnModal(msj,siFunc,noFunc,title)
{
    if(msj === undefined)
        msj = "";
    
    if(title === undefined)
        title = "Confirmación";   

    var content = "<div id=\"ModalConfirmDiv\" class=\"modal-dialog\"><div class=\"modal-content\"><div class=\"modal-header\">";
    content += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">×</button>";
    content += "<h3 id=\"myModalLabel\">"+title+"</h3>";
    content += "</div>";
    content += "<div class=\"modal-body\">";
    content += "<p>"+msj+"</p>";
    content += "</div>";
    content += "<div class=\"modal-footer\">";
    content += "<button id=\"buttoncancelar\" class=\"btn\" data-dismiss=\"modal\" aria-hidden=\"true\">Cancelar</button>";
    content += "<button id=\"buttonaceptar\" class=\"btn btn-primary\">Aceptar</button>";
    content += "</div></div></div>";
    
    p_modal.Obj.empty();
    p_modal.Obj.append(content);
    
    if(!p_modal.Try('show'))
    {    
        var ans = confirm(msj);
        
        if (ans)
        {
            if(siFunc!=undefined)
                siFunc();
        }
        else
        {
            if(noFunc!=undefined)
               noFunc();
        }        
    }
    else
    {
    
        $("#buttoncancelar").click(function() {
            $('#ModalConfirmDiv').remove();
            p_modal.Try('hide');
            
            if(noFunc!=undefined)
                noFunc();
        });     
        
        $("#buttonaceptar").click(function() {
            $('#ModalConfirmDiv').remove();
            p_modal.Try('hide');
            if(siFunc!=undefined)
                siFunc();
        });
    }
   return true; 
}

function p_getOnModal(url, msj)
{
    if(msj === undefined)
        msj = "";

    p_modal.Obj.empty();
    p_loadingModal(msj);
    //p_modal.Try('show');
    
    if (url.indexOf("#") === 0)
    {
        p_modal.Obj.empty();
        p_modal.Obj.append($(url).html());
        p_modal.Try('show');
        p_initialize('#'+p_modal.Obj.attr("id"));
        return;
    }
    
    $.ajax({
        url: url,
        success: function(data){
           //p_modal.Try('hide');
           p_modal.Obj.empty();
           p_modal.Obj.append(data);
           p_modal.Try('show');
           p_initialize('#'+p_modal.Obj.attr("id"));
           p_formModal('#'+p_modal.Obj.attr("id"));           
        },
        error: function( jqXHR, textStatus,  errorThrown ){
            if(errorThrown==='Forbidden')
                p_alertOnModal("Usted no tiene permisos suficientes.",'Acceso restringido')
            else
                p_alertOnModal("Ha ocurrido un error: "+errorThrown,'Error')
        }
    });
}


function p_getOnBackground(url)
{
    $.ajax({
        url: url
    });

}

function p_formModal(parenttag)
{
    var $form = $(parenttag).find("form");
    $form.submit(function( event ) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function(data, textStatus, xhr) {
                if (data.redirect) {
                    // data.redirect contains the string URL to redirect to
                    window.location.href = data.redirect;
                }
                else {
                    // data.form contains the HTML for the replacement form
                    $(parenttag).html(data);
                    p_formModal(parenttag);
                }
            },
            error: function( jqXHR, textStatus,  errorThrown ){
                if(errorThrown==='Forbidden')
                    p_alertOnModal("Usted no tiene permisos suficientes.",'Acceso restringido')
                else
                    p_alertOnModal("Ha ocurrido un error: "+errorThrown,'Error')
            }
        });
    });
}


var p_test=1;
function p_getMore(url,obj)
{
    var container = $(obj).parent().parent();
    if(container != undefined)
    {
        $.ajax({
            url: url,
            success: function(data){
               //p_modal.Try('hide');
               container.replaceWith(data);
               
               $( "body a.loadmore" ).each(function( index ) {
        
                $(this).click(function(){
                    var url = $(this).attr('href');
                    if(url!=undefined)
                        p_getMore(url,this);            
                    return false;
                });
            });
            },
            error: function( jqXHR, textStatus,  errorThrown ){
                if(errorThrown==='Forbidden')
                    p_alertOnModal("Usted no tiene permisos suficientes.",'Acceso restringido')
                else
                    p_alertOnModal("Ha ocurrido un error: "+errorThrown,'Error')
            }
        });

        
        return true;
    }
    return false;
}

function p_setTypeahead(input_obj,source_url,fillable_base_id,minLengthVal)
{
    if(minLengthVal == undefined)
        minLengthVal = 1;
    input_obj.attr('autocomplete','off');
    input_obj.typeahead({
                   source: function (query, process) {
                           var items = [];
                           map = {};

                           return $.getJSON(source_url, { query: query }, function (data) {
                                   $.each(data, function (i, item) {
                                           map[item.label] = item;
                                           items.push(item.label);
                                   });
                                   return process(items);
                           });
                   },
                   updater: function (item) {
                           selectedUser = map[item].value;
                           if(fillable_base_id != undefined){
                               $.each(map[item], function (i, item) {
                                   $('#'+fillable_base_id+i).val(item);

                               });
                           }
                           return selectedUser;
                   },
                   highlighter: function (item) {
                           return map[item].label;
                   },
                   matcher: function (item) {
                           return true;
                   },
                   minLength: minLengthVal,
           }); 
}

/*
 * 
 * Loading Animation
 * 
 */
 
 
    
    //fgnass.github.com/spin.js#v1.2.7
    !function(window, document, undefined) {

      /**
       * Copyright (c) 2011 Felix Gnass [fgnass at neteye dot de]
       * Licensed under the MIT license
       */

      var prefixes = ['webkit', 'Moz', 'ms', 'O'] /* Vendor prefixes */
        , animations = {} /* Animation rules keyed by their name */
        , useCssAnimations

      /**
       * Utility function to create elements. If no tag name is given,
       * a DIV is created. Optionally properties can be passed.
       */
      function createEl(tag, prop) {
        var el = document.createElement(tag || 'div')
          , n

        for(n in prop) el[n] = prop[n]
        return el
      }

      /**
       * Appends children and returns the parent.
       */
      function ins(parent /* child1, child2, ...*/) {
        for (var i=1, n=arguments.length; i<n; i++)
          parent.appendChild(arguments[i])

        return parent
      }

      /**
       * Insert a new stylesheet to hold the @keyframe or VML rules.
       */
      var sheet = function() {
        var el = createEl('style', {type : 'text/css'})
        ins(document.getElementsByTagName('head')[0], el)
        return el.sheet || el.styleSheet
      }()

      /**
       * Creates an opacity keyframe animation rule and returns its name.
       * Since most mobile Webkits have timing issues with animation-delay,
       * we create separate rules for each line/segment.
       */
      function addAnimation(alpha, trail, i, lines) {
        var name = ['opacity', trail, ~~(alpha*100), i, lines].join('-')
          , start = 0.01 + i/lines*100
          , z = Math.max(1 - (1-alpha) / trail * (100-start), alpha)
          , prefix = useCssAnimations.substring(0, useCssAnimations.indexOf('Animation')).toLowerCase()
          , pre = prefix && '-'+prefix+'-' || ''

        if (!animations[name]) {
          sheet.insertRule(
            '@' + pre + 'keyframes ' + name + '{' +
            '0%{opacity:' + z + '}' +
            start + '%{opacity:' + alpha + '}' +
            (start+0.01) + '%{opacity:1}' +
            (start+trail) % 100 + '%{opacity:' + alpha + '}' +
            '100%{opacity:' + z + '}' +
            '}', sheet.cssRules.length)

          animations[name] = 1
        }
        return name
      }

      /**
       * Tries various vendor prefixes and returns the first supported property.
       **/
      function vendor(el, prop) {
        var s = el.style
          , pp
          , i

        if(s[prop] !== undefined) return prop
        prop = prop.charAt(0).toUpperCase() + prop.slice(1)
        for(i=0; i<prefixes.length; i++) {
          pp = prefixes[i]+prop
          if(s[pp] !== undefined) return pp
        }
      }

      /**
       * Sets multiple style properties at once.
       */
      function css(el, prop) {
        for (var n in prop)
          el.style[vendor(el, n)||n] = prop[n]

        return el
      }

      /**
       * Fills in default values.
       */
      function merge(obj) {
        for (var i=1; i < arguments.length; i++) {
          var def = arguments[i]
          for (var n in def)
            if (obj[n] === undefined) obj[n] = def[n]
        }
        return obj
      }

      /**
       * Returns the absolute page-offset of the given element.
       */
      function pos(el) {
        var o = { x:el.offsetLeft, y:el.offsetTop }
        while((el = el.offsetParent))
          o.x+=el.offsetLeft, o.y+=el.offsetTop

        return o
      }

      var defaults = {
        lines: 12,            // The number of lines to draw
        length: 7,            // The length of each line
        width: 5,             // The line thickness
        radius: 10,           // The radius of the inner circle
        rotate: 0,            // Rotation offset
        corners: 1,           // Roundness (0..1)
        color: '#000',        // #rgb or #rrggbb
        speed: 1,             // Rounds per second
        trail: 100,           // Afterglow percentage
        opacity: 1/4,         // Opacity of the lines
        fps: 20,              // Frames per second when using setTimeout()
        zIndex: 2e9,          // Use a high z-index by default
        className: 'spinner', // CSS class to assign to the element
        top: 'auto',          // center vertically
        left: 'auto',         // center horizontally
        position: 'relative'  // element position
      }

      /** The constructor */
      var Spinner = function Spinner(o) {
        if (!this.spin) return new Spinner(o)
        this.opts = merge(o || {}, Spinner.defaults, defaults)
      }

      Spinner.defaults = {}

      merge(Spinner.prototype, {
        spin: function(target) {
          this.stop()
          var self = this
            , o = self.opts
            , el = self.el = css(createEl(0, {className: o.className}), {position: o.position, width: 0, zIndex: o.zIndex})
            , mid = o.radius+o.length+o.width
            , ep // element position
            , tp // target position

          if (target) {
            target.insertBefore(el, target.firstChild||null)
            tp = pos(target)
            ep = pos(el)
            css(el, {
              left: (o.left == 'auto' ? tp.x-ep.x + (target.offsetWidth >> 1) : parseInt(o.left, 10) + mid) + 'px',
              top: (o.top == 'auto' ? tp.y-ep.y + (target.offsetHeight >> 1) : parseInt(o.top, 10) + mid)  + 'px'
            })
          }

          el.setAttribute('aria-role', 'progressbar')
          self.lines(el, self.opts)

          if (!useCssAnimations) {
            // No CSS animation support, use setTimeout() instead
            var i = 0
              , fps = o.fps
              , f = fps/o.speed
              , ostep = (1-o.opacity) / (f*o.trail / 100)
              , astep = f/o.lines

            ;(function anim() {
              i++;
              for (var s=o.lines; s; s--) {
                var alpha = Math.max(1-(i+s*astep)%f * ostep, o.opacity)
                self.opacity(el, o.lines-s, alpha, o)
              }
              self.timeout = self.el && setTimeout(anim, ~~(1000/fps))
            })()
          }
          return self
        },

        stop: function() {
          var el = this.el
          if (el) {
            clearTimeout(this.timeout)
            if (el.parentNode) el.parentNode.removeChild(el)
            this.el = undefined
          }
          return this
        },

        lines: function(el, o) {
          var i = 0
            , seg

          function fill(color, shadow) {
            return css(createEl(), {
              position: 'absolute',
              width: (o.length+o.width) + 'px',
              height: o.width + 'px',
              background: color,
              boxShadow: shadow,
              transformOrigin: 'left',
              transform: 'rotate(' + ~~(360/o.lines*i+o.rotate) + 'deg) translate(' + o.radius+'px' +',0)',
              borderRadius: (o.corners * o.width>>1) + 'px'
            })
          }

          for (; i < o.lines; i++) {
            seg = css(createEl(), {
              position: 'absolute',
              top: 1+~(o.width/2) + 'px',
              transform: o.hwaccel ? 'translate3d(0,0,0)' : '',
              opacity: o.opacity,
              animation: useCssAnimations && addAnimation(o.opacity, o.trail, i, o.lines) + ' ' + 1/o.speed + 's linear infinite'
            })

            if (o.shadow) ins(seg, css(fill('#000', '0 0 4px ' + '#000'), {top: 2+'px'}))

            ins(el, ins(seg, fill(o.color, '0 0 1px rgba(0,0,0,.1)')))
          }
          return el
        },

        opacity: function(el, i, val) {
          if (i < el.childNodes.length) el.childNodes[i].style.opacity = val
        }

      })

      /////////////////////////////////////////////////////////////////////////
      // VML rendering for IE
      /////////////////////////////////////////////////////////////////////////

      /**
       * Check and init VML support
       */
      ;(function() {

        function vml(tag, attr) {
          return createEl('<' + tag + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', attr)
        }

        var s = css(createEl('group'), {behavior: 'url(#default#VML)'})

        if (!vendor(s, 'transform') && s.adj) {

          // VML support detected. Insert CSS rule ...
          sheet.addRule('.spin-vml', 'behavior:url(#default#VML)')

          Spinner.prototype.lines = function(el, o) {
            var r = o.length+o.width
              , s = 2*r

            function grp() {
              return css(
                vml('group', {
                  coordsize: s + ' ' + s,
                  coordorigin: -r + ' ' + -r
                }),
                { width: s, height: s }
              )
            }

            var margin = -(o.width+o.length)*2 + 'px'
              , g = css(grp(), {position: 'absolute', top: margin, left: margin})
              , i

            function seg(i, dx, filter) {
              ins(g,
                ins(css(grp(), {rotation: 360 / o.lines * i + 'deg', left: ~~dx}),
                  ins(css(vml('roundrect', {arcsize: o.corners}), {
                      width: r,
                      height: o.width,
                      left: o.radius,
                      top: -o.width>>1,
                      filter: filter
                    }),
                    vml('fill', {color: o.color, opacity: o.opacity}),
                    vml('stroke', {opacity: 0}) // transparent stroke to fix color bleeding upon opacity change
                  )
                )
              )
            }

            if (o.shadow)
              for (i = 1; i <= o.lines; i++)
                seg(i, -2, 'progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)')

            for (i = 1; i <= o.lines; i++) seg(i)
            return ins(el, g)
          }

          Spinner.prototype.opacity = function(el, i, val, o) {
            var c = el.firstChild
            o = o.shadow && o.lines || 0
            if (c && i+o < c.childNodes.length) {
              c = c.childNodes[i+o]; c = c && c.firstChild; c = c && c.firstChild
              if (c) c.opacity = val
            }
          }
        }
        else
          useCssAnimations = vendor(s, 'animation')
      })()

      if (typeof define == 'function' && define.amd)
        define(function() { return Spinner })
      else
        window.Spinner = Spinner

    }(window, document);
