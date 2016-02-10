
var $altima_jq = jQuery.noConflict();

$altima_jq(document).ready(function() {
    $altima_jq.fn.lightTabs = function(options){

        var createTabs = function(){
            tabs = this;
            i = 0;

            showPage = function(i){
                $altima_jq(tabs).children("div").children("div").hide();
                $altima_jq(tabs).children("div").children("div").eq(i).show();
                $altima_jq(tabs).children("ul").children("li").removeClass("active");
                $altima_jq(tabs).children("ul").children("li").eq(i).addClass("active");
            }

            localStorage.selectedTab = (localStorage.selectedTab) ? localStorage.selectedTab : 0;
            showPage(localStorage.selectedTab);

            $altima_jq(tabs).children("ul").children("li").each(function(index, element){
                $altima_jq(element).attr("data-page", i);
                i++;
            });

            $altima_jq(tabs).children("ul").children("li").click(function(){
                localStorage.selectedTab = parseInt($altima_jq(this).attr("data-page"));
                showPage(parseInt($altima_jq(this).attr("data-page")));
            });
        };
        return this.each(createTabs);
    };
});

$altima_jq(document).ready(function() {
    $altima_jq(".tabs").lightTabs();
});
