box.getJDoc().ready(function() {
    if($('#onglet-box-aside').length>0){
        box.get('ui').create('tabs.aside', {
            tabListElm: '#onglet-box-aside'
        });
    }
    if($('#onglet-box-tab2').length>0){
        box.get('ui').create('tabs.tab2', {
            tabListElm: '#onglet-box-tab2'
        });
    }
    box.get('ui').create('tabs.tab3', {
        tabListElm: '#onglet-box-tab3'
    });
    box.get('ui').create('tabs.tab5', {
        tabListElm: '#onglet-box-tab5'
    });
});