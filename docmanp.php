<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mosConfig_absolute_path . '/administrator/components/com_docmanrss/class.docmanp.php');

$no_html = 1;

$feed = $Config;
unset($feed['max_items']);

$database->setQuery('SELECT id FROM #__components WHERE link=\'option=com_docman\' LIMIT 1');
$itemid = $database->loadResult();
//echo 'SELECT id, dmname, dmdescription FROM #__docman WHERE published=1 ORDER BY dmdate_published DESC LIMIT '.(!empty($Config['max_items'])?(int)$Config['max_items']:10);
$database->setQuery('SELECT id, dmname, dmdescription, dmdate_published FROM #__docman WHERE published=1 ORDER BY dmdate_published DESC LIMIT '.(!empty($Config['max_items'])?(int)$Config['max_items']:10));
$data = $database->loadObjectList();

if(sizeof($data) == 0){
	die();
}

header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="'.(!empty($Config['encoding'])?$Config['encoding']:'UTF-8').'"?>'."\n";
echo '<rss version="2.0"> '."\n";
echo '<channel>'."\n";

foreach($feed as $tag=>$value)
{
	echo '<'.$tag.'>'.$value.'</'.$tag.'>'."\n";
}

foreach($data as $item){

	$link = /*$mosConfig_live_site.'/*/'index.php?option=com_docman&amp;task=doc_details&amp;Itemid='.$itemid.'&amp;gid='.$item->id;
	$link = ((strcasecmp( substr( $link, 0, 5), 'http:' ) == 0)?$link:sefRelToAbs($link));
	echo '<item>'."\n";
	echo '<link>'.$link.'</link>'."\n";
	echo '<guid isPermaLink="true">'.$link.'</guid>'."\n";
	echo '<lastBuildDate>'.date('D, d M Y g:i:s O', strtotime($item->dmdate_published)).'</lastBuildDate>';
	echo '<title>'.$item->dmname.'</title>'."\n";
	echo '<description><![CDATA['.substr(str_replace('/images/', $mosConfig_live_site.'/images/', preg_replace( "#{dm_orders}(.*?){/dm_orders}#s", '', $item->dmdescription )),0,200).'...]]></description>'."\n";
	//echo '<comments>'.$mosConfig_live_site.'/index.php?option=com_docman&amp;task=doc_details&amp;Itemid='.$itemid.'&amp;gid='.$item->id.'#comments</comments>'."\n";
	echo '</item>'."\n";
}

echo '</channel>'."\n";
echo '</rss>';

?>
