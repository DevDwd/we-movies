// assets/app.js
import './styles/app.scss';

import MovieSearch from './modules/search';
import MovieModal from './modules/modal';
import FeaturedMovie from './modules/featured-movie';
import GenreFilter from './modules/genre-filter';

document.addEventListener('DOMContentLoaded', () => {
    new MovieSearch();
    new MovieModal();
    new FeaturedMovie();
    new GenreFilter();
});