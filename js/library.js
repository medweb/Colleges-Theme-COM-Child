/**
 * Created by stephen
 *
 * To use, place this shortcode inside any page.
 * [library]
 *
 * To restrict to specific resource types, add the parameter for that resource.
 * [library resource="E-Databases" ]
 * [library resource="E-Journals" ]
 * [library resource="E-Books" ]
 * [library resource="E-Textbooks" ]
 * 
 * Shortcode code: includes/shortcodes/library_books.php 
 */
jQuery( document ).ready(function($) {

    // X on page load, get the request tokens.
    // X also, get the resource types.
    // X also, get subject types.
    // X also, get sessions.
    // create dropdown input for the resource. default to first. (can only search on unique resources; can't combine).
    // create dropdown input for subject, limited to currently selected resource. default to none/all (can possibly search multiple subjects; not sure if OR or AND logic)
    // create dropdown input for session.
    // create dropdown input for number of items per page (default 20, allow 10, 20, 30, 50, 100)
    // create dropdown for search string filtering. hardcoded search fields. default to 'all'.
    //      Title, SortTitle, URL, ISBNOrISSN, Provider, Description, Year, and Authors
    // create input for search string
    // display list of all books in current resource, limit to first 20.
    // on dropdown change, ajax to limit books.
    // also on search string change, ajax to limit books.

    var g_request_tokens;
    var g_search_criteria = {}; // the search parameters currently set
    g_search_criteria.Sessions = []; // default to empty array
    g_search_criteria.Subjects = [];
    g_search_criteria.SortCriteria = [];
    g_search_criteria.FilterCriteria = [];
    g_search_criteria.ShowImages = true;
    g_search_criteria.PageSize = 30; // 30 per page default
    g_search_criteria.PageNumber = 0; // start at first page (0-based)
    g_search_criteria.ShowActive = true; // only show 'active' resources. this lets defunct resources stay in the system without requiring deletion.

    var api_domain = 'https://library.med.ucf.edu/api/Communications'; // COM IT api url. test is librarydev.med., and production is library.med.

    var proxy_prefix = 'https://login.ezproxy.med.ucf.edu/login?url='; // e-books must be prefixed by the proxy to allow off-campus access

    // Initialize the form (get dropdown values)
    initial_load_form_options();


    /**
     * On change functionality. When dropdowns change,
     * set the search criteria and then run a search.
     */
    $('#resource').on("change", function(){

        // unlike sessions or subjects, this should always be set to
        // a value. 'all' does not exist with this API for resource.
        g_search_criteria.ResourceType = $('#resource').val();
        get_subjects();
        //get_books(); // run primary search
        g_search_criteria.PageNumber = 0;
    });
    $('#session').on("change", function(){
        // after changing session, modify the search criteria and
        // then update the results.
        // Do not update the subjects; it's better for the user to be able
        // to select a session and subject that contains no results
        // rather than hiding a subject or session because of a choice.
        var session_selector = $('#session');
        if ((session_selector.val()) && (session_selector.val() != 'all')) {
            // user specified a session. add the search criteria
            g_search_criteria.Sessions = [session_selector.val()];

        } else {
            // no session specified. show all.
            g_search_criteria.Sessions = [];
        }
        g_search_criteria.PageNumber = 0;
        get_books(); // run primary search

    });
    $('#subject').on("change", function(){
        // after changing subject, modify the search criteria and
        // then update the results.
        // Do not update the subjects; it's better for the user to be able
        // to select a session and subject that contains no results
        // rather than hiding a subject or session because of a choice.
        var subject_selector = $('#subject');
        if ((subject_selector.val()) && (subject_selector.val() != 'all')) {
            // user specified a session. add the search criteria
            g_search_criteria.Subjects = [subject_selector.val()];
        } else {
            // no session specified. show all.
            g_search_criteria.Subjects = [];
        }
        g_search_criteria.PageNumber = 0;
        get_books(); // run primary search
    });

    /**
     * Run a search when the user types and submits the search form.
     * Note: the only time the form should be submitted manually is
     * when the user types in a text search and submits the form.
     * All other input fields (select options) automatically
     * submit ajax requests and update the book list.
     */
    $('#library-search').submit(function(event){
        event.preventDefault(); // don't reload the page; our form doesn't actually submit.

        // reset the user to the first page anytime they search for text.
        g_search_criteria.PageNumber = 0;
        g_search_criteria.FilterCriteria = [];


        // Commented out code: This breaks the user search into multiple words, which was to allow for
        // searching books that had punctuation or special characters. However, the multiple filters
        // caused the query to slow down with each word added. COM IT modified their API to allow
        // searches with punctuation ignored, so this code is not needed.
        // It does, however, also allow the user to search for words out of order or for a phrase where
        // the title actually contains a word in-between. If COM IT updates their API to run quickly with
        // multiple filters, it might be good to reactivate this code.
        /*
        var user_search_text = $('#library-search-text').val();
        var user_search_array = user_search_text.split(" ");

        user_search_array.forEach(function(word){
            word = word.replace(/[^0-9a-z]/gi, '');

            // exclude any characters other than alphanumeric in a word.
            if (word) {
                var filter_criteria = {};
                filter_criteria.Fields = [ 'Title', 'SortTitle', 'Authors', 'Description', 'Provider' ];
                filter_criteria.FilterOption = 'contains'; // 'contains' or 'starts with'. contains is usually best.
                filter_criteria.Parameter = word;
                filter_criteria.FilterOrder = 2;
                g_search_criteria.FilterCriteria.push(filter_criteria);
            }
        });*/
        var filter_criteria = {};
        filter_criteria.Fields = ['Title','SortTitle','Authors','Description'];
        filter_criteria.FilterOption = 'contains'; // 'contains' or 'starts with'. contains is usually best.
        filter_criteria.Parameter = $('#library-search-text').val();
        filter_criteria.FilterOrder = 1;
        
        g_search_criteria.FilterCriteria.push(filter_criteria);
        console.log(g_search_criteria.FilterCriteria);
        console.log(g_search_criteria);
        get_books(); // run primary search
    });

    /**
     * Redo search when sort is changed.
     * @api  Valid fields are Title, SortTitle, URL, ISBNOrISSN, Provider, Description, Year, and Authors.
     * @api  Valid sort criteria are 'ascending' and 'descending'.
     * @TODO should we reset page to 0?
     * @TODO figure out how to allow user to sort opposite of current.
     */
    $('#sortby, #sortby-order').on("change", function(){
        var sort_criteria = {};
        sort_criteria.Field = $('#sortby').val();
        sort_criteria.SortOption = $('#sortby-order').val();
        sort_criteria.SortOrder = '1'; // can have multiple sort options. different numbers change the priority.
        // if the first criteria is equal, it sorts by the second sortorder. if sortorder is equal between two options, the order
        // in which they are applied is undefined.

        g_search_criteria.SortCriteria = [sort_criteria];
        get_books();
    });

    /**
     * Changes the ascending/descending text to be more understandable based on the sort field.
     */
    $('#sortby').on("change", function(){
        if ($('#sortby').val() == 'Year'){
            $('#sortby-order option[value="ascending"]').text("Oldest to Newest");
            $('#sortby-order option[value="descending"]').text("Newest to Oldest");
        } else {
            $('#sortby-order option[value="ascending"]').text("A - Z");
            $('#sortby-order option[value="descending"]').text("Z - A");
        }
    });

    /**
     * Begin Function Definitions
     */

    /**
     * Sets the validation tokens required for the API calls.
     * This token expires after use, or after a few seconds, so
     * it must be refreshed for every api call.
     *
     */
    function get_request_tokens(){
        //console.log('getting tokens');
        return $.ajax({
            type: "Get",
            url: api_domain + "/GetRequestTokens",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (request_tokens) {
                // We succeeded in our request. Set the request tokens for our request model.
                g_request_tokens = request_tokens;
                g_search_criteria.RequestTokens = request_tokens;
                //console.log(request_tokens);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                // on failure, reset token to null.
                g_search_criteria.RequestTokens = null;
                console.log('error getting tokens');
            }
        });

    }

    function initial_load_form_options(){
        // @TODO possibly save between refreshes, so that a refresh doesn't load default or empty values
        $.when(get_request_tokens()).done(function(){
            //console.log(g_search_criteria.RequestTokens);

            // assuming we have valid tokens, get the resource types
            if (g_search_criteria.RequestTokens){
                var resource_selector = $('#resource');
                // get the resource types. then populate the dropdown.
                get_resource_types(); // this will cascade to get subjects as well

                get_sessions(); // this can load simultaneously.

            }
        });


    }

    function get_resource_types(){
        // get all resource types
        $.when(get_request_tokens()).done(function() {
            $.ajax({
                type: "Post",
                url: api_domain + "/GetResourceTypes",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                data: JSON.stringify(g_search_criteria.RequestTokens),
                success: function (results) {

                    populate_resource_types(results);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Your error handling here
                    // No types detected.

                    //return null;
                }
            });
        });
    }

    /**
     * Populates the dropdown for resource types.
     * Calls the ajax function to get subjects, limited
     * to the current resource type.
     */
    function populate_resource_types(json_resources){

        // parse the json and load the html
        //console.log("resources: " + json_resources["Results"]);
        var resource_selector = $('#resource');
        if (resource_selector.prop('type') != 'hidden') {

            resource_selector
                .empty()
                .append("<option value='header' disabled  >Resource</option>")
                .append("<option value='separator' disabled  >------------</option>");

            $(json_resources["Results"]).each(function(){
                resource_selector.append("<option>" + this + "</option>");
            });

            var default_resource = json_resources["Results"][0]; // cannot search by all resources, so default to first one.
            resource_selector.val(default_resource);
        }

        g_search_criteria.ResourceType = $('#resource').val();
        get_subjects();
    }

    function get_sessions(){
        // get all resource types
        $.when(get_request_tokens()).done(function() {
            $.ajax({
                type: "Post",
                url: api_domain + "/GetSessions",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                data: JSON.stringify(g_search_criteria.RequestTokens),
                success: function (results) {

                    populate_sessions(results);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Your error handling here
                    // No types detected.

                    //return null;
                }
            });
        });
    }

    function populate_sessions(json_sessions){
        //console.log('sessions: ' + json_sessions["Results"]);
        var session_selector = $('#session');
        session_selector
            .empty()
            .append("<option value='header' disabled  >Session</option>")
            .append("<option value='all'>All Sessions</option>").prop('selected','true')
            .append("<option value='separator' disabled  >------------</option>");

        $(json_sessions["Results"]).each(function(){
            session_selector.append("<option>" + this + "</option>");
        });
        get_books();


    }

    function get_subjects(){
        // get all subjects based on the currently defined search parameters

        // ajax call
        $.when(get_request_tokens()).done(function() {

            $.ajax({
                type: "Post",
                url: api_domain + "/GetSubjectsByResourceType",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                data: JSON.stringify(g_search_criteria),
                success: function (results) {

                    populate_subjects(results);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Your error handling here
                    // No types detected.

                    //return null;
                }
            });
        });
    }

    // Will populate subjects, and then force a get_books call as well.
    function populate_subjects(json_subjects){
        //console.log("subjects: " + json_subjects["Results"]);
        var subject_selector = $('#subject');

        // when populating, save the current choice (if exists).
        // then, if the new list still has that choice, auto select it.
        // otherwise, default to 'all'.
        var user_choice = $('#subject').val();
        //console.log("User choice: " + user_choice);

        subject_selector
            .empty()
            .append("<option value='header' disabled  >Subject</option>")
            .append("<option value='all'>All Subjects</option>").prop('selected','true')
            .append("<option value='separator' disabled  >------------</option>");

        $(json_subjects["Results"]).each(function(){
            subject_selector.append("<option value='" + this + "'>" + this + "</option>");
        });
        if ($("#subject option[value='" + user_choice + "']").length > 0) {
            // the user's previous choice still exists in this list. select it.
            $("#subject option[value='" + user_choice + "']").prop('selected','true');
        }
        get_books();
    }

    /**
     * The main search function.
     * Runs a search using the user-provided form data.
     * Returns paginated results of matching resources.
     *
     * (Not just books, but that's the easiest name for the function.)
     */
    function get_books(){

        //console.log("Search Criteria for books: " + JSON.stringify(g_search_criteria));

        if ($('#resource').val() == 'E-Databases' ) {
            g_search_criteria.SubjectsSortOption = 'ascending'; // group e-databases by subject
        }
        $('#search-results').empty();
        $('#search-pagination-top, #search-pagination-bottom').hide();
        $('#restricted-explanation').hide();
        $('div.loader').show();

        $.when(get_request_tokens()).done(function() {
            $.ajax({
                type: "Post",
                url: api_domain + "/GetResources",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                data: JSON.stringify(g_search_criteria),
                success: function (results) {
                    populate_books(results);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Your error handling here
                    // No types detected.
                    console.log('search error');
                    //return null;
                }
            });
        });


    }

    function populate_books(json_search_results){
        var book_selector = $('#search-results');

        book_selector.empty(); // clear the div first.

        if ($('#resource').val() == 'E-Databases' ) {
            // for e-databases, group by subject.
            // loop through all, get the list of unique subjects.
            // then loop through each subject, make a header,
            // and print out all resources that have that subject.
            var subjects = [];
            console.log(json_search_results);

            $(json_search_results.Results).each(function(){
                if (this.Subjects.length > 0) {
                    subjects.push(this.Subjects[0].Title)
                }
            });
            function onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            }
            subjects = subjects.filter(onlyUnique);
            subjects.forEach(function(subject){
                //@TODO echo subject header here. then loop through all books and check if it matches.
                book_selector.append('<h2>' + subject + '</h2>');
                $(json_search_results.Results).each(function(){
                    if ((this.Subjects.length > 0) && (subject == (this).Subjects[0].Title))
                        {
                            book_selector.append(book_json_to_html(this));
                        }
                });

            })

        } else {
            // for everything else, just print out the books
            $(json_search_results.Results).each(function(){
                book_selector.append(book_json_to_html(this));
            });
        }

        console.log(json_search_results);
        update_pagination(json_search_results.TotalResults); // the search always gives us the total results so we can paginate

        $('#search-pagination-top, #search-pagination-bottom').show();
        $('#restricted-explanation').show();
        $('div.loader').hide();
    }

    /**
     * Takes a single book json object and returns html to append
     * @param json_book
     */
    function book_json_to_html(json_book){
        // restricted, thumbnail, author, title, date, provider
        var book_element = "";

        book_element += "<div class='book' data-PKResourceID='" + json_book.PKResourceID + "'>";
        // restricted
        if (json_book.LimitedToCOM){
            book_element += "<div class='restricted' alt='Restricted to College of Medicine' title='Restricted to College of Medicine'> </div>";
        } else {
            book_element += "<div class='unrestricted'> </div>";
        }

        if (json_book.Thumbnail && json_book.ThumbnailContentType) {
        // thumbnail
            book_element += "<div class='thumbnail'><img src='data:" + json_book.ThumbnailContentType + ";base64," + json_book.Thumbnail + "' /></div>"
        }

        if (json_book.SortTitle) {
            // title
            book_element += "<div class='title'><a href='" + proxy_prefix + json_book.URL + "' onclick='/*return redirect(\"" + proxy_prefix + json_book.URL + "\") */;' target='blank' >" + json_book.Title + "</a></div>";
        }

        if (json_book.Description) {
            // description. used by e-databases
            book_element += "<div class='description'><span>" + json_book.Description + "</span></div>";
        }

        if (json_book.Authors) {
            // author
            book_element += "<div class='author'><span class='author-header'>Author: </span><span class='author-value'>" + json_book.Authors + "</span></div>";
        }

        if (json_book.Year) {
        // date
            book_element += "<div class='date'><span>Year: " + json_book.Year + "</span></div>";
        }

        if (json_book.DateRange) {
            // date range - for e-journals
            book_element += "<div class='daterange'><span>Date Range: " + json_book.DateRange + "</span></div>";
        }

        if (json_book.Provider) {
        // provider
            book_element += "<div class='provider'><span>Provider: " + json_book.Provider + "</span></div>";
        }

        if (json_book.ISBNOrISSN) {
            // issn is used for monthly or serial publications. they do not define a specific book or revision.
            // issn numbers are always 8 digit numbers, sometimes with a dash in the middle.
            // isbn numbers are always at least 9 digits. therefore, we can distinguish between
            // the two by counting the digits.
            if (json_book.ISBNOrISSN.replace(/[^0-9]/g,"").length == 8) {
                book_element += "<div class='isbn issn'><span>ISSN: " + json_book.ISBNOrISSN + "</span></div>";
            } else {
                book_element += "<div class='isbn'><span>ISBN: " + json_book.ISBNOrISSN + "</span></div>";
            }
        }

        book_element += "</div>";

        return book_element;
    }

    /**
     * Creates the pagination section.
     * This shows how many pages there are in the search, and your current page.
     * @param total_count
     */
    function update_pagination(total_count){

        if (total_count > 0) {
            var pages_total = Math.floor((total_count - 1) / g_search_criteria.PageSize) + 1; // calculate total pages by results givin from API
        } else {
            var pages_total = 1; // default to 1 page with 0 results. 0 pages messes up the pagination plugin.
        }

        var pagination = $('#search-pagination-top, #search-pagination-bottom'); // <ul> selector for pagination
        pagination.twbsPagination('destroy');


        var defaultOpts = {
            initiateStartPageClick: false, // don't trigger a click event upon creation; get_books has already loaded, so calling here would create an infinite loop
            visiblePages: 5, // number of links or buttons to show of neighbor pages
            first: "|<",
            last: ">|",
            prev: "<",
            next: ">",
            hideOnlyOnePage: false, // @TODO possible bug in plugin. if this is true, it will loop forever calling the onPageClick handler if there is only one page.
                                    // Possibly the hide function prevents reading the inistiateStartPageClick, or the act of hiding causes it to trigger.
            onPageClick: function (event, page) {
                // when the user clicks another page number, update the query and run the ajax call for books
                g_search_criteria.PageNumber = page - 1; // api is 0-based for page numbers, but pagination uses 1-based.
                get_books();
            }
        };
        // after each page change, destroy pagination and recreate with new settings.
        // this also works when menu items are updated, which changes total results and thus total page numbers.
        pagination.twbsPagination($.extend({}, defaultOpts, {
            totalPages: pages_total,
            startPage: g_search_criteria.PageNumber + 1,
            initiateStartPageClick: false,
        }));


        /**
         * Add text showing the current result range.
         */
        var pagination_result_range_text = "";
        pagination_result_range_text += "<div class='pagination-range'><span>" + total_count + " items in " + pages_total + " pages.</span></div>";

        // @TODO fix CSS to make this look good, then uncomment and publish to production
        $('#search-pagination-range-bottom').empty().append(pagination_result_range_text);

        /**
         * Now, update the alternate pagination. This lets the user manually type in a page number.
         *
         */

        var pagination_alternate = $('#search-pagination-alternate-top, #search-pagination-alternate-bottom');
        // @TODO create input with min 1 and max of pages_total, step 1. also a 'go' button. also show current number as current page.
        pagination_alternate.empty();

        var pagination_alternate_html = "";
        pagination_alternate_html += "<label for='page-number'>Page</label>";
        pagination_alternate_html += "<input id='page-number' type='number' step='1' min='1' max='" + pages_total + "' placeholder='1 - " + pages_total + "' />";
        pagination_alternate_html += "<input type='submit' value='Go To Page' />";

        pagination_alternate.append(pagination_alternate_html);
    }
});

/**
 * Redirects the user to the url specified, with the proxy prefix added.
 * @param URL
 * @returns {boolean}
 */
function redirect(URL){
    window.open(URL, '_blank');
    return false;
}