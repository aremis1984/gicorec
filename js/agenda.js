
$(document).ready(function() {
    //función para la creación de tablas separadas por pestañas
    $(".tabs").tabs({
        collapsible: true
    });



    //para el ajax de seleccionar mascota
    $("#select_mascota").autocomplete({
        minLength: 2,
        source: "/gicorec/index.php?ajax=agenda_getmascotas&dni=" + $("#dni").val(),
        select: function(event, ui) {    //funcion que se activa cuando seleccionamos
            ponerDetallesDeMascota(ui.item.value);
            event.preventDefault();
        },
        focus: function(event, ui) {  //funcion que se activa cuando pasamos el raton por encima
            ponerDetallesDeMascota(ui.item.value);
        }
    });


    //para el ajax de seleccionar horas disponibles para la fecha seleccionada
    $(".selecthora").change(function() {
        var fecha = $("#fecha_cita").val();
        var vet = $("#veterinario option:selected").val();

        console.log(vet);
        if (fecha != "" && (vet != 0 || vet != "0")) {

            $("#horas").children().remove();
            $.get("/gicorec/index.php?ajax=cita_gethoraslibres&fecha=" + fecha + "&veterinario=" + vet, function(data) {
                $("#horas").append(data);
            });
        }
        else {
            $("#horas").children().remove();
        }
    });

    $("#no_click").click(function() {
        return false;
    });

    // autocomplete para solicitar los productos
    $("#productosAdd").autocomplete({
        minLength: 2,
        source: "/gicorec/index.php?ajax=cita_getproductos",
        select: function(event, ui) {
            //funcion para gestionar la selección del objeto
            var datos = JSON.parse(ui.item.value);

            var newChild = "<div class='producto'>";
            newChild += "<input type='text' name='producto[" + datos.referencia + "][ref]' value='" + datos.referencia + "'>";
            newChild += "<input type='text' name='producto[" + datos.referencia + "][nombre]' value='" + datos.nombre + "' disabled>";
            newChild += "<span class='hidden costeBase'>" + datos.importe + "</span>";
            newChild += "<input type='text' name='producto[" + datos.referencia + "][cantidad]' class='productoCantidad'>";
            newChild += "<input type='text' name='producto[" + datos.referencia + "][importe]' disabled>";
            newChild += "<span class='cantidadTotal'> (stock: " + datos.cantidad + ")</span>";
            newChild += "<span class='button eliminarProducto'>Eliminar</span>";
            newChild += "</div>";

            $("#recetasWrapper").append(newChild);
            event.preventDefault();
        },
        focus: function(event, ui) {
            //funcion para gestionar que pasa cuando se pone el raton sobre el elemento
        }
    });

    // un listener para cuando cambia la cantida modificar el coste asociado a ese producto
    $("form").on("change", "input.productoCantidad", function() {

        var costeBase = parseFloat($(this).siblings(".costeBase").text().replace(",", "."));


        ////////////////////////////////////////////////////////////////////////
        /////////         ESTA PENDIENTE SABER COMO SE DEFINE EL PRECIO      ///
        ////////////////////////////////////////////////////////////////////////

        var coste = parseInt($(this).val()) * costeBase;
        $(this).next("input").val(coste.toFixed(2));

    });

    //handler para eliminar un producto previamente seleccionado
    $("body").on("click", "span.eliminarProducto", function() {
        $(this).parent("div").fadeOut(function() {
            $(this).remove();
        });
    });

    //handler para eliminar las clases disabled en el submit
    $("#productosAdd").parents("form").submit(function() {
        $("input:disabled", $(this)).removeAttr("disabled");
        return true;
    });
});




/** funcion para establecer los detalles de la mascota cuando se selecciona o hace hover sobre una mascota
 * del listado que viene de autocomplete */
function ponerDetallesDeMascota(value) {
    var datos = JSON.parse(value);

    $("#id_mascota").val(datos.id_pac);
    $("#select_mascota").val(datos.nombre_pac);
    $("#dni").val(datos.dni_propietario);
    $("#propietario").val(datos.nombre);
    $("#raza").val(datos.raza);
    $("#historia").val(datos.historia);
    $("#telefono").val(datos.telefono);
}