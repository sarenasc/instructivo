document.addEventListener("DOMContentLoaded", () => {
  const formDetalle = document.getElementById("formDetalle");

  if (formDetalle) {
    formDetalle.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(formDetalle);
      const selectedCalibres = Array.from(document.getElementById("id_calibre").selectedOptions).map(option => option.value);

      if (selectedCalibres.length === 0) {
        alert("Debes seleccionar al menos un calibre.");
        return;
      }

      // Eliminar campo mÃºltiple para procesar uno a uno
      formData.delete("id_calibre[]");

      // Enviar una peticiÃ³n por cada calibre
      const requests = selectedCalibres.map(id_calibre => {
        const tempFormData = new FormData(formDetalle);
        tempFormData.append("id_calibre", id_calibre);

        return fetch("../controllers/procesar_detalle_instructivo.php", {
          method: "POST",
          body: tempFormData
        }).then(response => response.json());
      });
        const mensajeDiv = document.getElementById("mensajeDetalle");
      Promise.all(requests)
        .then(responses => {
          const errores = responses.filter(r => !r.success);
          if (errores.length > 0) {
            mensajeDiv.className ="alert alert-danger text-white bg-danger";
            mensajeDiv.textContent = "Error al guardar el detalle";
            mensajeDiv.classList.remove("d-none");
            setTimeout(() => {
            mensajeDiv.classList.add("d-none");
          }, 5000);
            console.error(errores);
          } else {
            mensajeDiv.className = "alert alert-success text-white bg-success";
            mensajeDiv.textContent = "Detalle Guadado con exito"
            mensajeDiv.classList.remove("d-none");
            setTimeout(() => {
            mensajeDiv.classList.add("d-none");
          }, 5000);
            //formDetalle.reset();
          }
        })
        .catch(error => {
          console.error("Error en la solicitud del detalle:", error);
        });
    });
  }
});


