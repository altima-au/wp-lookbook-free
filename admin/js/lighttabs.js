jQuery.noConflict();
(function($){
    jQuery.fn.lightTabs = function(options){

        var createTabs = function(){
            tabs = this;
            i = 0;

            showPage = function(i){
                $(tabs).children("div").children("div").hide();
                $(tabs).children("div").children("div").eq(i).show();
                $(tabs).children("ul").children("li").removeClass("active");
                $(tabs).children("ul").children("li").eq(i).addClass("active");
            }

            localStorage.selectedTab = (localStorage.selectedTab) ? localStorage.selectedTab : 0;
            showPage(localStorage.selectedTab);

            $(tabs).children("ul").children("li").each(function(index, element){
                $(element).attr("data-page", i);
                i++;
            });

            $(tabs).children("ul").children("li").click(function(){
                localStorage.selectedTab = parseInt($(this).attr("data-page"));
                showPage(parseInt($(this).attr("data-page")));
            });
        };
        return this.each(createTabs);
    };
})(jQuery);

jQuery(document).ready(function(){
    jQuery(".tabs").lightTabs();
});
