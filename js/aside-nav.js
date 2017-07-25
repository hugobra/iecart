// configure scrollbar HTML
// take notice of class="{$clsFace}", because the class name is also configurable
box.get('ui').modifyConfig('scrollable', {
    htmlFace: '<span class="{$clsFace}"><span class="scrollbar-face-start"></span><span class="scrollbar-face-end"></span></span>'
});

// wait for the DOM to be ready
box.dom(document).ready(function() {
    // and create a vertical scroll with buttons

    $('.expandable').on('click','.nav-title', function(){
    	$(this).parent().toggleClass("expanded");
    	box.get('ui').destroy('scrollable.example');
    	box.get('ui').create('scrollable.example', {
	        rootElm: '#scrollable-nav',
	        buttons: true,
	        bar: true // mandatory when buttons are present
	    });
    });


     $('.sub-nav-enabled').on('mouseleave', function(){
        box.get('ui:scrollable.example').compute();
    });


     $('.aside-nav .sub-nav-enabled').click(function(){
        $(this).next('.sub-nav').toggle();
        $(this).toggleClass('selected');
     });
     $('.aside-nav .sub-nav-enabled2').click(function(){
       $('.sub-nav2').toggle();
       $(this).toggleClass('selected');
     });

});