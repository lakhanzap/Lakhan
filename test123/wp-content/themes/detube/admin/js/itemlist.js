/*
HTML Example:

<div class="item-list-container">
	<a href="#" class="add-new-item">Add New Slide</a>
	<ul class="item-list ui-sortable" id="mustxxx-item-list">
	</ul>
	<ul class="item-list-sample" id="mustxxx-item-list-sample">
		<li>
			// Do someting...
			<a href="#" class="delete-item">Delete Item</a>
		</li>
	</ul>
</div>
*/

window.dp = window.dp || {};

(function($) {

dp.itemList = {
	init: function () {
		var $this = this,
			container = $('.dp-panel .item-list-container');
	
		container.each(function(){
			var container = $(this),
				list = container.find('.item-list');
				sample = $('#'+list.attr('id')+'-sample');
			
			sample.appendTo('body');
		
			list.sortable({
				handle:".section-hndle",
				cursor:'move',
				placeholder: 'sortable-placeholder',
				start: function(e, ui) {
					height = ui.item.outerHeight();
					ui.placeholder.height(height);
				}
			});
			
			container.on('click', '.add-new-item', function(e) {
				e.preventDefault();
				$this.add(list, sample, $(this));
				return false;
			});

			list.on('click', '.delete-item', function(e) {
				var item = $(this).parents('li');
				e.preventDefault();
				$this.del(item);
				return false;
			});
		});
	},
	
	add: function(list, sample, object) {
		var m = [];
		list.find('li').each( function() {
			var rel = $(this).attr('rel');
			m.push(parseInt(rel));
		});
		var n = list.find('li').length;
		while($.inArray(n, m) != -1 ) { n++; }
			
		var clone = sample.find('li').clone();
		clone = clone.attr('rel',n).html(sample.html().replace(/##/ig,n));
		
		if(object.attr('data-position') == 'prepend')
			clone.prependTo(list);
		else
			clone.appendTo(list);
	},
	
	del: function(item) {
		if (confirm("Are you sure you want to delete this item?")) { 
			item.animate({opacity: 0.25}, 500, function(){
				item.remove();
			}); 
		} else { 
			return false; 
		}
	}
};
	
$(document).ready(function(){ dp.itemList.init(); });
	
})(jQuery);