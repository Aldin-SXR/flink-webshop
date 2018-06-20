var Utility = {
    /* clear local storage */
    clearStorage: function() {
        $.each(window.localStorage, function(key, value) {
            window.localStorage.removeItem(key);
        })
    },
    /* add to local storage */
    addToStorage: function(key, value) {
        window.localStorage.setItem(key, value);
    },
    /* Get value from local storage */ 
    getFromStorage: function(key) {
        return window.localStorage.getItem(key);
    },
    /* remove an item from local storage */
    removeFromStorage: function(key) {
        window.localStorage.removeItem(key);
    },
    /* clear session storage */
    clearSession: function() {
        $.each(window.sessionStorage, function(key, value) {
            window.sessionStorage.removeItem(key);
        })
    },
    /* add to session storage */
    addToSession: function(key, value) {
        window.sessionStorage.setItem(key, value);
    },
    /* Get value from session storage */ 
    getFromSession: function(key) {
        return window.sessionStorage.getItem(key);
    },
    /* remove from session storage */
    removeFromSession: function(key) {
        window.sessionStorage.removeItem(key);
    },
    /* Get multiple items from session storage */
    getMultipleFromSession: function(filter_key) {
        var results = [];
        $.each(window.sessionStorage, function(item_key) {
            if (item_key.includes(filter_key)) {
                results.push(window.sessionStorage.getItem(item_key));
            }
        });
        return results;
    }

}