# Bashed 10.10.10.68 - Linux

 - __User__:
 - __Root__:

## Initial Attempt, 05/06/2020

### Enumeration

`nmap -oA bashed -A -p- 10.10.10.68`

```shell
Nmap scan report for 10.10.10.68
Host is up (0.032s latency).
Not shown: 65534 closed ports
PORT   STATE SERVICE VERSION
80/tcp open  http    Apache httpd 2.4.18 ((Ubuntu))
|_http-server-header: Apache/2.4.18 (Ubuntu)
|_http-title: Arrexel's Development Site
```
`gobuster dir -w /usr/share/wordlists/dirb/common.txt -u http://10.10.10.68 -t 40`

```shell
===============================================================
Gobuster v3.0.1
by OJ Reeves (@TheColonial) & Christian Mehlmauer (@_FireFart_)
===============================================================
[+] Url:            http://10.10.10.68
[+] Threads:        40
[+] Wordlist:       /usr/share/wordlists/dirb/common.txt
[+] Status codes:   200,204,301,302,307,401,403
[+] User Agent:     gobuster/3.0.1
[+] Timeout:        10s
===============================================================
/css (Status: 301)
/dev (Status: 301)
/.htaccess (Status: 403)
/.htpasswd (Status: 403)
/.hta (Status: 403)
/fonts (Status: 301)
/images (Status: 301)
/index.html (Status: 200)
/js (Status: 301)
/php (Status: 301)
/server-status (Status: 403)
/uploads (Status: 301)
```
