var Utility = {
    /* clear local storage */
    clearStorage: function() {
        $.each(window.localStorage, function(key, value) {
            window.localStorage.removeItem(key);
        })
    },
    /* remove an item from local storage */
    removeFromStorage: function(key) {
        window.localStorage.removeItem(key);
    },
    /* add to session storage */
    addToSession: function(key, value) {
        window.sessionStorage.setItem(key, value);
    },
    /* Get value from session storage */ 
    getFromSession: function(key) {
        return window.sessionStorage.getItem(key);
    }

}