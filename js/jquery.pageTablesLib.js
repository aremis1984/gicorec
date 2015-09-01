
/** funcion privada de la libreria, se utiliza para establecer si un determinado boton deberá ser o no valido */
$.fn.checkButtonsValidity = function(){
    //obtenemos la tabla asociada a este boton
    var table = $(this).data("relatedTable");
        
    //obtenemos el identificador de la página activa
    var pageActive = $("tbody", table).data("pageActive");
    
    if ( $(this).attr("id") == "prev" ){
        var prevButton = $(this);
        var nextButton = $(this).siblings("div.button");
    }
    else {
        var prevButton = $(this).siblings("div.button");
        var nextButton = $(this);
    }
        
    if (pageActive <= 1 ){
        //si el pageActive es 1 o inferior entonces el boton de ANTERIOR se desactiva
        prevButton.addClass("disabled");
    }
    else {
        prevButton.removeClass("disabled");
    }
    
    if (pageActive >= table.children("div").size() ){
        //si el pageActive es igual o superior al numero de hijos posibles
        nextButton.addClass("disabled");
    }
    else {
        nextButton.removeClass("disabled");
    }
}


/** funcion para inicializar el objeto, convirtiendo la tabla en una tabla de tipo por paginas. Solo es necesario
 * invocar la funcion sobre una tabla con mas de 15 hijos, sino no hace nada                                        */
$.fn.pageTable = function(itemsPerPage){
    itemsPerPage || ( itemsPerPage = 15 );
    
    var i=1;
    var name=1;

    if ( $("tbody", $(this) ).children().size() > itemsPerPage ){
        
        $(this).css({
            borderBottomWidth: "2px",
            borderBottomStyle: "solid",
            marginBottom: "10px"
        });
        
        //creo un div para almacenar (futuro) las primeras 15 celdas
        $(this).append("<div class='hiddenElmt page1'></div>");
        
        $("tbody", $(this)).data("pageActive", "1");
        
        var total = $("tbody", $(this) ).children().size();
        var parentTable = $(this);
        
        // agrupo las filas segun el numero total pasado por parmetro
        $("tbody", $(this) ).children("tr").each( function() {
            
            //cada X ocurrencias reinicio el contador y creo una nueva "pagina"
             if ( i > itemsPerPage){
                i=1;
                name++;
                //creo un nuevo div que contendrá las celdas de esta "pagina"
                $(parentTable).append("<div class='hiddenElmt page"+name+"'></div>");
            }  
            
            if ( name > 1 ){
                //debo mover la fila a su div correspondiente
                $(parentTable).children(".page"+name).append( $(this) );
            }
            i++;            
        });
        
        
        $(this).after("<div><div id=prev class='button tableNav inline_block'>Anterior</div><div id=next class='button tableNav inline_block'>Siguiente</div><span class='italic'>Total: "+total+"</span></div>");
        
        //asociamos cada boton con la tabla sobre la que actua
        $("div#prev:last, div#next:last").data("relatedTable", $(this)).each(function(){
            $(this).checkButtonsValidity();
            
            //asociamos listener a cada boton creado para que realice correctamente las acciones de "pasar pagina"
            if ( $(this).attr("id") == "next"){
                $(this).click(function(){
                    //solo procesamos l apeticion del click si NO esta desabilitado
                    if ( !$(this).hasClass("disabled") ){
                    
                        //obtenemos la tabla asociada a este boton
                        var table = $(this).data("relatedTable");
                        
                        //obtenemos las celdas actualemente activas
                        var target = $("tbody", table).data("pageActive");
                        
                        var childrens = $("tbody", table).children();
                                
                        //las movemos al div que les corresponde en base a su numeración de página
                        childrens.appendTo( table.children("div.page"+target) );
                        
                        target = parseInt(target)+1;
                        
                        //cargamos las siguientes celdas activas
                        table.children("div.page"+target).children().appendTo( $("tbody", table) );
                        
                        //actualizamos el body que indique que pagina es la activa
                        $("tbody", table).data("pageActive", target);
                        
                        $(this).checkButtonsValidity();
                    }
                });
            }
            else {
                $(this).click(function(){
                    //solo procesamos l apeticion del click si NO esta desabilitado
                    if ( !$(this).hasClass("disabled") ){
                        
                        //obtenemos la tabla asociada a este boton
                        var table = $(this).data("relatedTable");
                        
                        //obtenemos las celdas actualemente activas
                        var target = $("tbody", table).data("pageActive");
                        
                        var childrens = $("tbody", table).children();
                        
                        //las movemos al div que les corresponde en base a su numeración de página
                        childrens.appendTo( table.children("div.page"+target) );
                        
                        target = parseInt(target)-1;
                        
                        //cargamos las siguientes celdas activas
                        table.children("div.page"+target).children().appendTo( $("tbody", table) );
                        
                        //actualizamos el body que indique que pagina es la activa
                        $("tbody", table).data("pageActive", target);
                        
                        $(this).checkButtonsValidity();
                    }
                });
            }
        });
    }
    return $(this);
};