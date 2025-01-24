<p align="center">
<img src="https://user-images.githubusercontent.com/2666735/30165599-36623bea-93a6-11e7-8956-1ddf99ce0e6f.png" alt="Meting">
</p>

<p align="center">
<a href="https://github.com/metowolf/Meting">
<img alt="Author" src="https://img.shields.io/badge/Author-METO&ELDment-blue.svg?style=flat-square" height="20"/>
<img alt="Star" src="https://img.shields.io/github/stars/ELDment/Meting-MusicApi-Fixed?style=for-the-badge&logo=github" height="20">
</a>
</p>

 > 🍰 强大的音乐API框架，支持网易云音乐、腾讯QQ音乐、酷狗音乐、酷我音乐。
 >
 > ✨ Wow, such a powerful music API framework. Support Netease Music, Tencent(QQ) Music, Kugou Music, Kuwo Music.

## Introduction
**[[Meting](https://github.com/metowolf/Meting)]** 的最新修复版本 🎉 我**没有迭代本API的打算**，所以我不能保证其的稳定性。
> **[[Meting's](https://github.com/metowolf/Meting)]** latest fixed version 🎉 I **have no intention of iterating this API**, so I cannot guarantee its stability.

A powerful music API framework to accelerate your development🎡
 + **Elegant** - Easy to use, a standardized format for all music platforms.
 + **Lightweight** - A single-file library that's less than 40KB.
 + **Powerful** - Support various music platforms, including Tencent, Netease, KuGou, Kuwo.
 + **Free** - Under MIT license.

## Requirement
PHP 5.4+ and BCMath, Curl, OpenSSL extension installed.
> **Note:** I use PHP 8.0 for development and debugging.

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
 - [Official website](https://github.com/ELDment/Meting-New/discussions)

## Related Projects
 - [metowolf/Meting](https://github.com/metowolf/Meting)
 - [MoePlayer/Hermit-X](https://github.com/MoePlayer/Hermit-X)
 - [MoePlayer/APlayer-Typecho](https://github.com/MoePlayer/APlayer-Typecho)
 - [mengkunsoft/MKOnlineMusicPlayer](https://github.com/mengkunsoft/MKOnlineMusicPlayer)
 - [webjyh/WP-Player](https://github.com/webjyh/WP-Player)
 - [yiyungent/Meting4Net](https://github.com/yiyungent/Meting4Net)
 - [injahow/meting-api](https://github.com/injahow/meting-api)
 - [mPlayer2](https://github.com/dodododooo/mPlayer2)

## 关键词
```
music, API, musicAPI, music API, 音乐, 音乐API, 聚合API, 聚合音乐API, Tencent, QQ, Netease, KuGou, Kuwo, TencentAPI, NeteaseAPI, KuGouAPI, KuwoAPI
Tencent Music, Netease Music, KuGou Music, Kuwo Music, 腾讯音乐, qq音乐, 腾讯QQ音乐, 网易云音乐, 网易音乐, 酷狗音乐, 酷我音乐
Tencent Music API, Netease Music API, KuGou Music API, Kuwo Music API, 腾讯音乐API, qq音乐API, 腾讯QQ音乐API, 网易云音乐API, 网易音乐API, 酷狗音乐API, 酷我音乐API
获取音乐直链API, 下载音乐API, 音乐搜索API, 音乐专辑信息API, 音乐歌曲详情API, 音乐歌曲信息API, 音乐歌单信息API, 音乐歌手作品API, 获取音乐专辑图片API, 获取音乐歌词API
获取网易云音乐直链API, 下载网易云音乐API, 网易云音乐搜索API, 网易云音乐专辑信息API, 网易云音乐歌曲详情API, 网易云音乐歌曲信息API, 网易云音乐歌单信息API, 网易云音乐歌手作品API, 获取网易云音乐专辑图片API, 获取网易云音乐歌词API
获取腾讯QQ音乐直链API, 下载腾讯QQ音乐API, 腾讯QQ音乐搜索API, 腾讯QQ音乐专辑信息API, 腾讯QQ音乐歌曲详情API, 腾讯QQ音乐歌曲信息API, 腾讯QQ音乐歌单信息API, 腾讯QQ音乐歌手作品API, 获取腾讯QQ音乐专辑图片API, 获取腾讯QQ音乐歌词API
获取酷狗音乐直链API, 下载酷狗音乐API, 酷狗音乐搜索API, 酷狗音乐专辑信息API, 酷狗音乐歌曲详情API, 酷狗音乐歌曲信息API, 酷狗音乐歌单信息API, 酷狗音乐歌手作品API, 获取酷狗音乐专辑图片API, 获取酷狗音乐歌词API
获取酷我音乐直链API, 下载酷我音乐API, 酷我音乐搜索API, 酷我音乐专辑信息API, 酷我音乐歌曲详情API, 酷我音乐歌曲信息API, 酷我音乐歌单信息API, 酷我音乐歌手作品API, 获取酷我音乐专辑图片API, 获取酷我音乐歌词API
Get Music Mp3 Url API, Download Music Song API, Music Search API, Get Music Album Info/Data/Content API, Get Music Song Info/Data/Content API, Music Music Info/Data/Content API, Get Music Playlist Info/Data/Content API, Get Music Songer's works API, Get Music Songer Info/Data, Get Music Album Pic/Picture API, Get Music Song/Music lyrics API
Get Netease Music Mp3 Url API, Download Netease Music Song API, Netease Music Search API, Get Netease Music Album Info/Data/Content API, Get Netease Music Song Info/Data/Content API, Netease Music Music Info/Data/Content API, Get Netease Music Playlist Info/Data/Content API, Get Netease Music Songer's works API, Get Netease Music Songer Info/Data, Get Netease Music Album Pic/Picture API, Get Netease Music Song/Music lyrics API
Get Tencent(QQ) Music Mp3 Url API, Download Tencent(QQ) Music Song API, Tencent(QQ) Music Search API, Get Tencent(QQ) Music Album Info/Data/Content API, Get Tencent(QQ) Music Song Info/Data/Content API, Tencent(QQ) Music Music Info/Data/Content API, Get Tencent(QQ) Music Playlist Info/Data/Content API, Get Tencent(QQ) Music Songer's works API, Get Tencent(QQ) Music Songer Info/Data, Get Tencent(QQ) Music Album Pic/Picture API, Get Tencent(QQ) Music Song/Music lyrics API
Get KuGou Music Mp3 Url API, Download KuGou Music Song API, KuGou Music Search API, Get KuGou Music Album Info/Data/Content API, Get KuGou Music Song Info/Data/Content API, KuGou Music Music Info/Data/Content API, Get KuGou Music Playlist Info/Data/Content API, Get KuGou Music Songer's works API, Get KuGou Music Songer Info/Data, Get KuGou Music Album Pic/Picture API, Get KuGou Music Song/Music lyrics API
Get Kuwo Music Mp3 Url API, Download Kuwo Music Song API, Kuwo Music Search API, Get Kuwo Music Album Info/Data/Content API, Get Kuwo Music Song Info/Data/Content API, Kuwo Music Music Info/Data/Content API, Get Kuwo Music Playlist Info/Data/Content API, Get Kuwo Music Songer's works API, Get Kuwo Music Songer Info/Data, Get Kuwo Music Album Pic/Picture API, Get Kuwo Music Song/Music lyrics API
```
