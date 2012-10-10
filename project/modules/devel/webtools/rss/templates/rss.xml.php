<?php echo '<?xml version="1.0" encoding="iso-8859-1"?>'; ?>
   <rss version="2.0">
       <channel>
           <title><?php echo $title; ?></title>
           <description><![CDATA[<?php echo $desc; ?>]]></description>
           <link><?php echo $url; ?></link>
           <?php foreach($items as $item){ ?>
           <item>
               <title><?php echo urlencode($item->rss_title); ?></title>
               <description><![CDATA[<?php echo $item->rss_desc; ?>]]></description>
               <pubDate><?php echo $item->rss_pubdate; ?></pubDate>
               <link><?php echo $item->rss_link; ?></link>
           </item>
           <?php } ?>
       </channel>
   </rss>