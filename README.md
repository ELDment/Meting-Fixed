<p align="center">
 <img src="https://user-images.githubusercontent.com/2666735/30165599-36623bea-93a6-11e7-8956-1ddf99ce0e6f.png" alt="Meting">
</p>

<p align="center">
 <img alt="Author" src="https://img.shields.io/badge/Author-METO&ELDment-blue.svg?style=flat-square" height="20"/>
 <img alt="Star" src="https://img.shields.io/github/stars/ELDment/Meting-MusicApi-Fixed?style=for-the-badge&logo=github" height="20">
</p>

> 🍰 强大的音乐API框架，支持网易云音乐、QQ音乐、酷狗音乐、酷我音乐
>
> ✨ A powerful music API framework supporting NetEase Cloud Music, QQ Music, Kugou Music, and Kuwo Music

## Introduction
本项目是基于 Meting 的稳定修复版本，**不再计划进行功能迭代**，长期稳定性无法保证。建议用于短期项目或学习用途。

A powerful music API framework to accelerate your development🎡
 + **Elegant** - Easy to use, a standardized format for all music platforms.
 + **Lightweight** - A single-file library that's less than 40KB.
 + **Powerful** - Support various music platforms, including Tencent, Netease, KuGou, Kuwo.
 + **Free** - Under MIT license.

## Requirement
PHP 5.4+ and BCMath, Curl, OpenSSL extension installed.

## Installation
Copy the PHP file to your project folder.

Then you can import the class into your application:

```php
use Metowolf\Meting;

$api = new Meting('netease');

$data = $api->format(true)->search('把回忆拼好给你');
```

> **Note:** Meting requires [BCMath](http://php.net/manual/en/book.bc.php), [cURL](http://php.net/manual/en/book.curl.php) and [OpenSSL](http://php.net/manual/en/book.openssl.php) extension in order to work.


## Quick Start
```php
// require 'vendor/autoload.php';
require 'Meting.php';

use Metowolf\Meting;

// Initialize to netease API
$api = new Meting('netease');

// Use custom cookie (option)
// $api->cookie('');

// Get data
$data = $api->format(true)->search('把回忆拼好给你', [
    'page' => 1,
    'limit' => 50
]);

echo $data;
// [{"id":35847388,"name":"Hello","artist":["Adele"],"album":"Hello","pic_id":"1407374890649284","url_id":35847388,"lyric_id":35847388,"source":"netease"},{"id":33211676,"name":"Hello","artist":["OMFG"],"album":"Hello",...

// Parse mp3 link
$data = $api->format(true)->url(35847388);

echo $data;
// {"url":"http:\/\/...","size":4729252,"br":128}
```

## More usage
 - [docs](https://github.com/metowolf/Meting/wiki)
 - [special for netease](https://github.com/metowolf/Meting/wiki/special-for-netease)

## Join the Discussion
 - [discussions](https://github.com/ELDment/Meting-New/discussions)

## Related Projects
 - [metowolf/Meting](https://github.com/metowolf/Meting)
 - [MoePlayer/Hermit-X](https://github.com/MoePlayer/Hermit-X)
 - [MoePlayer/APlayer-Typecho](https://github.com/MoePlayer/APlayer-Typecho)
 - [mengkunsoft/MKOnlineMusicPlayer](https://github.com/mengkunsoft/MKOnlineMusicPlayer)
 - [webjyh/WP-Player](https://github.com/webjyh/WP-Player)
 - [yiyungent/Meting4Net](https://github.com/yiyungent/Meting4Net)
 - [injahow/meting-api](https://github.com/injahow/meting-api)
 - [mPlayer2](https://github.com/dodododooo/mPlayer2)

## Keywords
```
音乐API 聚合音乐API 多平台音乐API 网易云音乐API QQ音乐API 酷狗音乐API 酷我音乐API 音乐搜索API 获取音乐直链API 获取音乐歌词API 获取音乐封面API 获取歌曲详情API 获取专辑信息API 获取歌单信息API 获取歌手信息API 网易云音乐搜索API 网易云音乐获取直链API 网易云音乐获取歌词API 网易云音乐获取封面API 网易云音乐获取歌曲详情API 网易云音乐获取专辑信息API 网易云音乐获取歌单信息API 网易云音乐获取歌手信息API QQ音乐搜索API QQ音乐获取直链API QQ音乐获取歌词API QQ音乐获取封面API QQ音乐获取歌曲详情API QQ音乐获取专辑信息API QQ音乐获取歌单信息API QQ音乐获取歌手信息API 酷狗音乐搜索API 酷狗音乐获取直链API 酷狗音乐获取歌词API 酷狗音乐获取封面API 酷狗音乐获取歌曲详情API 酷狗音乐获取专辑信息API 酷狗音乐获取歌单信息API 酷狗音乐获取歌手信息API 酷我音乐搜索API 酷我音乐获取直链API 酷我音乐获取歌词API 酷我音乐获取封面API 酷我音乐获取歌曲详情API 酷我音乐获取专辑信息API 酷我音乐获取歌单信息API 酷我音乐获取歌手信息API Music API Aggregated Music API Multi-platform Music API Netease Cloud Music API QQ Music API KuGou Music API Kuwo Music API Music Search API Get Music MP3 URL API Get Music Stream URL API Get Music Lyrics API Get Music Cover Art API Get Song Details API Get Album Info API Get Playlist Info API Get Artist Info API Netease Cloud Music Search API Netease Cloud Music MP3 URL API Netease Cloud Music Stream URL API Netease Cloud Music Lyrics API Netease Cloud Music Cover Art API Netease Cloud Music Song Details API Netease Cloud Music Album Info API Netease Cloud Music Playlist Info API Netease Cloud Music Artist Info API QQ Music Search API QQ Music MP3 URL API QQ Music Stream URL API QQ Music Lyrics API QQ Music Cover Art API QQ Music Song Details API QQ Music Album Info API QQ Music Playlist Info API QQ Music Artist Info API KuGou Music Search API KuGou Music MP3 URL API KuGou Music Stream URL API KuGou Music Lyrics API KuGou Music Cover Art API KuGou Music Song Details API KuGou Music Album Info API KuGou Music Playlist Info API KuGou Music Artist Info API Kuwo Music Search API Kuwo Music MP3 URL API Kuwo Music Stream URL API Kuwo Music Lyrics API Kuwo Music Cover Art API Kuwo Music Song Details API Kuwo Music Album Info API Kuwo Music Playlist Info API Kuwo Music Artist Info API
```
