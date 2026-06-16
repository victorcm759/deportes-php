document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const savedTheme = localStorage.getItem("theme") || "light";
    setTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            const nextTheme = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
            setTheme(nextTheme);
            localStorage.setItem("theme", nextTheme);
        });
    }

    const inputBusqueda = document.getElementById("busqueda");
    const contenedor = document.getElementById("sugerencias");

    if (inputBusqueda && contenedor) {
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
    }

    const limpiar = document.getElementById("limpiar-filtros");
    if (limpiar) {
        limpiar.addEventListener("click", function () {
            const busqueda = document.getElementById("busqueda");
            const sugerencias = document.getElementById("sugerencias");

            if (busqueda) {
                busqueda.value = "";
            }
            if (sugerencias) {
                sugerencias.innerHTML = "";
            }

            if (window.location.search.includes("busqueda=")) {
                window.location.href = window.location.pathname;
            }
        });
    }

    const toggleFiltros = document.getElementById("toggle-filtros");
    if (toggleFiltros) {
        toggleFiltros.addEventListener("click", function () {
            const filtros = document.getElementById("contenedor-filtros");
            if (filtros) {
                filtros.classList.toggle("oculto");
            }
        });
    }

    const filtrosForm = document.querySelector("#contenedor-filtros form");
    if (filtrosForm) {
        filtrosForm.addEventListener("submit", function () {
            const filtros = document.getElementById("contenedor-filtros");
            if (filtros) {
                filtros.classList.add("oculto");
            }
        });
    }
});

function setTheme(theme) {
    if (theme === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
    } else {
        document.documentElement.removeAttribute("data-theme");
    }
    const button = document.getElementById("theme-toggle");
    if (button) {
        button.textContent = theme === "dark" ? "Modo claro" : "Modo oscuro";
    }
}
