/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}



(function() {

	/**
	 * @todo make the new item fields more dynamic
	 */

	// CartMenu plugin
	var PlatformMenu = {

		// Settings
		settings : {

			// Add new item selector
			addItemSelector : '.new-item-add',

			// Menu selector
			menuSelector: '.platform-menu',

			// Item selectors
			itemSelectors: {
				wrapper   : '.item',
				handle    : '.item-header',
				toggle    : '.item-toggle-details',
				toggleAll : '.toggle-all-items',
				details   : '.item-details',
				remove    : '.item-remove'
			},

			// Array of fields that each menu item has,
			// along with the selectors for each one
			itemFields : [
				{
					name        : 'name',
					newSelector : '.new-item-name'
				},
				{
					name        : 'slug',
					newSelector : '.new-item-slug'
				},
				{
					name        : 'uri',
					newSelector : '.new-item-uri'
				}
			],

			// Item template
			itemTemplate: '',

			// Last item identifier
			lastItemId: 0
		},

		// Object the plugin was called on,
		// the form itself
		elem : null,

		// Menu object
		menu : null,

		init : function(elem, settings) {
			var self  = this;
			self.elem = elem;

			// Override default settings
			$.extend(self.settings, settings);

			// Setup menu
			self.setupMenu();

			// Observe new items
			self.observeNewItems();

			// Observe items
			self.observeItems();

			// Observe save
			self.observeSave();
		},

		// Sets up the menu
		setupMenu: function() {
			var self  = this;
			var elem  = self.elem;
			var menu  = elem.find(self.settings.menuSelector);
			self.menu = menu;

			menu.nestedSortable({
				disableNesting       : 'no-nest',
				forcePlaceholderSize : true,
				handle               : 'div header',
				helper               :'clone',
				items                : 'li',
				maxLevels            : 0,
				opacity              : .6,
				placeholder          : 'placeholder',
				revert               : 250,
				tabSize              : 25,
				tolerance            : 'pointer',
				toleranceElement     : '> div'
			});

			// Observe changes
			self.observeMenuChanges();

			return this;
		},

		// Observe menu changes
		observeMenuChanges: function() {
			var self = this;
			var elem = self.elem;
			var menu = self.menu;
		},

		// Observe new items
		observeNewItems: function() {
			var self      = this;
			var elem      = self.elem;
			var settings  = self.settings;

			/**
			 * When user adds a new item
			 */
			elem.find(settings.addMenuItem).on('click', function(e) {
				e.preventDefault();

				alert('f');

				return;

				$name = elem.find(selectors.name);
				$slug = elem.find(selectors.slug);
				$uri  = elem.find(selectors.uri);

				self.addMenuItem($name.val(), $slug.val(), $uri.val());

				return false;
			});

			return this;
		},

		/**
		 * Add a new menu item
		 * 
		 * @param  string  name
		 * @param  string  uri
		 * @return PlatformMenu
		 */
		addMenuItem: function(name, slug, uri) {

			if (name.length == 0 || uri.length == 0) {
				return alert('Fill out all fields.');
			}

			var self         = this;
			var elem         = self.elem;
			var menu         = self.menu;
			var id           = self.settings.lastItemId  + 1;
			var itemTemplate = self.settings.itemTemplate;

			// Update our template with real vars
			itemTemplate = itemTemplate.replace(/\{\{id\}\}/gi, id)
			                           .replace(/\{\{name\}\}/gi, name)
			                           .replace(/\{\{slug\}\}/gi, name)
			                           .replace(/\{\{uri\}\}/gi, uri);

			// Append our item
			menu.append(itemTemplate);

			// Increase the last item id
			self.settings.lastItemId += 1;

			return this;
		},

		observeItems: function() {
			var self = this;
			var elem = self.elem;
			var itemSelectors = self.settings.itemSelectors;

			// Toggle all items
			elem.find(itemSelectors.toggleAll).on('click', function() {

				elem.find(itemSelectors.details).toggleClass('show');
			});

			/**
			 * We're using this selector so we observe any
			 * newly created menu items as well
			 */
			$('body').on('click', elem.selector + ' ' + itemSelectors.wrapper + ' ' + itemSelectors.toggle, function() {
				$wrapper = $(this).closest(itemSelectors.wrapper);
				$wrapper.find(itemSelectors.details).toggleClass('show');
			});
		},

		observeSave: function() {
			var self         = this;
			var elem         = self.elem;
			var saveSelector = self.settings.saveSelector;
			var menu         = self.menu;

			elem.on('submit', function(e) {
				e.preventDefault();

				/**
				 * We combine both the data about the
				 * order of the menu and the inputs
				 * to be posted to the save action through
				 * Ajax
				 */
				var postData = $.extend(elem.find('input').serializeObject(), {
					'items_hierarchy' : menu.nestedSortable('toHierarchy', { attribute: 'data-item' })
				});

				// AJAX call to save menu
				$.ajax({
					url      : elem.attr('action'),
					type     : 'POST',
					// dataType : 'json',
					data     : postData,
					success  : function(data, textStatus, jqXHR) {
						if (data.length && data != 'null') {
							data = data.replace(/null/gi, '');
							alert(data);
						}
					},
					error    : function(jqXHR, textStatus, errorThrown) {
						alert(jqXHR.status + ' ' + errorThrown);
					}
				});
			});

			// // When the user clicks the save button
			// elem.find(saveSelector).on('click', function() {

			// 	/**
			// 	 * We combine both the data about the
			// 	 * order of the menu and the inputs
			// 	 * to be posted to the save action through
			// 	 * Ajax
			// 	 */
			// 	var postData = $.extend(elem.find('input').serializeObject(), {
			// 		items : menu.nestedSortable('toHierarchy', { attribute: 'data-item' })
			// 	});

			// 	// AJAX call to save menu
			// 	$.ajax({
			// 		url      : saveUri + (menuId ? '/' + menuId : ''),
			// 		type     : 'POST',
			// 		// dataType : 'json',
			// 		data     : postData,
			// 		success  : function(data, textStatus, jqXHR) {
			// 			if (data.length && data != 'null') {
			// 				data = data.replace(/null/gi, '');
			// 				alert(data);
			// 			}
			// 		},
			// 		error    : function(jqXHR, textStatus, errorThrown) {
			// 			alert(jqXHR.status + ' ' + errorThrown);
			// 		}
			// 	});
			// });
		}
	}

	// The actual jquery plugin
	$.fn.platformMenu = function(settings) {
		PlatformMenu.init(this, settings);
	}

	$.fn.serializeObject = function()
	{
		var o = {};
		var a = this.serializeArray();
		$.each(a, function() {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

})(jQuery);