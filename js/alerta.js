document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const modal = document.getElementById("modal-notificacion");
    const modalTitulo = document.getElementById("modal-titulo");
    const modalMensaje = document.getElementById("modal-mensaje");
    const modalIcono = document.getElementById("modal-icono");
    const btnCerrar = document.getElementById("btn-cerrar-modal");

    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault(); // Evita recargar la página

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData
                });

                // Leemos texto plano primero para evitar que falle silenciosamente
                const textoBruto = await response.text();
                let data;

                try {
                    data = JSON.parse(textoBruto);
                } catch (errJson) {
                    console.error("Respuesta de PHP no es JSON válido:", textoBruto);
                    mostrarModal("Error PHP", "El servidor devolvió un error. Abre la consola (F12) para verlo.", "❌");
                    return;
                }

                if (data.status === "error") {
                    mostrarModal("¡Ups!", data.message, "❌");
                } else if (data.status === "success") {
                    mostrarModal("¡Bienvenido!", data.message, "🎉");
                    
                    const destino = data.redirect ? data.redirect : "login.html";
                    
                    setTimeout(() => {
                        window.location.href = destino;
                    }, 1800);
                }
            } catch (error) {
                console.error("Error de red o JS:", error);
                mostrarModal("Error de conexión", "Ocurrió un problema de servidor. Intenta de nuevo.", "⚠️");
            }
        });
    }

    function mostrarModal(titulo, mensaje, icono) {
        if (modal) {
            modalTitulo.textContent = titulo;
            modalMensaje.textContent = mensaje;
            modalIcono.textContent = icono;
            modal.classList.add("activo");
        }
    }

    if (btnCerrar) {
        btnCerrar.addEventListener("click", () => {
            modal.classList.remove("activo");
        });
    }
});