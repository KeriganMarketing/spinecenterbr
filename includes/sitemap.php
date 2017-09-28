<?php 
//ini_set('display_errors', 'On');
$sitemapUrl = $site_url."sitemap.xml";

header('Content-Type: text/xml; charset=UTF-8');

$xml = new DOMDocument( "1.0", "UTF-8" );
$xml->formatOutput = true; 

$xml_schema = $xml->createElement( "urlset" );
$xml_schema->setAttribute( "xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9" );
$xml_schema->setAttribute( "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance" );
$xml_schema->setAttribute( "xsi:schemaLocation", "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" );

$xml->appendChild( $xml_schema );

$mapContents = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="/includes/xml-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\r\n";

	$query = "SELECT controller, dead, pageType, dateModified FROM pageTable WHERE parent is NULL ORDER BY navOrder ASC";
	
	if(!$result = mysql_query($query)){
		print("Could not execute nav query! <br />");
		die(mysql_error());
	}
	
	while($topNav = mysql_fetch_assoc($result)){
		if($topNav['pageType'] == 'blog'){
			$mapContents .= trim('<url>' . "\r\n" .'
	<loc>'.$site_url.$topNav['controller'].'/</loc>' . "\r\n" .'
	<lastmod>'.date('Y-m-d', strtotime($topNav['dateModified'])).'</lastmod>' . "\r\n" .'
	<changefreq>daily</changefreq>' . "\r\n" .'
</url>') . "\r\n";
			
			$xml_BlogTopUrl = $xml->createElement( 'url' );
			$xml_BlogTopLoc = $xml->createElement( 'loc', $site_url.$topNav['controller'].'/' );
			$xml_BlogTopMod = $xml->createElement( 'lastmod', date('Y-m-d', strtotime($topNav['dateModified'])) );
			$xml_BlogTopChange = $xml->createElement( 'changefreq', 'daily' );
			
			$xml_BlogTopUrl->appendChild( $xml_BlogTopLoc );
			$xml_BlogTopUrl->appendChild( $xml_BlogTopMod );
			$xml_BlogTopUrl->appendChild( $xml_BlogTopChange );
			$xml_schema->appendChild( $xml_BlogTopUrl );
			
			$blogQuery = "SELECT controller, date FROM blogPosts WHERE active='1' ORDER BY date DESC";
			if($blogResult = mysql_query($blogQuery)){
				while($blog = mysql_fetch_assoc($blogResult)){
					if(strpos($subNav['controller'],'#') === false){
						$mapContents .= trim('<url>' . "\r\n" .'
	<loc>'.$site_url.$topNav['controller'].'/view/'.$blog['controller'].'/</loc>' . "\r\n" .'
	<lastmod>'.date('Y-m-d', strtotime($blog['date'])).'</lastmod>' . "\r\n" .'
	<changefreq>daily</changefreq>' . "\r\n" .'
</url>') . "\r\n";
					
					$xml_blogUrl = $xml->createElement( 'url' );
					$xml_blogLoc = $xml->createElement( 'loc', $site_url.$topNav['controller'].'/view/'.$blog['controller'].'/' );
					$xml_blogMod = $xml->createElement( 'lastmod', date('Y-m-d', strtotime($blog['date'])) );
					$xml_blogChange = $xml->createElement( 'changefreq', 'daily' );
					
					$xml_blogUrl->appendChild( $xml_blogLoc );
					$xml_blogUrl->appendChild( $xml_blogMod );
					$xml_blogUrl->appendChild( $xml_blogChange );
					$xml_schema->appendChild( $xml_blogUrl );
			
					}
				}
			}
		}else{
			if(strpos($topNav['controller'],'#') === false){
				if($topNav['dead'] == 0){
				$mapContents .= trim('<url>' . "\r\n" .'
	<loc>'.$site_url.$topNav['controller'].'/</loc>' . "\r\n" .'
	<lastmod>'.date('Y-m-d', strtotime($topNav['dateModified'])).'</lastmod>' . "\r\n" .'
	<changefreq>daily</changefreq>' . "\r\n" .'
</url>') . "\r\n";
			
			$xml_topUrl = $xml->createElement( 'url' );
			$xml_topLoc = $xml->createElement( 'loc', $site_url.$topNav['controller'].'/' );
			$xml_topMod = $xml->createElement( 'lastmod', date('Y-m-d', strtotime($topNav['dateModified'])) );
			$xml_topChange = $xml->createElement( 'changefreq', 'daily' );
					
			$xml_topUrl->appendChild( $xml_topLoc );
			$xml_topUrl->appendChild( $xml_topMod );
			$xml_topUrl->appendChild( $xml_topChange );
			$xml_schema->appendChild( $xml_topUrl );
			
			}
			}
	
			$subQuery = "SELECT controller, dead, dateModified FROM pageTable WHERE parent='".$topNav['controller']."' ORDER BY navOrder ASC";
			if($subResult = mysql_query($subQuery)){
				while($subNav = mysql_fetch_assoc($subResult)){
					if(strpos($subNav['controller'],'#') === false){
						if($subNav['dead'] == 0) {
							$mapContents .= trim('<url>' . "\r\n" .'
	<loc>'.$site_url.$topNav['controller'].'/'.$subNav['controller'].'/</loc>' . "\r\n" .'
	<lastmod>'.date('Y-m-d', strtotime($subNav['dateModified'])).'</lastmod>' . "\r\n" .'
	<changefreq>daily</changefreq>' . "\r\n" .'
</url>') . "\r\n";

					$xml_subUrl = $xml->createElement( 'url' );
					$xml_subLoc = $xml->createElement( 'loc', $site_url.$topNav['controller'].'/'.$subNav['controller'].'/' );
					$xml_subMod = $xml->createElement( 'lastmod', date('Y-m-d', strtotime($subNav['dateModified'])) );
					$xml_subChange = $xml->createElement( 'changefreq', 'daily' );
					
					$xml_subUrl->appendChild( $xml_subLoc );
					$xml_subUrl->appendChild( $xml_subMod );
					$xml_subUrl->appendChild( $xml_subChange );
					$xml_schema->appendChild( $xml_subUrl );
			
					}
					}
				}
			}
		}
	}
$mapContents .= '</urlset>'; 
$xml->save("sitemap.xml");
print_r ($mapContents);

//echo 'Wrote: ' . $xml->save("sitemap.xml") . ' bytes'; // Wrote: 72 bytes

$nodes = $xml->getElementsByTagName('loc');

// cUrl handler to ping the Sitemap submission URLs for Search Engines…
function myCurl($url,array $get = NULL, array $options = array()){
	
	$defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => FALSE,
        CURLOPT_TIMEOUT => 4
    ); 
	
	$ch = curl_init($url);
	curl_setopt($ch, ($options + $defaults));
	curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	//return "<!--".$httpCode."-->";
}

if ($nodes->length > 0) {
	//re-save
	$xml->save('/sitemap.xml');
	//echo 'saved.';
	
	
	
	/*//Google
	$url = "http://www.google.com/webmasters/sitemaps/ping?sitemap=".$sitemapUrl;
	$returnCode = myCurl($url);
	//echo "<!--<p>Google Sitemaps has been pinged (return code: $returnCode).</p>-->";
	
	//Bing / MSN / Yahoo!
	$url = "http://www.bing.com/ping?sitemap=".$sitemapUrl;
	$returnCode = myCurl($url);
	//echo "<!--<p>Bing / MSN Sitemaps has been pinged (return code: $returnCode).</p>-->";
	
	//ASK
	$url = "http://submissions.ask.com/ping?sitemap=".$sitemapUrl;
	$returnCode = myCurl($url);
	//echo "<!--<p>ASK.com Sitemaps has been pinged (return code: $returnCode).</p>-->";*/


}
?>