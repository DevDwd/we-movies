// modules/movies.js
import MovieModal from './modal.js';

export default class Movies {
    constructor() {
        this.initSearchInput();
        this.initMovieCards();
        this.modal = new MovieModal();
    }

    initSearchInput() {
        const searchInput = document.querySelector('.search-input');
        const searchResults = document.querySelector('.search-results');

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', this.debounce(async (e) => {
                const query = e.target.value.trim();
                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }

                try {
                    const response = await fetch(`/api/movie/search?q=${encodeURIComponent(query)}`);
                    const movies = await response.json();
                    this.displaySearchResults(movies, searchResults);
                } catch (error) {
                    console.error('Search error:', error);
                }
            }, 300));

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }
    } 

    initMovieCards() {
        document.querySelectorAll('.movie-card').forEach(card => {
            card.addEventListener('click', async () => {
                const movieId = card.dataset.movieId;
                if (movieId) {
                    await this.showMovieDetails(movieId);
                }
            });
        });
    }

    async showMovieDetails(movieId) {
        try {
            const response = await fetch(`/api/movie/${movieId}`);
            const movie = await response.json();
            console.log('movie');
            console.log(movie);
            if (movie && typeof movie === 'object') {
                console.log('appel de show() movie');
                await this.modal.show(movie);
            }
        } catch (error) {
            console.error('Error loading movie details:', error);
        }
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    displaySearchResults(movies, container) {
        if (!movies.length) {
            container.innerHTML = '<div class="no-results">Aucun film trouvé</div>';
        } else {
            container.innerHTML = `
                <div class="search-results-list">
                    ${movies.map(movie => `
                        <div class="search-result-item" data-movie-id="${movie.id}">
                            <div class="search-result-poster">
                                ${movie.poster_path
                                    ? `<img src="https://image.tmdb.org/t/p/w92${movie.poster_path}" alt="${movie.title}">`
                                    : '<div class="no-poster"></div>'
                                }
                            </div>
                            <div class="search-result-info">
                                <div class="search-result-title">${movie.title}</div>
                                <div class="search-result-meta">
                                    <span class="year">${new Date(movie.release_date).getFullYear()}</span>
                                    <span class="rating">★ ${movie.vote_average.toFixed(1)}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;

            container.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', () => {
                    const movieId = item.dataset.movieId;
                    if (movieId) {
                        this.showMovieDetails(movieId);
                        container.style.display = 'none';
                    }
                });
            });
        }
        container.style.display = 'block';
    }
}