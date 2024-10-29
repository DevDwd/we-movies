// assets/modules/genre-filter.js

export default class GenreFilter { 
    constructor() {
        this.form = document.getElementById('genre-filter-form');
        this.checkboxes = document.querySelectorAll('.genre-checkbox');
        this.init();
    }

    init() {
        if (!this.form) return;

        // Écouter les changements sur chaque checkbox
        this.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => this.handleGenreChange());
        });

        // Restaurer les sélections depuis l'URL si elles existent
        this.restoreSelections();
    }

    handleGenreChange() {
        const selectedGenres = Array.from(this.checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedGenres.length === 0) {
            window.location.href = '/';
        } else {
            window.location.href = `/genre/${selectedGenres.join(',')}`;
        }
    }

    restoreSelections() {
        // Récupérer les genres sélectionnés depuis l'URL
        const match = window.location.pathname.match(/\/genre\/(.+)/);
        if (match) {
            const selectedGenres = match[1].split(',');
            this.checkboxes.forEach(checkbox => {
                checkbox.checked = selectedGenres.includes(checkbox.value);
            });
        }
    }
}