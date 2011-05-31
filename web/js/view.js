(function($) {
  
  $('#dm_page div.dm_widget.content_gallery_background').live('dmWidgetLaunch', function()
  {
    //alert(dmWidgetGalleryBackgroundPlugin.medias.length);

    // only if elements in gallery
    if(!dmWidgetGalleryBackgroundPlugin.medias.length)
    {
      return;
    }

    // get options from gallery metadata
    var options = dmWidgetGalleryBackgroundPlugin.options;
    
    var defaultOptions = {
      containment:"body", // or "#myGalleryElement"
      timer:options.delay*1000,
      effTimer:options.duration*1000,
      controls:"#controls",
      grayScale:false,
      // If your server allow directory listing
      //folderPath:"elements/",
      // else:
      images:dmWidgetGalleryBackgroundPlugin.medias,

      onStart:function(){$("#controls .play").hide();$("#controls .pause").show();},
      onPause:function(){$("#controls .play").show();$("#controls .pause").hide();},
      onPlay:function(opt){$("#controls .play").hide();$("#controls .pause").show();},
      onChange:function(idx){}, //idx=the zero based index of the displayed photo
      onNext:function(opt){},   //opt=the options relatives to this component instance
      onPrev:function(opt){}   //opt=the options relatives to this component instance
    };
    
    $.mbBgndGallery.buildGallery(defaultOptions);    

  });

})(jQuery);
