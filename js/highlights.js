/*jshint eqeqeq:false*/
/*global jQuery:false, $:false, box:false*/

box.getJDoc().ready(function() {
	$('.carousel').each(function() {
		var sId = this.id;
		box.get('ui').create('carousel.' + sId, {
			rootElm: '#' + sId,
			horizontal: true,
			display: 1,
			duration: 600,
			paginate: true,
			buttons: true,

			txtBtnPrev: box.get('l10n:carousel').prev,
			txtBtnNext: box.get('l10n:carousel').next
		});
	});
});