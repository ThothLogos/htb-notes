# Devel 10.10.10.5 - Windows

# 00 - Initial Attempt, 4/30/20

`nmap -sC -sV -oA devel 10.10.10.5`

```shell
# Nmap 7.80 scan initiated Thu Apr 30 22:43:34 2020 as: nmap -sC -sV -oA devel 10.10.10.5
Nmap scan report for 10.10.10.5
Host is up (0.033s latency).
Not shown: 998 filtered ports
PORT   STATE SERVICE VERSION
21/tcp open  ftp     Microsoft ftpd
| ftp-anon: Anonymous FTP login allowed (FTP code 230)
| 03-18-17  02:06AM       <DIR>          aspnet_client
| 03-17-17  05:37PM                  689 iisstart.htm
|_03-17-17  05:37PM               184946 welcome.png
| ftp-syst:
|_  SYST: Windows_NT
80/tcp open  http    Microsoft IIS httpd 7.5
| http-methods:
|_  Potentially risky methods: TRACE
|_http-server-header: Microsoft-IIS/7.5
|_http-title: IIS7
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows

Service detection performed. Please report any incorrect results at https://nmap.org/submit/ .
# Nmap done at Thu Apr 30 22:43:45 2020 -- 1 IP address (1 host up) scanned in 11.65 seconds
```

#### Initial Ideas

Open Ports:

 - 21 FTP
  - Anonymous Login allowed? (code 230)
 - 80 IIS v7.5
  - FTP gives clue to iisstart.htm and welcome.png possibly hosted
  - Potentially risky method "TRACE" ?

Goals:

 - Look into anonymous FTP login situation
 - WTF is HTTP `TRACE` method?
 - Any vulns for IIS 7.5?

#### FTP Port 21

 - FTP worked for user `anonymous` and password `none@na.com` or blank

```shell
thoth@kali:~/Projects/htb-notes/machines/Devel/dumps$ ftp 10.10.10.5
Connected to 10.10.10.5.
220 Microsoft FTP Service
Name (10.10.10.5:thoth): anonymous
331 Anonymous access allowed, send identity (e-mail name) as password.
Password:
230 User logged in.
Remote system type is Windows_NT.
ftp>
```

 - Within the FTP found the following file structure:

```shell
aspnet_client/
  ..
  system_web/
    ..
    2_0_50727/
welcome.png
iisstart.htm
```

Directories seem to lead nowhere, can't see any files. Poked around some FTP commands, didn't find anything. Might be more to explore here with my lack of FTP command knowledge. Can we probe for more user accounts? I am able to upload files with `send`... hmm. Perhaps look into IIS file structure and if I can work toward RCE via this?

#### Web Server Microsoft IIS on Port 80

This is version 7.5 of IIS, which was released with Windows 7 and Windows Server 2008 R2. Can't find exact lifetime dates.

 - HTTP `TRACE` led me to [this Cross-Site Tracing paper](https://www.cgisecurity.com/whitehat-mirror/WH-WhitePaper_XST_ebook.pdf)

 - Attempting the basic echo they illustrate seems to fail. Perhaps I don't know how to issue telnet properly. Tried to curl it, that didn't work either.

```shell
thoth@kali:~/Projects/htb-notes/machines/Devel/dumps$ telnet 10.10.10.5 80
Trying 10.10.10.5...
Connected to 10.10.10.5.
Escape character is '^]'.
TRACE / HTTP/1.1
Host: foo.bar
X-Header: test
^]
HTTP/1.1 400 Bad Request
Content-Type: text/html; charset=us-ascii
Server: Microsoft-HTTPAPI/2.0
Date: Mon, 04 May 2020 11:43:37 GMT
Connection: close
Content-Length: 339
```

```shell
thoth@kali:~/Projects/htb-notes/machines/Devel/dumps$ curl -v -X TRACE -H "Host: foo.bar" -H "X-Header: test" 10.10.10.5
*   Trying 10.10.10.5:80...
* TCP_NODELAY set
* Connected to 10.10.10.5 (10.10.10.5) port 80 (#0)
> TRACE / HTTP/1.1
> Host: foo.bar
> User-Agent: curl/7.68.0
> Accept: */*
> X-Header: test
>
* Mark bundle as not supporting multiuse
< HTTP/1.1 501 Not Implemented
< Content-Type: text/html
< Server: Microsoft-IIS/7.5
< X-Powered-By: ASP.NET
< Date: Mon, 04 May 2020 11:41:57 GMT
< Content-Length: 1508

<html removed>
```