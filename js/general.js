$(document).ready(function() {
//para crear el formulario de añadir desplegable 
    $(".add_button").click(function() {
        $(".add_form2").slideUp(function() {
            $(".add_form").finish().slideToggle();
        });
    });
//la existencia de esta función para diferentes clases permite que en páginas donde usamos varios formularios
//para la insersión como en alamcen, si abrimos uno y accionamos el botón del otro, se muestre el efecto de 
//cerrado del anterior y despliegue del nuevo
    $(".add_button2").click(function() {
        $(".add_form").slideUp(function() {
            $(".add_form2").finish().slideToggle();
        });
    });
//para formularios desplegables en secciones donde no usamos sino uno
    $(".add_button3").click(function() {
        $(".add_form3").finish().slideToggle();
    });
//mensaje de confirmación de acción
    $(".mensaje_confirm").dialog();
//para paginar las tablas, esto ocurrirá cuando pasen de 15 elementos y así evitamos que las páginas se muestren muy grandes
    $("table").each(function() {
        $(this).pageTable(15);
    });
//para mostrar el widget de calendario con el formato de fecha española
    $.datepicker.setDefaults($.datepicker.regional[ "es" ]);

    $("input.date").datepicker();

    //para gestionar que los formularios que requieran que se ponga la misma contraseña sea asi
    $(".claveRepetida").change(function() {
        var parent = $(this).parents("form");
        var value = $(this).val();
        var obj = $(this).get(0);

        $(".claveRepetida", parent).each(function() {
            if ($(this).get(0) != obj) {

                if ($(this).val() != "") {

                    if ($(this).val() != value) {
                        $(".claveRepetida", parent).addClass("error");
                    }
                    else {
                        $(".claveRepetida", parent).removeClass("error");
                    }
                }


            }
        });
    });

    //controlamos que no se haga submit de un formulario que tenga entre sus hijos algun elemento marcado como error
    $("form.validate").submit(function(event) {
        if ($(".error", $(this)).size() > 0) {
            event.preventDefault();
        }
    });
});



