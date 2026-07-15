document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menu-toggle");
    const menuLateral = document.getElementById("menu-lateral");
    const menuFondo = document.getElementById("menu-fondo");
    const menuCerrar = document.getElementById("menu-cerrar");

    function abrirMenu() {
        menuLateral.classList.add("abierto");
        menuFondo.classList.add("visible");
        menuToggle.classList.add("oculto");
        menuToggle.setAttribute("aria-expanded", "true");
        menuLateral.setAttribute("aria-hidden", "false");
    }

    function cerrarMenu() {
        menuLateral.classList.remove("abierto");
        menuFondo.classList.remove("visible");
        menuToggle.classList.remove("oculto");
        menuToggle.setAttribute("aria-expanded", "false");
        menuLateral.setAttribute("aria-hidden", "true");
    }

    if (menuToggle && menuLateral && menuFondo) {
        menuToggle.addEventListener("click", function () {
            const abierto = menuLateral.classList.contains("abierto");
            abierto ? cerrarMenu() : abrirMenu();
        });
        menuFondo.addEventListener("click", cerrarMenu);
        if (menuCerrar) {
            menuCerrar.addEventListener("click", cerrarMenu);
        }
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                cerrarMenu();
            }
        });
    }

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

    const divisionSelect = document.getElementById("division");
    const participanteIndividual = document.getElementById("participante-individual");
    const participanteParejas = document.getElementById("participante-parejas");
    const labelParticipanteIndividual = document.getElementById("label-participante-individual");
    const labelParticipanteParejas = document.getElementById("label-participante-parejas");

    if (divisionSelect && participanteIndividual && participanteParejas) {
        divisionSelect.addEventListener("change", function () {
            const esIndividual = this.value === "individual";
            const esParejas = this.value === "parejas";

            participanteIndividual.classList.toggle("oculto", !esIndividual);
            participanteIndividual.disabled = !esIndividual;

            participanteParejas.classList.toggle("oculto", !esParejas);
            participanteParejas.disabled = !esParejas;

            if (labelParticipanteIndividual) {
                labelParticipanteIndividual.classList.toggle("oculto", !esIndividual);
            }
            if (labelParticipanteParejas) {
                labelParticipanteParejas.classList.toggle("oculto", !esParejas);
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
