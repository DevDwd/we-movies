// assets/modules/search.js

import MovieModal from './modal.js';

export default class MovieSearch {
    constructor() {
        console.log('MovieSearch initialized');
        this.searchInput = document.querySelector('.search-input');
        this.searchResults = document.querySelector('.search-results');
        this.genreForm = document.getElementById('genre-filter-form');
        
        this.modal = new MovieModal();

        this.init();
    }

    init() {
        if (this.searchInput) {
            console.log('Adding search event listener');
            this.searchInput.addEventListener('input', this.debounce((event) => {
                console.log('Search input:', event.target.value);
                const query = event.target.value.trim();
                if (query.length >= 2) {
                    this.performSearch(query);
                } else {
                    this.clearResults();
                }
            }, 300));
        }

        if (this.genreForm) {
            console.log('Adding genre form listeners');
            const checkboxes = this.genreForm.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', (event) => {
                    console.log('Checkbox changed:', event.target.value, event.target.checked);
                    this.handleGenreFilter();
                });
            });
        }

        this.addMovieCardListeners();
    }

    async performSearch(query) {
        console.log('Performing search for:', query);
        try {
            const response = await fetch(`/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            console.log('Search results:', data);
            this.displayResults(data);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displayResults(movies) {
        if (!movies || movies.length === 0) {
            this.searchResults.innerHTML = '<div class="no-results">Aucun film trouvé</div>';
        } else {
            console.log('1');
            console.log("https://image.tmdb.org/t/p/w92${movie.posterPath}");
            this.searchResults.innerHTML = movies.map(movie => `
                <div class="search-result-item" data-movie-id="${movie.id}">
                    <div class="movie-poster">
                        ${movie.posterPath 
                            ? `<img src="https://image.tmdb.org/t/p/w92${movie.posterPath}" alt="${movie.title}">`
                            : '<div class="no-poster"></div>'
                        }
                    </div>
                    <div class="movie-info">
                        <div class="movie-title">${movie.title}</div>
                        <div class="movie-year">${new Date(movie.releaseDate).getFullYear()}</div>
                    </div>
                </div>
            `).join('');

            this.addResultsEventListeners();
        }

        this.searchResults.style.display = 'block';
    }

    addResultsEventListeners() {
        this.searchResults.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', () => {
                const movieId = item.dataset.movieId;
                this.showMovieDetails(movieId);
            });
        });
    }

    addMovieCardListeners() {
        const movieCards = document.querySelectorAll('.movie-card');
        movieCards.forEach(card => {
            card.addEventListener('click', () => {
                const movieId = card.dataset.movieId;
                console.log('Movie card clicked:', movieId);
                this.modal.show(movieId);
            });
        });
    }

    async showMovieDetails(movieId) {
        this.modal.show(movieId);
        this.clearResults();
    }

    addResultClickHandlers() {
        const items = this.searchResults.querySelectorAll('.search-result-item');
        items.forEach(item => {
            item.addEventListener('click', () => {
                const movieId = item.dataset.movieId;
                console.log('Movie clicked:', movieId);
                window.location.href = `/api/movie/${movieId}`;
            });
        });
    }

    clearResults() {
        if (this.searchResults) {
            this.searchResults.style.display = 'none';
            this.searchResults.innerHTML = '';
        }
    }

    handleGenreFilter() {
        const checkboxes = this.genreForm.querySelectorAll('input[type="checkbox"]:checked');
        const selectedGenres = Array.from(checkboxes).map(cb => cb.value);
        
        console.log('Selected genres:', selectedGenres);
        
        if (selectedGenres.length === 0) {
            window.location.href = '/';
        } else {
            window.location.href = `/genre/${selectedGenres.join(',')}`;
        }
    }

    debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing MovieSearch');
    new MovieSearch();
});