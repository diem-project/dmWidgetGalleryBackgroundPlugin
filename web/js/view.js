(function($) {

  $('#dm_page div.dm_widget.content_gallery_background').live('dmWidgetLaunch', function()
  {
    //alert(dmWidgetGalleryBackgroundPlugin.slides.length);

    $(function($) {
		// only if elements in gallery
		if(!dmWidgetGalleryBackgroundPlugin.options.slides || !dmWidgetGalleryBackgroundPlugin.options.slides.length)
		{
		  return;
		}

		// get options from gallery metadata
		var options = dmWidgetGalleryBackgroundPlugin.options;

		$.supersized(
			dmWidgetGalleryBackgroundPlugin.options
		);

	});
  });
})(jQuery);
