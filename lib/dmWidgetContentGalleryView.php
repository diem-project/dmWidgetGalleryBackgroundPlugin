<?php

class dmWidgetContentGalleryBackgroundView extends dmWidgetPluginView
{
  
  public function configure()
  {
    parent::configure();
    
    $this->addRequiredVar(array('medias', 'method', 'animation'));

    $this->addJavascript(array('dmWidgetGalleryBackgroundPlugin.bgnd', 'dmWidgetGalleryBackgroundPlugin.view'));
    $this->addStylesheet(array('dmWidgetGalleryBackgroundPlugin.view'));
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    // extract media ids
    $mediaIds = array();
    foreach($vars['medias'] as $index => $mediaConfig)
    {
      $mediaIds[] = $mediaConfig['id'];
    }
    
    // fetch media records
    $mediaRecords = empty($mediaIds) ? array() : $this->getMediaQuery($mediaIds)->fetchRecords()->getData();
    
    // sort records
    $this->mediaPositions = array_flip($mediaIds);
    usort($mediaRecords, array($this, 'sortRecordsCallback'));
    
    // build media tags
    $medias = array();
    foreach($mediaRecords as $index => $mediaRecord)
    {
      $mediaTag = $this->getHelper()->media($mediaRecord);
  
      if (!empty($vars['width']) || !empty($vars['height']))
      {
        $mediaTag->size(dmArray::get($vars, 'width'), dmArray::get($vars, 'height'));
      }
  
      $mediaTag->method($vars['method']);
  
      if ($vars['method'] === 'fit')
      {
        $mediaTag->background($vars['background']);
      }
      
      if ($alt = $vars['medias'][$index]['alt'])
      {
        $mediaTag->alt($this->__($alt));
      }
      
      if ($quality = dmArray::get($vars, 'quality'))
      {
        $mediaTag->quality($quality);
      }
      
      $medias[] = array(
        'tag'   => $mediaTag,
        'link'  => $vars['medias'][$index]['link']
      );
    }
  
    // replace media configuration by media tags
    $vars['medias'] = $medias;
    
    return $vars;
  }
  
  protected function sortRecordsCallback(DmMedia $a, DmMedia $b)
  {
    return $this->mediaPositions[$a->get('id')] > $this->mediaPositions[$b->get('id')];
  }
  
  protected function getMediaQuery($mediaIds)
  {
    return dmDb::query('DmMedia m')
    ->leftJoin('m.Folder f')
    ->whereIn('m.id', $mediaIds);
  }

  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    $vars = $this->getViewVars();
    $helper = $this->getHelper();
    $jsVar = array('options' => array(), 'medias' => array());
    
    $html = $helper->open('script', array('type' => 'text/javascript',
        'language' => 'javascript'));
    
    $jsVar['options']['autostart'] = $vars['auto_start'];
    $jsVar['options']['animation'] = $vars['animation'];
    $jsVar['options']['delay']     = dmArray::get($vars, 'delay', 3);
    $jsVar['options']['duration']     = dmArray::get($vars, 'duration', 3);
    foreach($vars['medias'] as $media)
    {
      $jsVar['medias'][] = $media['tag']->getSrc() ;
    }
    
    
    $html .= '
      var dmWidgetGalleryBackgroundPlugin = ' . str_replace(array('\/', '"image"'), array('/', 'image'), json_encode($jsVar)) . ';';
    
    $html .= $helper->close('script');

    if ($vars['show_controls']) {    
      $html .= '<div id="controls">
			<div class="pause">pause</div>
			<div class="play">play</div>
			<div class="prev">prev</div>
			<div class="next">next</div>
			<div class="counter"></div>
			<div class="bw">gray scale</div>
		</div>
		<div id="thumbs"></div>
     ';
    }
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  protected function doRenderForIndex()
  {
    $alts = array();
    foreach($this->compiledVars['medias'] as $media)
    {
      if (!empty($media['alt']))
      {
        $alts[] = $media['alt'];
      }
    }
    
    return implode(', ', $alts);
  }
  
}
