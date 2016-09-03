<?php

namespace Chriscubos\Assets;

class Assets {

	protected static $css="<link href='{{url}}' rel='stylesheet'/>\n";
	protected static $js="<script src='{{url}}'></script>\n";

	public static function js($url) {
		return self::processToAssets($urls);
	}

	public static function css($url) {
		return self::processToAssets($urls);
	}

	public static function packages($packages, $filter=null) {
		$urls = [];
		$processed = '';
		$x = ($filter!=null)?["$filter"=>true]:[];
		if (is_array($packages)) {
			foreach ($packages as $package) {
				$url = config('jspack.package.'.$package);
				$processed .= self::processUrls($url, "/packages/$package", $x);
			}
		} else {
			$urls = config('jspack.package.'.$packages);
			return self::processUrls($urls, "/packages/$packages", $x);
		}
		return $processed;
	}

	public static function theme($urls, $side='back') {
		return self::processUrls($urls, "/themes/".config('themes.'.$side), ['has_sub'=>true]);
	}

	public static function bootstrap($more_urls=null) {
		$urls = array_merge($more_urls,[
			"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js",
			"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css",
			"font-awesome.min.css",
			"https://code.jquery.com/jquery-3.1.0.min.js",
		]);
		return self::processToAssets($urls);
	}

	public static function collection($collection, $filter=null) {
		$urls = [];
		$x = ($filter!=null)?["$filter"=>true]:[];
		if (is_array($collection)) {
			foreach ($collection as $collected) {
				$urls = array_merge($urls, config('themes.collection.'.$collected));
			}	
		} else {
			$urls = config('themes.collection.'.$collection);
		}

		return self::processToAssets($urls, $x);
	}

	public static function pubilc($assets, $filter=null) {
		$urls = [];
		$x = ($filter!=null)?["$filter"=>true]:[];
		if (is_array($assets)) {
			foreach ($assets as $asset) {
				$urls = array_merge($urls, $asset);
			}	
		} else {
			$urls = $assets;
		}

		return self::processUrls($urls, '/', $x);
	}



	public static function processToAssets($urls, $x=[]) {
		$x = array_merge(['has_sub'=>true], $x);
		return self::processUrls($urls, '/assets', $x);
	}

	public static function processUrls($urls, $path, $x=[]) {
		$asset="";
		foreach ($urls as $url) {
			if ((isset($x['js']) and str_is('*.css', $url)) || 
				(isset($x['css']) and str_is('*.js', $url))) {
				// wala
			} else {
				if (str_is("http*", $url)==0) {
					if (!str_is('*.css', $url) &&  !str_is('*.js', $url)) {
						$filter = isset($x['js'])?$filter='js':(isset($x['css'])?$filter='css':null);
						$asset .= self::packages($url, $filter);
					} else {
						$sub = isset($x['has_sub'])?(str_is('*.css', $url) ? '/css':'/js'):'';
						$url = "$path$sub/$url";
						$asset .= self::makeHtml($url);
					}
				} else {
					$asset .= self::makeHtml($url);
				}
			}
		}
		return $asset;
	}

	public static function makeHtml($url) {
		return str_replace('{{url}}', $url, (str_is('*.css', $url) ? self::$css : self::$js));
	}

}