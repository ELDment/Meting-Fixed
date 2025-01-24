<p align="center">

<img alt="Author" src="https://img.shields.io/badge/Author-ELDment-blue.svg?style=flat-square" height="20"/>
<img alt="Star" src="https://img.shields.io/github/stars/ELDment/CSharp-Music-API?style=for-the-badge&logo=github" height="20">

</p>

 > 🍰 .NET强大的音乐API框架，支持网易云音乐、腾讯QQ音乐
 >
 > ✨ Wow, such a powerful .NET music API framework, Support Netease Music, Tencent(QQ) Music.

## Introduction

A powerful music API framework to accelerate your development🎡
 + **Elegant** - Easy to use, a standardized format for all music platforms.
 + **Powerful** - Support various music platforms, including Tencent, Netease, KuGou, Kuwo.
 + **Free** - Under GPLv3 license.

## Requirement
Newtonsoft.Json extension.
> **Note:** Net Core 8.0 for development.

## Building
Download the source files to your project folder.

Then you can build your library:

```powershell
dotnet clean

dotnet build -c Release
```

> **Note:** Requires [Newtonsoft.Json](https://github.com/JamesNK/Newtonsoft.Json) extension in order to work.


## Quick Start
```csharp
//using MusicAPI;

public class Program {
	static async Task Main(string[] args) {
		string? buffer;
		MusicAPI.MusicAPI api = new MusicAPI.MusicAPI("NetEase");
		Dictionary<string, string> keyValues = new Dictionary<string, string> {
			{ "Cookie", "your cookie" },
		};
		api.Cookie(keyValues);
		
		Console.WriteLine($"搜索歌曲:");
		buffer = await api.Search("secret base ~君がくれたもの~");
		Console.WriteLine($"{buffer}\n\n");

		Console.WriteLine($"解析歌曲:");
		buffer = await api.Url(33911781);
		Console.WriteLine($"{buffer}\n\n");

		Console.WriteLine($"歌曲信息:");
		buffer = await api.Song(33911781);
		Console.WriteLine($"{buffer}\n\n");

		Console.WriteLine($"获取歌词:");
		buffer = await api.Lyric(33911781);
		Console.WriteLine($"{buffer}\n\n");

		return;
	}
}
```

## More usage
 - [special for netease](https://github.com/metowolf/Meting/wiki/special-for-netease)

## Related Projects
 - [metowolf/Meting](https://github.com/metowolf/Meting)
 - [ELDment/Meting-MusicApi-Fixed](https://github.com/ELDment/Meting-MusicApi-Fixed)

## Keywords
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
