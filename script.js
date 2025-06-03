const moisNoms = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

let dateActive = new Date();

const calMoisAnnee = document.getElementById("mois-annee");
const calDates = document.getElementById("cal-dates");

function genererCalendrier(date) {
    const mois = date.getMonth();
    const annee = date.getFullYear();
    calMoisAnnee.textContent = `${moisNoms[mois]} ${annee}`;
    calDates.innerHTML = "";

    const premierJour = new Date(annee, mois, 1);
    const premierJourSemaine = (premierJour.getDay() + 6) % 7;

    const nbJours = new Date(annee, mois + 1, 0).getDate();
    const nbJoursPrec = new Date(annee, mois, 0).getDate();

    // Jours du mois précédent
    for (let i = premierJourSemaine - 1; i >= 0; i--) {
        const jour = nbJoursPrec - i;
        calDates.innerHTML += `<div class="autre-mois">${jour}</div>`;
    }


    // Jours du mois actuel
    for (let i = 1; i <= nbJours; i++) {
        const ajd = new Date();
        const dateJour = new Date(annee, mois, i);
        const estPasse = dateJour < ajd;

        const dateStr = `${annee}-${(mois + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;
        const estDispo = datesDisponibles.includes(dateStr);

        calDates.innerHTML += `<div class="${estPasse ? "passe" : ""} ${estDispo ? "disponible" : ""}" data-jour="${i}">${i}</div>`;
    }


    // Ajouter un écouteur pour la sélection
    document.querySelectorAll("#cal-dates div").forEach(cell => {
        if (!cell.classList.contains("autre-mois")) {
            cell.addEventListener("click", () => {
                // Efface les sélections précédentes
                document.querySelectorAll("#cal-dates div.selectionne").forEach(c => c.classList.remove("selectionne"));
                cell.classList.add("selectionne");

                const jour = cell.getAttribute("data-jour");
                const moisActuel = dateActive.getMonth() + 1;
                const anneeActuelle = dateActive.getFullYear();
                const dateStr = `${anneeActuelle}-${moisActuel.toString().padStart(2, '0')}-${jour.padStart(2, '0')}`;

                // Met à jour le texte
                document.getElementById("jour-selectionne").textContent = `${jour}/${moisActuel}/${anneeActuelle}`;

                // Appel AJAX
                fetch("rdv.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `date=${dateStr}`
                })
                    .then(response => response.json())
                    .then(data => {
                        const creneauxDiv = document.getElementById("liste-creneaux");
                        creneauxDiv.innerHTML = "";

                        if (data.length === 0) {
                            creneauxDiv.innerHTML = "<p>Aucun créneau disponible pour ce jour.</p>";
                            return;
                        }

                        data.forEach(creneau => {
                            const btn = document.createElement("button");
                            btn.className = "btn-creneau";
                            btn.textContent = creneau.heure;
                            btn.onclick = () => {
                                const serviceId = document.getElementById("service-select").value;
                                if (!serviceId) {
                                    alert("Veuillez d'abord sélectionner un service.");
                                    return;
                                }

                                if (!confirm(`Confirmer la réservation du créneau ${creneau.heure} pour ce service ?`)) return;

                                fetch("rdv.php", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                    body: `action=reservation&date=${dateStr}&heure=${creneau.heure}&service_id=${serviceId}`
                                })
                                    .then(res => res.text())
                                    .then(txt => {
                                        alert(txt);
                                        location.reload(); // pour rafraîchir les créneaux
                                    })
                                    .catch(err => console.error("Erreur réservation :", err));
                            };

                            creneauxDiv.appendChild(btn);
                        });
                    })
                    .catch(error => {
                        console.error("Erreur AJAX :", error);
                    });
            });
        }
    });


}

document.getElementById("mois-suivant").addEventListener("click", () => {
    dateActive.setMonth(dateActive.getMonth() + 1);
    genererCalendrier(dateActive);
});

document.getElementById("mois-precedent").addEventListener("click", () => {
    dateActive.setMonth(dateActive.getMonth() - 1);
    genererCalendrier(dateActive);
});

let datesDisponibles = [];

fetch("rdv.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=dates_disponibles"
})
    .then(res => res.json())
    .then(data => {
        datesDisponibles = data;
        genererCalendrier(dateActive);
    });
genererCalendrier(dateActive);


function deconnecter() {
    if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
        let path = window.location.pathname.includes('/admin') ? '../logout.php' : 'logout.php';
        let pathindex = window.location.pathname.includes('/admin') ? '../index.php' : 'index.php';
        fetch(path, { method: "POST" })
            .then(response => {
                if (response.ok) {
                    window.location.href = pathindex;
                } else {
                    alert("Erreur lors de la déconnexion.");
                }
            })
            .catch(error => {
                console.error("Erreur réseau :", error);
                alert("Erreur réseau.");
            });
    }
}
