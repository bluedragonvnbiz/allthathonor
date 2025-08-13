jQuery(document).ready(function($){

});//end ready

const elementPageLoad = '<div id="page_load"><span class="loader"> </span></div>';
function addLoading() {jQuery('body').append(elementPageLoad);}
function unLoading() {jQuery('#page_load').remove();}
