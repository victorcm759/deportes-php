document.addEventListener("DOMContentLoaded", function () {
    const inputBusqueda = document.getElementById("busqueda");
    const contenedor = document.getElementById("sugerencias");

    inputBusqueda.addEventListener("input", function () {
        const query = this.value;

        if (query.length < 2) {
            contenedor.innerHTML = "";
            return;
        }

        fetch("sugerencias.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                contenedor.innerHTML = "";
                data.forEach(sugerencia => {
                    const div = document.createElement("div");
                    div.textContent = sugerencia;
                    div.addEventListener("click", () => {
                        inputBusqueda.value = sugerencia;
                        contenedor.innerHTML = "";
                    });
                    contenedor.appendChild(div);
                });
            });
    });
});
document.getElementById("limpiar-filtros").addEventListener("click", function () {
    const input = document.getElementById("busqueda");
    const sugerencias = document.getElementById("sugerencias");

    input.value = "";
    sugerencias.innerHTML = "";

    // Si quieres que también se recargue la tabla sin filtros:
    if (window.location.search.includes("busqueda=")) {
        window.location.href = window.location.pathname; // recarga limpia
    }
});

document.getElementById("toggle-filtros").addEventListener("click", function () {
  const filtros = document.getElementById("contenedor-filtros");
  filtros.classList.toggle("oculto");
});

document.querySelector("#contenedor-filtros form").addEventListener("submit", function () {
  document.getElementById("contenedor-filtros").classList.add("oculto");
});
