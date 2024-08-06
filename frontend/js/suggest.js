$(document).ready(function(){
    var shopIoUrl = window.location.origin+'/io.php';
    /**
     * ======================================================================
     *  LS SEARCH SUGGESTION
     * ======================================================================
     */
    $(document).on('click', 'form.activesuggestion .form-clear', removeSuggestionContainer);       
    $(document).on('keyup', 'input[name="qs"].tt-input', addSuggestionContainer);
    $(document).on('click', '#mobile-search-dropdown', lsSearchSuggestionToggle);
    var typingTimer;
    var doneTypingInterval = 500;   
    var $input = $("input[name='qs'].tt-input");

    $input.on('keyup', function (e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(lsSearchSuggestions, doneTypingInterval); 
    });

    $input.on('keydown', function (e) {
        clearTimeout(typingTimer);
    });

    // Toggle container
    function lsSearchSuggestionToggle(e){  
        // $input.focus();
        var elem = $(this);
        // elem = $input; 
        var pElem =  elem.closest('form');
        $('html').addClass('disablescroll');
        addSuggestionContainer(); 
        lsSearchSuggestions();

    }

    // Add Suggestion container
    function addSuggestionContainer(){
        var elem = $input; 
        var pElem =  elem.closest('form');
        var inputWrap  = elem.closest('form').find('.form-icon');
        var prodElem  = elem.closest('form').find('.ls-suggestion .prod-sg-content');
        // pElem.addClass('activesuggestion');
        if(pElem.find('.ls-suggestion').length <= 0){ 
            $.ajax({
                type: 'POST',
                url: shopIoUrl,
                data: {
                    'io': JSON.stringify(
                        {
                            'name': 'lsGetSuggestionContainer', 
                            'params': [{'type':'html'}]  
                        }
                    ),
                }
            }).done(function(data){
                if(data.prodsJSON){
                    pElem.find(".ls-suggestion").html("sdfgdfgdfg");
                    // prodElem.find('.content-wrap').append(jQuery.parseJSON(data.prodsJSON));  
                    console.log("aja");
                }
                // console.log(jQuery.parseJSON(data.jsonData));
                if(data.jsonData){
                    if(inputWrap.find('.arrow-right').length <= 0){
                        // inputWrap.prepend(jQuery.parseJSON(data.searchHereText));
                    }
                    pElem.addClass('activesuggestion');
                    pElem.append(jQuery.parseJSON(data.jsonData));
                    lsSearchSuggestions();
                    console.log("yes");
                }
            });
        }
    }
    
    function removeSuggestionContainer(){
        var elem = $input;
        var pElem =  elem.closest('form');
        $('html').removeClass('disablescroll');   
        pElem.removeClass('activesuggestion'); 
        pElem.find('.ls-suggestion').remove();
        pElem.find('.sec-text').remove();
        pElem.find('.arrow-icon').remove();
        $input.val("");  
    }

    $(document).on("mouseup", function(e){
        var container = $(".search-wrapper");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            removeSuggestionContainer(); 
        }
    })

    // AJAX SEARCH DATA
    function lsSearchSuggestions(e){
        var elem = $(this);
        elem = $input;
        $($input).each(function(e){
            if($(this).val()){
                elem = $(this);
                console.log($(this).val()+" dfsd");
            }
        });
        // search result containers
        var sgWrap  = elem.closest('form').find('.ls-suggestion');
        var prodElem  = elem.closest('form').find('.ls-suggestion .prod-sg-content');
        var catElem  = elem.closest('form').find('.ls-suggestion .cat-sg-content');
        // adding loaders to containers 
        prodElem.closest('.sg-sec-wrap').addClass('is-loading');
        catElem.closest('.sg-sec-wrap').addClass('is-loading');  
        // setting up request
        var currentRequest = null;  
        // console.log(elem.val());
        if(elem.val() && elem.val().length > 0){
            addSuggestionContainer();
            let cVal = elem.val();
            var currentRequest = $.ajax({
                type: 'POST',
                url: shopIoUrl,
                beforeSend : function()    {           
                    if(currentRequest != null) {
                        // console.log(currentRequest);
                        currentRequest.abort();
                        // console.log(currentRequest);
                    }
                }, 
                data: {
                    'io': JSON.stringify(
                        {
                            'name': 'lsGetSearchSuggestions', 
                            'params': [{'type':'suggestion', 'key':cVal}]  
                        }
                    ),
                }
            }).done(function(data){
                // console.log(data);
                prodElem.find('.content-wrap').empty();  
                catElem.find('.content-wrap').empty();  
                if(data.prodsJSON){
                    prodElem.find('.content-wrap').append(jQuery.parseJSON(data.prodsJSON));  
                }
                if(data.catsJSON){
                    catElem.find('.content-wrap').append(jQuery.parseJSON(data.catsJSON));  
                }
                prodElem.closest('.sg-sec-wrap').removeClass('is-loading');
                catElem.closest('.sg-sec-wrap').removeClass('is-loading'); 
            }).fail(function(jqXHR,textStatus){

                prodElem.closest('.sg-sec-wrap').removeClass('is-loading');
                catElem.closest('.sg-sec-wrap').removeClass('is-loading'); 
            })
        }else{
            prodElem.closest('.sg-sec-wrap').removeClass('is-loading');
            catElem.closest('.sg-sec-wrap').removeClass('is-loading'); 
        }
    }
});


