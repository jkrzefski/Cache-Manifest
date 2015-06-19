<?php

/**
 * The MIT License (MIT)
 * 
 * Copyright (c) 2015 jkrzefski
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
**/

class Manifest
{
	private $cache = array();
	
	private $network = array();
	
	private $fallback = array();
	
	private $hash = "";
	
	public function __construct()
	{
		header('Content-Type: text/cache-manifest');
		echo "CACHE MANIFEST\n";
	}
	
	public function addToCache($filename, $fallback = FALSE)
	{
		array_push($this->cache, $filename);
		if ($fallback) {
			$this->addToFallback($filename, $filename);
		}
		$this->addToHash($filename);
		return $this;
	}
	
	public function addToNetwork($network)
	{
		array_push($this->network, $network);
		$this->addToHash($network);
		return $this;
	}
	
	public function addToFallback($online, $fallback)
	{
		array_push($this->fallback, array("online" => $online, "fallback" => $fallback));
		$this->addToHash($online);
		$this->addToHash($fallback);
		return $this;
	}
	
	private function addToHash($string)
	{
		if (is_file($string)) {
			$hash = md5_file($string);
		}
		else {
			$hash = md5($string);
		}
		$this->hash .= $hash;
	}
	
	private function printCache()
	{
		echo "CACHE:\n";
		foreach ($this->cache as $file) {
			if (is_file($file)) {
				// Encode URL or it will break
				echo str_replace(' ', '%20', $file) . "\n";
			}
		}
	}
	
	private function printNetwork()
	{
		echo "NETWORK:\n";
		foreach ($this->network as $network) {
			echo $network . "\n";
		}
	}
	
	private function printFallback()
	{
		echo "FALLBACK:\n";
		foreach ($this->fallback as $fallback) {
			echo $fallback["online"] . " " . $fallback["fallback"] . "\n";
		}
	}
	
	private function printHash()
	{
		echo "# Hash: " . md5($this->hash);
	}
	
	public function printManifest()
	{
		$this->printCache();
		$this->printNetwork();
		$this->printFallback();
		$this->printHash();
	}
}

$cache = new Manifest;

$cache->addToCache("_resources/css/base.css")
	->addToCache("_resources/css/font-awesome.min.css")
	->addToCache("_resources/fonts/fontawesome-webfont.eot", TRUE)
	->addToCache("_resources/fonts/fontawesome-webfont.svg", TRUE)
	->addToCache("_resources/fonts/fontawesome-webfont.ttf", TRUE)
	->addToCache("_resources/fonts/fontawesome-webfont.woff", TRUE)
	->addToCache("_resources/fonts/fontawesome-webfont.woff2", TRUE)
	->addToCache("_resources/fonts/FontAwesome.otf", TRUE)
	->addToCache("_resources/js/jquery-2.1.4.min.js")
	->addToCache("_resources/js/jquery.mobile.custom.min.js")
	->addToCache("_resources/js/localstoragedb.min.js")
	->addToCache("_resources/js/compatibility.js")
	->addToCache("_resources/js/engine.js")
	->addToNetwork("*");

$cache->printManifest();
