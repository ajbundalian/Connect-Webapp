function toggleMoreFilters() {
    const moreFilters = document.getElementById('more-filters');
    if (moreFilters.style.display === 'none') {
        moreFilters.style.display = 'block';
    } else {
        moreFilters.style.display = 'none';
    }
}