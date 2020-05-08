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

Looks very similar to Grandpa so far.