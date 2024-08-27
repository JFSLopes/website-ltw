document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const suggestionsDiv = document.getElementById('suggestions');

    if (!searchInput){
        return; // Exit early if imageContainer is null
    }

    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim().toLowerCase();

        if (query.length > 0) {
            fetchSuggestions(query)
                .then(suggestions => populateSuggestions(suggestions))
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    suggestionsDiv.innerHTML = '<p>Error fetching suggestions</p>';
                });
        } else {
            suggestionsDiv.innerHTML = '';
        }
    });

    function fetchSuggestions(query) {
        return fetch('action_search.php?q=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch suggestions: ' + response.status);
                }
                return response.json();
            });
    }

    function populateSuggestions(suggestions) {
        suggestionsDiv.innerHTML = '';
        if (!Array.isArray(suggestions) || suggestions.length === 0) {
            suggestionsDiv.innerHTML = '<p>No suggestions found</p>';
            return;
        }

        const ul = document.createElement('ul');
        suggestions.forEach(function(suggestion) {
            const li = document.createElement('li');
            li.textContent = suggestion.name;
            li.addEventListener('click', function() {
                
                searchInput.value = suggestion.name;
                
                suggestionsDiv.innerHTML = '';
            });
            ul.appendChild(li);
        });
        suggestionsDiv.appendChild(ul);
    }
});
